<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Data source resource usage log
 */
class CDataSourceLogArchive extends CDataSourceLog {
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = 'datasource_log_archive';

    return $spec;
  }

  /**
   * Logs aggregation
   *
   * @param int  $std_agg
   * @param int  $avg_agg
   * @param int  $sup_agg
   * @param bool $dry_run
   */
  static function aggregate($std_agg = 10, $avg_agg = 60, $sup_agg = 1440, $dry_run = true) {
    $dla   = new self;
    $table = $dla->_spec->table;

    $ds = $dla->getDS();
    $ds->exec("SET SESSION group_concat_max_len = 100000;");

    $last_month = CMbDT::transform("- 1 MONTH", CMbDT::dateTime(), "%Y-%m-%d 00:00:00");
    $last_year  = CMbDT::transform("- 1 YEAR", CMbDT::dateTime(), "%Y-%m-%d 00:00:00");

    // Get the oldest log to aggregate
    $query = "SELECT `period`
              FROM $table
              WHERE `period` <= '$last_month'
                AND `aggregate` <= IF (`period` <= '$last_year', '$avg_agg', '$std_agg')
              ORDER BY `period` LIMIT 1;";

    $oldest_from = $ds->loadResult($query);

    if (!$oldest_from) {
      CAppUI::stepAjax("No log to aggregate", UI_MSG_OK);

      return;
    }

    // Take the 6 months period to aggregate
    $oldest_to = min(CMbDT::transform("+ 6 MONTHS", $oldest_from, "%Y-%m-%d 00:00:00"), $last_month);

    // Dry run mode, just compute the number of logs to aggregate
    if ($dry_run) {
      // Récupération des IDs de journaux à supprimer
      $query = "SELECT count(`datasourcelog_id`) AS count
                FROM $table
                WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
                   AND `aggregate` <= IF (`period` <= '$last_year', '$avg_agg', '$std_agg');";

      $count = $ds->loadResult($query);

      $msg = "%d datasource logs to aggregate from %s to %s";
      CAppUI::stepAjax($msg, UI_MSG_OK, $count, CMbDT::date($oldest_from), CMbDT::date($oldest_to));

      return;
    }

    // Récupération des IDs de journaux à supprimer
    $query = "SELECT
                CAST(GROUP_CONCAT(`datasourcelog_id` SEPARATOR ',') AS CHAR) AS ids,
                `module_action_id`,
                `datasource`,
                `period`,
                `bot`
              FROM $table
              WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
                AND `period` <= '$last_year'
                AND `aggregate` < '$sup_agg'
              GROUP BY `module_action_id`, `datasource`, DATE_FORMAT(`period`, '%Y-%m-%d 00:00:00'), `bot`";

    $year_IDs_to_aggregate = $ds->loadList($query);

    if ($year_IDs_to_aggregate) {
      foreach ($year_IDs_to_aggregate as $_aggregate) {
        $query = "INSERT INTO $table (
                    `datasource`,
                    `module_action_id`,
                    `period`,
                    `aggregate`,
                    `bot`,
                    `requests`,
                    `duration`
                  )
                  SELECT
                    `datasource`,
                    `module_action_id`,
                    date_format(`period`, '%Y-%m-%d 00:00:00'),
                    '$sup_agg',
                    `bot`,
                    @requests := SUM(`requests`),
                    @duration := SUM(`duration`)
                  FROM $table
                  WHERE `datasourcelog_id` IN (" . $_aggregate['ids'] . ")
                  GROUP BY `module_action_id`, `datasource`, DATE_FORMAT(`period`, '%Y-%m-%d 00:00:00'), `bot`
                  ON DUPLICATE KEY UPDATE
                    `requests` = `requests` + @requests,
                    `duration` = `duration` + @duration";

        if (!$ds->exec($query)) {
          trigger_error("Failed to insert aggregated datasource logs", E_USER_ERROR);

          return;
        }

        // Delete previous logs
        $query = "DELETE
                  FROM $table
                  WHERE `datasourcelog_id` IN (" . $_aggregate['ids'] . ")";

        $ds->exec($query);
      }
    }

    $query = "SELECT
                CAST(GROUP_CONCAT(`datasourcelog_id` SEPARATOR ',') AS CHAR) AS ids,
                `module_action_id`,
                `datasource`,
                `period`,
                `bot`
              FROM $table
              WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
                AND `period` <= '$last_month'
                AND `period`  > '$last_year'
                AND `aggregate` < '$avg_agg'
              GROUP BY `module_action_id`, `datasource`, DATE_FORMAT(`period`, '%Y-%m-%d %H:00:00'), `bot`";

    $month_IDs_to_aggregate = $ds->loadList($query);

    if ($month_IDs_to_aggregate) {
      foreach ($month_IDs_to_aggregate as $_aggregate) {
        $query = "INSERT INTO $table (
                    `datasource`,
                    `module_action_id`,
                    `period`,
                    `aggregate`,
                    `bot`,
                    `requests`,
                    `duration`
                  )
                  SELECT
                    `datasource`,
                    `module_action_id`,
                    date_format(`period`, '%Y-%m-%d %H:00:00'),
                    '$avg_agg',
                    `bot`,
                    @requests := SUM(`requests`),
                    @duration := SUM(`duration`)
                  FROM $table
                  WHERE `datasourcelog_id` IN (" . $_aggregate['ids'] . ")
                  GROUP BY `module_action_id`, `datasource`, DATE_FORMAT(`period`, '%Y-%m-%d %H:00:00'), `bot`
                  ON DUPLICATE KEY UPDATE
                    `requests` = `requests` + @requests,
                    `duration` = `duration` + @duration";

        if (!$ds->exec($query)) {
          trigger_error("Failed to insert aggregated datasource logs", E_USER_ERROR);

          return;
        }

        // Delete previous logs
        $query = "DELETE
                  FROM $table
                  WHERE `datasourcelog_id` IN (" . $_aggregate['ids'] . ")";

        $ds->exec($query);
      }
    }

    $IDs_to_aggregate = array_merge($year_IDs_to_aggregate, $month_IDs_to_aggregate);

    $msg = "%d datasource logs inserted from %s to %s";
    CAppUI::stepAjax($msg, UI_MSG_OK, count($IDs_to_aggregate), CMbDT::date($oldest_from), CMbDT::date($oldest_to));
  }
}
