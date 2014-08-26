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
 * Access Log
 */
class CAccessLogArchive extends CAccessLog {
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec        = parent::getSpec();
    $spec->table = 'access_log_archive';

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
    $ala   = new self;
    $table = $ala->_spec->table;

    $ds = $ala->getDS();
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
      CAppUI::setMsg("No log to aggregate", UI_MSG_OK);

      return;
    }

    // Take the 6 months period to aggregate
    $oldest_to = min(CMbDT::transform("+ 6 MONTHS", $oldest_from, "%Y-%m-%d 00:00:00"), $last_month);

    // Dry run mode, just compute the number of logs to aggregate
    if ($dry_run) {
      // Récupération des IDs de journaux à supprimer
      $query = "SELECT count(`accesslog_id`) AS count
                FROM $table
                WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
                   AND `aggregate` <= IF (`period` <= '$last_year', '$avg_agg', '$std_agg');";

      $count = $ds->loadResult($query);

      $msg = "%d access logs to aggregate from %s to %s";
      CAppUI::setMsg($msg, UI_MSG_OK, $count, CMbDT::date($oldest_from), CMbDT::date($oldest_to));

      return;
    }

    // Récupération des IDs de journaux à supprimer
    $query = "SELECT
                CAST(GROUP_CONCAT(`accesslog_id` SEPARATOR ',') AS CHAR) AS ids,
                `module_action_id`,
                `period`,
                `bot`
              FROM $table
              WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
                AND `period` <= '$last_year'
                AND `aggregate` < '$sup_agg'
              GROUP BY `module_action_id`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`";

    $year_IDs_to_aggregate = $ds->loadList($query);

    if ($year_IDs_to_aggregate) {
      foreach ($year_IDs_to_aggregate as $_aggregate) {
        $query = "INSERT INTO $table (
                    `module_action_id`,
                    `period`,
                    `aggregate`,
                    `bot`,
                    `hits`,
                    `duration`,
                    `request`,
                    `nb_requests`,
                    `size`,
                    `errors`,
                    `warnings`,
                    `notices`,
                    `processus`,
                    `processor`,
                    `peak_memory`
                  )
                  SELECT
                    `module_action_id`,
                    date_format(`period`, '%Y-%m-%d 00:00:00'),
                    '$sup_agg',
                    `bot`,
                    @hits        := SUM(`hits`),
                    @duration    := SUM(`duration`),
                    @request     := SUM(`request`),
                    @nb_requests := SUM(`nb_requests`),
                    @size        := SUM(`size`),
                    @errors      := SUM(`errors`),
                    @warnings    := SUM(`warnings`),
                    @notices     := SUM(`notices`),
                    @processus   := SUM(`processus`),
                    @processor   := SUM(`processor`),
                    @peak_memory := SUM(`peak_memory`)
                  FROM $table
                  WHERE `accesslog_id` IN (" . $_aggregate['ids'] . ")
                  GROUP BY `module_action_id`, DATE_FORMAT(`period`, '%Y-%m-%d 00:00:00'), `bot`
                  ON DUPLICATE KEY UPDATE
                    `hits`        = `hits`        + @hits,
                    `duration`    = `duration`    + @duration,
                    `request`     = `request`     + @request,
                    `nb_requests` = `nb_requests` + @nb_requests,
                    `size`        = `size`        + @size,
                    `errors`      = `errors`      + @errors,
                    `warnings`    = `warnings`    + @warnings,
                    `notices`     = `notices`     + @notices,
                    `processus`   = `processus`   + @processus,
                    `processor`   = `processor`   + @processor,
                    `peak_memory` = `peak_memory` + @peak_memory";

        if (!$ds->exec($query)) {
          CAppUI::setMsg("Failed to insert aggregated access logs", UI_MSG_ERROR);

          return;
        }

        // Delete previous logs
        $query = "DELETE
                  FROM $table
                  WHERE `accesslog_id` IN (" . $_aggregate['ids'] . ")";

        $ds->exec($query);
      }
    }

    $query = "SELECT
                CAST(GROUP_CONCAT(`accesslog_id` SEPARATOR ',') AS CHAR) AS ids,
                `module_action_id`,
                `period`,
                `bot`
              FROM $table
              WHERE `period` BETWEEN '$oldest_from' AND '$oldest_to'
                AND `period` <= '$last_month'
                AND `period`  > '$last_year'
                AND `aggregate` < '$avg_agg'
              GROUP BY `module_action_id`, date_format(`period`, '%Y-%m-%d %H:00:00'), `bot`";

    $month_IDs_to_aggregate = $ds->loadList($query);

    if ($month_IDs_to_aggregate) {
      foreach ($month_IDs_to_aggregate as $_aggregate) {
        $query = "INSERT INTO $table (
                    `module_action_id`,
                    `period`,
                    `aggregate`,
                    `bot`,
                    `hits`,
                    `duration`,
                    `request`,
                    `nb_requests`,
                    `size`,
                    `errors`,
                    `warnings`,
                    `notices`,
                    `processus`,
                    `processor`,
                    `peak_memory`
                  )
                  SELECT
                    `module_action_id`,
                    date_format(`period`, '%Y-%m-%d %H:00:00'),
                    '$avg_agg',
                    `bot`,
                    @hits        := SUM(`hits`),
                    @duration    := SUM(`duration`),
                    @request     := SUM(`request`),
                    @nb_requests := SUM(`nb_requests`),
                    @size        := SUM(`size`),
                    @errors      := SUM(`errors`),
                    @warnings    := SUM(`warnings`),
                    @notices     := SUM(`notices`),
                    @processus   := SUM(`processus`),
                    @processor   := SUM(`processor`),
                    @peak_memory := SUM(`peak_memory`)
                  FROM $table
                  WHERE `accesslog_id` IN (" . $_aggregate['ids'] . ")
                  GROUP BY `module_action_id`, DATE_FORMAT(`period`, '%Y-%m-%d %H:00:00'), `bot`
                  ON DUPLICATE KEY UPDATE
                    `hits`        = `hits`        + @hits,
                    `duration`    = `duration`    + @duration,
                    `request`     = `request`     + @request,
                    `nb_requests` = `nb_requests` + @nb_requests,
                    `size`        = `size`        + @size,
                    `errors`      = `errors`      + @errors,
                    `warnings`    = `warnings`    + @warnings,
                    `notices`     = `notices`     + @notices,
                    `processus`   = `processus`   + @processus,
                    `processor`   = `processor`   + @processor,
                    `peak_memory` = `peak_memory` + @peak_memory";

        if (!$ds->exec($query)) {
          CAppUI::setMsg("Failed to insert aggregated access logs", UI_MSG_ERROR);

          return;
        }

        // Delete previous logs
        $query = "DELETE
                  FROM $table
                  WHERE `accesslog_id` IN (" . $_aggregate['ids'] . ")";

        $ds->exec($query);
      }
    }

    $IDs_to_aggregate = array_merge($year_IDs_to_aggregate, $month_IDs_to_aggregate);

    $msg = "%d access logs inserted from %s to %s";
    CAppUI::setMsg($msg, UI_MSG_OK, count($IDs_to_aggregate), CMbDT::date($oldest_from), CMbDT::date($oldest_to));
  }
}
