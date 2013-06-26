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
  public $module;
  public $action;
  public $period;
  public $hits;
  public $duration;
  public $processus;
  public $processor;
  public $request;
  public $peak_memory;
  public $size;
  public $errors;
  public $warnings;
  public $notices;
  public $aggregate;
  public $bot;
  
  // Form fields
  public $_average_hits        = 0;
  public $_average_duration    = 0;
  public $_average_processus   = 0;
  public $_average_processor   = 0;
  public $_average_request     = 0;
  public $_average_peak_memory = 0;
  public $_average_size        = 0;
  public $_average_errors      = 0;
  public $_average_warnings    = 0;
  public $_average_notices     = 0;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'access_log';
    $spec->key   = 'accesslog_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["module"]      = "str notNull";
    $props["action"]      = "str notNull";
    $props["period"]      = "dateTime notNull";
    $props["hits"]        = "num pos notNull";
    $props["duration"]    = "float notNull";
    $props["request"]     = "float notNull";
    $props["processus"]   = "float";
    $props["processor"]   = "float";
    $props["peak_memory"] = "num min|0";
    $props["size"]        = "num min|0";
    $props["errors"]      = "num min|0";
    $props["warnings"]    = "num min|0";
    $props["notices"]     = "num min|0";
    $props["aggregate"]   = "num min|0 default|10";
    $props["bot"]         = "enum list|0|1 default|0";

    $props["_average_duration"] = "num min|0";
    
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
      if (!in_array($_name, array("module", "action", "period", "aggregate", "bot"))) {
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
      $this->_average_duration    = $this->duration    / $this->hits;
      $this->_average_processus   = $this->processus   / $this->hits;
      $this->_average_processor   = $this->processor   / $this->hits;
      $this->_average_request     = $this->request     / $this->hits;
      $this->_average_peak_memory = $this->peak_memory / $this->hits;
      $this->_average_errors      = $this->errors      / $this->hits;
      $this->_average_warnings    = $this->warnings    / $this->hits;
      $this->_average_notices     = $this->notices     / $this->hits;
    }
    // If time period == 1 hour
    $this->_average_hits = $this->hits / 60;   // hits per min
    $this->_average_size = $this->size / 3600; // size per sec
  }

  /**
   * Load aggregated statistics
   *
   * @param string $start     Start date
   * @param string $end       End date
   * @param int    $groupmod  Grouping mode
   * @param null   $module    Module name
   * @param bool   $DBorNotDB Load database stats or not
   * @param string $human_bot Human/bot filter
   *
   * @return array|CStoredObject[]
   */
  static function loadAgregation($start, $end, $groupmod = 0, $module = null, $DBorNotDB = false, $human_bot = null) {
    $query = "SELECT
        accesslog_id,
        module,
        action,
        SUM(hits)        AS hits,
        SUM(size)        AS size,
        SUM(duration)    AS duration, 
        SUM(duration)    AS processus, 
        SUM(duration)    AS processor, 
        SUM(request)     AS request,
        SUM(peak_memory) AS peak_memory,
        SUM(errors)      AS errors,
        SUM(warnings)    AS warnings,
        SUM(notices)     AS notices,
        0 AS grouping
      FROM access_log
      USE INDEX (period)
      WHERE period BETWEEN '$start' AND '$end' ";

    // 2 means for both of them
    if ($human_bot === '0' || $human_bot === '1') {
      $query .= "\nAND bot = '$human_bot' ";
    }

    if ($module && !$groupmod) {
      $query .= "AND module = '$module' ";
    }
    
    switch ($groupmod) {
      case 2 :
        $query .= "GROUP BY grouping ";
        break;
      case 1 :
        $query .= "GROUP BY module ORDER BY module ";
        break;
      case 0 :
        $query .= "GROUP BY module, action ORDER BY module, action ";
        break;
    }
    
    if ($DBorNotDB) {
      $query = "SELECT 
        `access_log`.`accesslog_id`,
        `access_log`.`module`,
        `access_log`.`action`,
        `access_log`.`period`,
        0 AS grouping
      FROM `access_log`
      USE INDEX (`period`)
      WHERE `access_log`.`period` BETWEEN '$start' AND '$end' ";

      // 2 means for both of them
      if ($human_bot === '0' || $human_bot === '1') {
        $query .= "\nAND bot = '$human_bot' ";
      }

      if ($module && !$groupmod) {
        $query .= "\nAND module = '$module' ";
      }
      
      switch ($groupmod) {
        case 2:
          $query .= "GROUP BY grouping ";
          break;
        case 1:
          $query .= "GROUP BY module ORDER BY module ";
          break;
        case 0:
          $query .= "GROUP BY module, action ORDER BY module, action ";
          break;
      }
      
      $log = new self;
      return $log->_spec->ds->loadList($query);
    }

    $log = new self;
    return $log->loadQueryList($query);
  }

  /**
   * Build aggregated stats for a period
   *
   * @param string $start         Start date time
   * @param string $end           End date time
   * @param string $period_format Period format
   * @param string $module_name   Module name
   * @param string $action_name   Action name
   * @param bool   $DBorNotDB     Include database logs stats
   * @param string $human_bot     Human/bot filter
   *
   * @return array|CStoredObject[]
   */
  static function loadPeriodAggregation($start, $end, $period_format, $module_name, $action_name, $DBorNotDB = false, $human_bot = null) {
    // Convert date format from PHP to MySQL
    $period_format = str_replace("%M", "%i", $period_format);

    $query = "SELECT
        `accesslog_id`,
        `module`,
        `action`,
        `period`,
        AVG(duration/hits)    AS _average_duration,
        AVG(processus/hits)   AS _average_processus,
        AVG(processor/hits)   AS _average_processor,
        AVG(request/hits)     AS _average_request,
        AVG(peak_memory/hits) AS _average_peak_memory,
        SUM(hits)             AS hits,
        SUM(size)             AS size,
        SUM(duration)         AS duration,
        SUM(processus)        AS processus,
        SUM(processor)        AS processor,
        SUM(request)          AS request,
        SUM(peak_memory)      AS peak_memory,
        SUM(errors)           AS errors,
        SUM(warnings)         AS warnings,
        SUM(notices)          AS notices,
      DATE_FORMAT(`period`, '$period_format') AS `gperiod`
      FROM `access_log`
      USE INDEX (period)
      WHERE `period` BETWEEN '$start' AND '$end'";

    // 2 means for both of them
    if ($human_bot === '0' || $human_bot === '1') {
      $query .= "\nAND bot = '$human_bot' ";
    }

    if ($module_name) {
      $query .= "\nAND `module` = '$module_name'";
    }

    if ($action_name) {
      $query .= "\nAND `action` = '$action_name'";
    }
    
    $query .= "\nGROUP BY `gperiod` ORDER BY `period`";
    
    if ($DBorNotDB) {
      $query = "SELECT 
        `access_log`.`accesslog_id`,
        `access_log`.`module`,
        `access_log`.`action`,
        `access_log`.`period`,
        `datasource_log`.`datasource`,
        SUM(`datasource_log`.`requests`) AS requests,
        SUM(`datasource_log`.`duration`) AS duration,
      DATE_FORMAT(`access_log`.`period`, '$period_format') AS `gperiod`
      FROM `datasource_log`, `access_log`
      WHERE `access_log`.`period` BETWEEN '$start' AND '$end'
        AND `access_log`.`accesslog_id` = `datasource_log`.`accesslog_id`";
        
      if ($module_name) {
        $query .= "\nAND `access_log`.`module` = '$module_name'";
      }

      if ($action_name) {
        $query .= "\nAND `access_log`.`action` = '$action_name'";
      }

      // 2 means for both of them
      if ($human_bot === '0' || $human_bot === '1') {
        $query .= "\nAND bot = '$human_bot' ";
      }
      
      $query .= "\nGROUP BY `gperiod`, `datasource_log`.`datasource` ORDER BY `period`";
      
      $log = new self;
      return $log->_spec->ds->loadList($query);
    }

    $log = new self;
    return $log->loadQueryList($query);
  }
}
