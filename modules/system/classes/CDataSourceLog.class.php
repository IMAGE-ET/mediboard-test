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
class CDataSourceLog extends CModuleActionLog {
  public $datasourcelog_id;

  // log unique logical key fields (signature)
  public $module_action_id;
  public $datasource;
  public $period;
  public $aggregate;
  public $bot;

  // Log data fields
  public $requests;
  public $duration;

  // Object Reference
  public $_module;
  public $_action;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec           = parent::getSpec();
    $spec->loggable = false;
    $spec->table    = 'datasource_log';
    $spec->key      = 'datasourcelog_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                     = parent::getProps();
    $props["datasource"]       = "str notNull";
    $props["period"]           = "dateTime notNull";
    $props["requests"]         = "num";
    $props["duration"]         = "float";
    $props["aggregate"]        = "num min|0 default|10";
    $props["bot"]              = "enum list|0|1 default|0";
    $props['module_action_id'] = "ref notNull class|CModuleAction";

    $props["_module"] = "str";
    $props["_action"] = "str";

    return $props;
  }

  /**
   * @see parent::getSignatureFields();
   */
  static function getSignatureFields() {
    return array("module_action_id", "period", "datasource", "aggregate", "bot");
  }

  /**
   * Load aggregated statistics
   *
   * @param string $start     Start date
   * @param string $end       End date
   * @param int    $groupmod  Grouping mode
   * @param null   $module    Module name
   * @param string $human_bot Human/bot filter
   *
   * @return CAccessLog[]
   */
  static function loadAggregation($start, $end, $groupmod = 0, $module = null, $human_bot = null) {
    $dl    = new static;
    $table = $dl->_spec->table;

    switch ($groupmod) {
      case 2:
        $query = "SELECT
            `datasourcelog_id`,
            $table.`module_action_id`,
            `period`,
            0 AS grouping
          FROM $table
          WHERE $table.`period` BETWEEN '$start' AND '$end'";
        break;

      case 0:
      case 1:
        $query = "SELECT
          `datasourcelog_id`,
          $table.`module_action_id`,
          `module_action`.`module` AS _module,
          `module_action`.`action` AS _action,
          `period`,
          0 AS grouping
        FROM $table
        LEFT JOIN `module_action` ON $table.`module_action_id` = `module_action`.`module_action_id`
        WHERE $table.`period` BETWEEN '$start' AND '$end'";
    }

    // 2 means for both of them
    if ($human_bot === '0' || $human_bot === '1') {
      $query .= "\nAND $table.`bot` = '$human_bot' ";
    }

    if ($module && !$groupmod) {
      $query .= "\nAND `module_action`.`module` = '$module' ";
    }

    switch ($groupmod) {
      case 2:
        $query .= "GROUP BY grouping ";
        break;
      case 1:
        $query .= "GROUP BY `module_action`.`module` ORDER BY `module_action`.`module` ";
        break;
      case 0:
        $query .= "GROUP BY `module_action`.`module`, `module_action`.`action` ORDER BY `module_action`.`module`, `module_action`.`action` ";
        break;
    }

    return $dl->loadQueryList($query);
  }

  /**
   * Build aggregated stats for a period
   *
   * @param string $start         Start date time
   * @param string $end           End date time
   * @param string $period_format Period format
   * @param string $module_name   Module name
   * @param string $action_name   Action name
   * @param string $human_bot     Human/bot filter
   *
   * @return CAccessLog[]
   */
  static function loadPeriodAggregation($start, $end, $period_format, $module_name, $action_name, $human_bot = null) {
    $dl    = new static;
    $table = $dl->_spec->table;

    // Convert date format from PHP to MySQL
    $period_format = str_replace("%M", "%i", $period_format);

    $query = "SELECT
        $table.`datasourcelog_id`,
        $table.`period`,
        $table.`datasource`,
        SUM($table.`requests`) AS requests,
        SUM($table.`duration`) AS duration,
        DATE_FORMAT($table.`period`, '$period_format') AS `gperiod`
      FROM $table
      WHERE $table.`period` BETWEEN '$start' AND '$end'";

    if ($module_name) {
      $actions = CModuleAction::getActions($module_name);
      if ($action_name) {
        $action_id = $actions[$action_name];
        $query .= "\nAND `module_action_id` = '$action_id'";
      }
      else {
        $query .= "\nAND `module_action_id` " . CSQLDataSource::prepareIn(array_values($actions));
      }
    }

    // 2 means for both of them
    if ($human_bot === '0' || $human_bot === '1') {
      $query .= "\nAND bot = '$human_bot' ";
    }

    $query .= "\nGROUP BY `gperiod`, $table.`datasource` ORDER BY `period`";

    return $dl->loadQueryList($query);
  }

  /**
   * Compute Flotr graph
   *
   * @param string  $module_name
   * @param string  $action_name
   * @param integer $startx
   * @param integer $endx
   * @param string  $interval
   * @param bool    $human_bot
   *
   * @return array
   */
  static function graphDataSourceLog($module_name, $action_name, $startx, $endx, $interval = 'one-day', $human_bot = null) {
    $dl = new static;

    switch ($interval) {
      default:
      case "one-day":
        $step          = "+10 MINUTES";
        $period_format = "%H:%M";
        $hours         = 1 / 6;
        $ticks_modulo  = 4;
        break;

      case "one-week":
        $step          = "+1 HOUR";
        $period_format = "%a %d %Hh";
        $hours         = 1;
        $ticks_modulo  = 8;
        break;

      case "height-weeks":
        $step          = "+1 DAY";
        $period_format = "%d/%m";
        $hours         = 24;
        $ticks_modulo  = 4;
        break;

      case "one-year":
        $step          = "+1 WEEK";
        $period_format = "%Y S%U";
        $hours         = 24 * 7;
        $ticks_modulo  = 4;
        break;

      case "four-years":
        $step          = "+1 MONTH";
        $period_format = "%m/%Y";
        $hours         = 24 * 30;
        $ticks_modulo  = 4;
        break;

      case "twenty-years":
        $step          = "+1 YEAR";
        $period_format = "%Y";
        $hours         = 24 * 30 * 12;
        $ticks_modulo  = 1;
        break;
    }

    $datax = array();
    $i     = 0;
    for ($d = $startx; $d <= $endx; $d = CMbDT::dateTime($step, $d)) {
      $datax[] = array($i, CMbDT::format($d, $period_format));
      $i++;
    }

    /** @var CDataSourceLog[] $logs */
    $logs = $dl::loadPeriodAggregation($startx, $endx, $period_format, $module_name, $action_name, $human_bot);

    $duration = array();
    $requests = array();

    $datetime_by_index = array();

    foreach ($datax as $x) {
      // Needed
      foreach ($logs as $log) {
        $duration[$log->datasource][$x[0]] = array($x[0], 0);
        $requests[$log->datasource][$x[0]] = array($x[0], 0);
      }

      foreach ($logs as $log) {
        if ($x[1] == CMbDT::format($log->period, $period_format)) {
          $duration[$log->datasource][$x[0]] = array($x[0], $log->duration);
          $requests[$log->datasource][$x[0]] = array($x[0], $log->requests);

          $datetime_by_index[$x[0]] = $log->period;
        }
      }
    }

    foreach ($datax as $i => &$x) {
      if ($i % $ticks_modulo) {
        $x[1] = '';
      }
    }

    $title = '';
    if ($module_name) {
      $title .= CAppUI::tr("module-$module_name-court");
    }
    if ($action_name) {
      $title .= " - $action_name";
    }

    $subtitle = CMbDT::format($endx, CAppUI::conf("longdate"));

    $options = array(
      'title'       => utf8_encode($title),
      'subtitle'    => utf8_encode($subtitle),
      'xaxis'       => array(
        'labelsAngle' => 45,
        'ticks'       => $datax,
      ),
      'yaxis'       => array(
        'min'             => 0,
        'title'           => utf8_encode('Temps de réponse'),
        'autoscaleMargin' => 1
      ),
      'y2axis'      => array(
        'min'             => 0,
        'title'           => utf8_encode('Requêtes'),
        'autoscaleMargin' => 1
      ),
      'grid'        => array(
        'verticalLines' => false
      ),
      'HtmlText'    => false,
      'spreadsheet' => array(
        'show'             => true,
        'csvFileSeparator' => ';',
        'decimalSeparator' => ','
      )
    );

    $series = array();

    // Right axis (before in order the lines to be on top)
    foreach ($requests as $datasource => $_requests) {
      $series[] = array(
        'label' => "$datasource-requetes",
        'data'  => $_requests,
        'bars'  => array(
          'show'    => true,
          'stacked' => true
        ),
        'yaxis' => 2
      );
    }

    // Left axis
    foreach ($duration as $datasource => $_duration) {
      $series[] = array(
        'label' => "$datasource-temps moyen",
        'data'  => $_duration,
        'lines' => array(
          'show' => true
        ),
      );
    }

    return array('series' => $series, 'options' => $options, 'module' => $module_name, 'datetime_by_index' => $datetime_by_index);
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
    $dl    = new static;
    $table = $dl->_spec->table;

    $ds = $dl->getDS();
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

    // Take the 1 month period to aggregate
    $oldest_to = min(CMbDT::transform("+ 1 MONTH", $oldest_from, "%Y-%m-%d 00:00:00"), $last_month);

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

    // Récupération des IDs de journaux à agréger à l'heure
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
              GROUP BY `module_action_id`, `datasource`, date_format(`period`, '%Y-%m-%d %H:00:00'), `bot`";

    $month_IDs_to_aggregate = $ds->loadList($query);

    if ($month_IDs_to_aggregate) {
      foreach ($month_IDs_to_aggregate as $_aggregate) {
        $query = "INSERT INTO `datasource_log_archive` (
                    `module_action_id`,
                    `datasource`,
                    `period`,
                    `aggregate`,
                    `bot`,
                    `requests`,
                    `duration`
                  )
                  SELECT
                    `module_action_id`,
                    `datasource`,
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
          CAppUI::setMsg("Failed to insert aggregated datasource logs", UI_MSG_ERROR);

          return;
        }

        // Delete previous logs
        $query = "DELETE
                  FROM $table
                  WHERE `datasourcelog_id` IN (" . $_aggregate['ids'] . ")";

        $ds->exec($query);
      }
    }

    // Récupération des IDs de journaux à agréger à la journée
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
              GROUP BY `module_action_id`, `datasource`, date_format(`period`, '%Y-%m-%d 00:00:00'), `bot`";

    $year_IDs_to_aggregate = $ds->loadList($query);

    if ($year_IDs_to_aggregate) {
      foreach ($year_IDs_to_aggregate as $_aggregate) {
        $query = "INSERT INTO `datasource_log_archive` (
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
          CAppUI::setMsg("Failed to insert aggregated datasource logs", UI_MSG_ERROR);

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
