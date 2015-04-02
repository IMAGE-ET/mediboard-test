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
class CAccessLog extends CMbObject {
  public $accesslog_id;

  // DB Fields
  public $module_action_id;
  public $period;
  public $hits;
  public $duration;
  //public $session_wait;
  //public $session_read;
  public $processus;
  public $processor;
  public $request;
  public $nb_requests;
  public $peak_memory;
  public $size;
  public $errors;
  public $warnings;
  public $notices;
  public $aggregate;
  public $bot;

  // Form fields
  public $_average_hits = 0;
  public $_average_duration = 0;
  //public $_average_session_wait = 0;
  //public $_average_session_read = 0;
  public $_average_processus = 0;
  public $_average_processor = 0;
  public $_average_request = 0;
  public $_average_nb_requests = 0;
  public $_average_peak_memory = 0;
  public $_average_size = 0;
  public $_average_errors = 0;
  public $_average_warnings = 0;
  public $_average_notices = 0;

  public $_module;
  public $_action;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec           = parent::getSpec();
    $spec->loggable = false;
    $spec->table    = 'access_log';
    $spec->key      = 'accesslog_id';

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props                     = parent::getProps();
    $props["module_action_id"] = "ref class|CModuleAction notNull";
    $props["period"]           = "dateTime notNull";
    $props["hits"]             = "num pos notNull";
    $props["duration"]         = "float notNull";
    //$props["session_wait"]     = "float";
    //$props["session_read"]     = "float";
    $props["request"]          = "float notNull";
    $props["nb_requests"]      = "num";
    $props["processus"]        = "float";
    $props["processor"]        = "float";
    $props["peak_memory"]      = "num min|0";
    $props["size"]             = "num min|0";
    $props["errors"]           = "num min|0";
    $props["warnings"]         = "num min|0";
    $props["notices"]          = "num min|0";
    $props["aggregate"]        = "num min|0 default|10";
    $props["bot"]              = "enum list|0|1 default|0";

    $props["_average_duration"]    = "num min|0";
    $props["_average_request"]     = "num min|0";
    $props["_average_peak_memory"] = "num min|0";
    $props["_average_nb_requests"] = "num min|0";

    $props["_module"] = "str";
    $props["_action"] = "str";

    return $props;
  }

  /**
   * Fast store using ON DUPLICATE KEY UPDATE MySQL feature
   *
   * @return string Store-like message
   */
  function fastStore() {
    $fields = $this->getPlainFields();
    unset($fields[$this->_spec->key]);

    $columns = array();
    $inserts = array();
    $updates = array();

    foreach ($fields as $_name => $_value) {
      $columns[] = "$_name";
      $inserts[] = "'$_value'";
      if (!in_array($_name, array("module_action_id", "period", "aggregate", "bot"))) {
        $updates[] = "$_name = $_name + '$_value'";
      }
    }

    $columns = implode(",", $columns);
    $inserts = implode(",", $inserts);
    $updates = implode(",", $updates);

    $query = "INSERT INTO access_log ($columns) 
      VALUES ($inserts)
      ON DUPLICATE KEY UPDATE $updates";

    $ds = $this->_spec->ds;
    if (!$ds->exec($query)) {
      return $ds->error();
    }

    return null;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->hits) {
      $this->_average_duration     = $this->duration     / $this->hits;
      //$this->_average_session_wait = $this->session_wait / $this->hits;
      //$this->_average_session_read = $this->session_read / $this->hits;
      $this->_average_duration     = $this->duration     / $this->hits;
      $this->_average_processus    = $this->processus    / $this->hits;
      $this->_average_processor    = $this->processor    / $this->hits;
      $this->_average_request      = $this->request      / $this->hits;
      $this->_average_nb_requests  = $this->nb_requests  / $this->hits;
      $this->_average_peak_memory  = $this->peak_memory  / $this->hits;
      $this->_average_errors       = $this->errors       / $this->hits;
      $this->_average_warnings     = $this->warnings     / $this->hits;
      $this->_average_notices      = $this->notices      / $this->hits;
    }
    // If time period == 1 hour
    $this->_average_hits = $this->hits / 60; // hits per min
    $this->_average_size = $this->size / 3600; // size per sec
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
   * @todo A partir de cette méthode, il faut compléter les champs de session
   *
   * @return CAccessLog[]
   */
  static function loadAggregation($start, $end, $groupmod = 0, $module = null, $human_bot = null) {
    $al    = new static;
    $table = $al->_spec->table;

    switch ($groupmod) {
      case 2:
        $query = "SELECT
            $table.`accesslog_id`,
            $table.`module_action_id`,
            SUM($table.`hits`)         AS hits,
            SUM($table.`size`)         AS size,
            SUM($table.`duration`)     AS duration,
            SUM($table.`processus`)    AS processus,
            SUM($table.`processor`)    AS processor,
            SUM($table.`request`)      AS request,
            SUM($table.`nb_requests`)  AS nb_requests,
            SUM($table.`peak_memory`)  AS peak_memory,
            SUM($table.`errors`)       AS errors,
            SUM($table.`warnings`)     AS warnings,
            SUM($table.`notices`)      AS notices,
            0 AS grouping
          FROM $table
          WHERE $table.`period` BETWEEN '$start' AND '$end'";
        break;

      case 0:
      case 1:
      $query = "SELECT
          $table.`accesslog_id`,
          $table.`module_action_id`,
          `module_action`.`module`   AS _module,
          `module_action`.`action`   AS _action,
          SUM($table.`hits`)         AS hits,
          SUM($table.`size`)         AS size,
          SUM($table.`duration`)     AS duration,
          SUM($table.`processus`)    AS processus,
          SUM($table.`processor`)    AS processor,
          SUM($table.`request`)      AS request,
          SUM($table.`nb_requests`)  AS nb_requests,
          SUM($table.`peak_memory`)  AS peak_memory,
          SUM($table.`errors`)       AS errors,
          SUM($table.`warnings`)     AS warnings,
          SUM($table.`notices`)      AS notices,
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

    return $al->loadQueryList($query);
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
    $al    = new static;
    $table = $al->_spec->table;

    // Convert date format from PHP to MySQL
    $period_format = str_replace("%M", "%i", $period_format);

    $query = "SELECT
        `accesslog_id`,
        `period`,
        SUM(hits)             AS hits,
        SUM(size)             AS size,
        SUM(duration)         AS duration,
        SUM(processus)        AS processus,
        SUM(processor)        AS processor,
        SUM(request)          AS request,
        SUM(nb_requests)      AS nb_requests,
        SUM(peak_memory)      AS peak_memory,
        SUM(errors)           AS errors,
        SUM(warnings)         AS warnings,
        SUM(notices)          AS notices,
      DATE_FORMAT(`period`, '$period_format') AS `gperiod`
      FROM $table
      WHERE `period` BETWEEN '$start' AND '$end'";

    // 2 means for both of them
    if ($human_bot === '0' || $human_bot === '1') {
      $query .= "\nAND bot = '$human_bot' ";
    }

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

    $query .= "\nGROUP BY `gperiod`";

    return $al->loadQueryList($query);
  }

  /**
   * Compute Flotr graph
   *
   * @param string  $module_name
   * @param string  $action_name
   * @param integer $startx
   * @param integer $endx
   * @param string  $interval
   * @param array   $left
   * @param array   $right
   * @param bool    $human_bot
   *
   * @return array
   */
  static function graphAccessLog($module_name, $action_name, $startx, $endx, $interval = 'one-day', $left, $right, $human_bot = null) {
    $al    = new static;

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

    $logs = $al::loadPeriodAggregation($startx, $endx, $period_format, $module_name, $action_name, $human_bot);

    $duration    = array();
    $processus   = array();
    $processor   = array();
    $request     = array();
    $nb_requests = array();
    $peak_memory = array();
    $errors      = array();
    $warnings    = array();
    $notices     = array();

    $hits = array();
    $size = array();

    $datetime_by_index = array();

    $errors_total = 0;
    foreach ($datax as $x) {
      // Needed
      $duration[$x[0]]    = array($x[0], 0);
      $processus[$x[0]]   = array($x[0], 0);
      $processor[$x[0]]   = array($x[0], 0);
      $request[$x[0]]     = array($x[0], 0);
      $nb_requests[$x[0]] = array($x[0], 0);
      $peak_memory[$x[0]] = array($x[0], 0);
      $errors[$x[0]]      = array($x[0], 0);
      $warnings[$x[0]]    = array($x[0], 0);
      $notices[$x[0]]     = array($x[0], 0);

      $hits[$x[0]] = array($x[0], 0);
      $size[$x[0]] = array($x[0], 0);


      foreach ($logs as $log) {
        if ($x[1] == CMbDT::format($log->period, $period_format)) {
          $duration[$x[0]]    = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'duration'});
          $processus[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'processus'});
          $processor[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'processor'});
          $request[$x[0]]     = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'request'});
          $nb_requests[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'nb_requests'});
          $peak_memory[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'peak_memory'});
          $errors[$x[0]]      = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'errors'});
          $warnings[$x[0]]    = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'warnings'});
          $notices[$x[0]]     = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '') . 'notices'});
          $errors_total += $log->_average_errors + $log->_average_warnings + $log->_average_notices;

          $hits[$x[0]] = array($x[0], $log->{($right[1] == 'mean' ? '_average_' : '') . 'hits'} / ($right[1] == 'mean' ? $hours : 1));
          $size[$x[0]] = array($x[0], $log->{($right[1] == 'mean' ? '_average_' : '') . 'size'} / ($right[1] == 'mean' ? $hours : 1));

          $datetime_by_index[$x[0]] = $log->period;
        }
      }
    }

    // Removing some xaxis ticks
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
        'title'           => utf8_encode(($left[0] == 'request_time' ? 'Temps de réponse' :
            ($left[0] == 'cpu_time' ? 'Temps CPU' :
              ($left[0] == 'errors' ? 'Erreurs' :
                ($left[0] == 'peak_memory' ? 'Mémoire' : 'Requetes SQL')))) .
          ($left[1] == 'mean' ? ' (par hit)' : '')),
        'autoscaleMargin' => 1
      ),
      'y2axis'      => array(
        'min'             => 0,
        'title'           => utf8_encode(($right[0] == 'hits' ? 'Hits' : 'Bande passante') .
          ($right[1] == 'mean' ? (($right[0] == 'hits' ? ' (par minute)' : ' (octets/s)')) : '')),
        'autoscaleMargin' => 1
      ),
      'grid'        => array(
        'verticalLines' => false
      ),
      /*'mouse' => array(
        'track' => true,
        'relative' => true
      ),*/
      'HtmlText'    => false,
      'spreadsheet' => array(
        'show'             => true,
        'csvFileSeparator' => ';',
        'decimalSeparator' => ','
      )
    );

    $series = array();

    // Right axis (before in order the lines to be on top)
    if ($right[0] == 'hits') {
      $series[] = array(
        'label' => 'Hits',
        'data'  => $hits,
        'bars'  => array(
          'show' => true
        ),
        'yaxis' => 2
      );
    }
    else {
      $series[] = array(
        'label' => 'Bande passante',
        'data'  => $size,
        'bars'  => array(
          'show' => true
        ),
        'yaxis' => 2
      );
    }

    // Left axis
    if ($left[0] == 'request_time') {
      $series[] = array(
        'label' => 'Page (s)',
        'data'  => $duration,
        'lines' => array(
          'show' => true
        ),
      );

      $series[] = array(
        'label' => 'DB (s)',
        'data'  => $request,
        'lines' => array(
          'show' => true
        ),
      );
    }
    elseif ($left[0] == 'cpu_time') {
      $series[] = array(
        'label' => 'Page (s)',
        'data'  => $duration,
        'lines' => array(
          'show' => true
        ),
      );

      $series[] = array(
        'label' => 'Process (s)',
        'data'  => $processus,
        'lines' => array(
          'show' => true
        ),
      );

      $series[] = array(
        'label' => 'CPU (s)',
        'data'  => $processor,
        'lines' => array(
          'show' => true
        ),
      );

      $series[] = array(
        'label' => 'DB (s)',
        'data'  => $request,
        'lines' => array(
          'show' => true
        ),
      );
    }
    elseif ($left[0] == 'errors') {
      if ($errors_total == 0) {
        $options['yaxis']['max'] = 1;
      }

      $series[] = array(
        'label' => 'Errors',
        'data'  => $errors,
        'color' => 'red',
        'lines' => array(
          'show' => true
        ),
      );

      $series[] = array(
        'label' => 'Warnings',
        'data'  => $warnings,
        'color' => 'orange',
        'lines' => array(
          'show' => true
        ),
      );

      $series[] = array(
        'label' => 'Notices',
        'data'  => $notices,
        'color' => 'yellow',
        'lines' => array(
          'show' => true
        ),
      );
    }
    elseif ($left[0] == 'memory_peak') {
      $series[] = array(
        'label' => 'Pic (byte)',
        'data'  => $peak_memory,
        'lines' => array(
          'show' => true
        ),
      );
    }
    else {
      $series[] = array(
        'label' => 'Requetes SQL',
        'data'  => $nb_requests,
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
    $al    = new static;
    $table = $al->_spec->table;

    $ds = $al->getDS();
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

    // Take the 1 month period to aggregate
    $oldest_to = min(CMbDT::transform("+ 1 MONTH", $oldest_from, "%Y-%m-%d 00:00:00"), $last_month);

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

    // Récupération des IDs de journaux à agréger à l'heure
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
        $query = "INSERT INTO `access_log_archive` (
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

    // Récupération des IDs de journaux à agréger à la journée
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
        $query = "INSERT INTO `access_log_archive` (
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

    $IDs_to_aggregate = array_merge($year_IDs_to_aggregate, $month_IDs_to_aggregate);

    $msg = "%d access logs inserted from %s to %s";
    CAppUI::setMsg($msg, UI_MSG_OK, count($IDs_to_aggregate), CMbDT::date($oldest_from), CMbDT::date($oldest_to));
  }
}
