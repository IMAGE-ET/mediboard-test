<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CAccessLog extends CMbObject {
  var $accesslog_id = null;
  
  // DB Fields
  var $module      = null;
  var $action      = null;
  var $period      = null;
  var $hits        = null;
  var $duration    = null;
  var $processus   = null;
  var $processor   = null;
  var $request     = null;
  var $peak_memory = null;
  var $size        = null;
  var $errors      = null;
  var $warnings    = null;
  var $notices     = null;
  
  // Form fields
  var $_average_hits        = 0;
  var $_average_duration    = 0;
  var $_average_processus   = 0;
  var $_average_processor   = 0;
  var $_average_request     = 0;
  var $_average_peak_memory = 0;
  var $_average_size        = 0;
  var $_average_errors      = 0;
  var $_average_warnings    = 0;
  var $_average_notices     = 0;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'access_log';
    $spec->key   = 'accesslog_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["module"]      = "str notNull";
    $specs["action"]      = "str notNull";
    $specs["period"]      = "dateTime notNull";
    $specs["hits"]        = "num pos";
    $specs["duration"]    = "float";
    $specs["processus"]   = "float";
    $specs["processor"]   = "float";
    $specs["request"]     = "float";
    $specs["peak_memory"] = "num min|0";
    $specs["size"]        = "num min|0";
    $specs["errors"]      = "num min|0";
    $specs["warnings"]    = "num min|0";
    $specs["notices"]     = "num min|0";

    $specs["_average_duration"] = "num min|0";
    
    return $specs;
  }
  
  /**
   * Fast store using ON DUPLICATE KEY UPDATE MySQL feature
   * @return string Store-like message
   */
  function fastStore() {
    $fields = $this->getPlainFields();
    unset($fields[$this->_spec->key]);
    foreach ($fields as $_name => $_value) {
      $columns[] = "$_name";
      $inserts[] = "'$_value'";
      if (!in_array($_name, array("module", "action", "period"))) {
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
  }
  
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
  
  static function loadAgregation($start, $end, $groupmod = 0, $module = null, $DBorNotDB = false) {
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
            
    if ($module && !$groupmod) {
      $query .= "AND module = '$module' ";
    }
    
    switch ($groupmod) {
      case 2 :  $query .= "GROUP BY grouping "; break;
      case 1 :  $query .= "GROUP BY module ORDER BY module "; break;
      case 0 :  $query .= "GROUP BY module, action ORDER BY module, action "; break;

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
        
      if ($module && !$groupmod) {
        $query .= "AND module = '$module' ";
      }
      
      switch ($groupmod) {
        case 2 :  $query .= "GROUP BY grouping "; break;
        case 1 :  $query .= "GROUP BY module ORDER BY module "; break;
        case 0 :  $query .= "GROUP BY module, action ORDER BY module, action "; break;
      }
      
      $log = new self;
      return $log->_spec->ds->loadList($query);
    }
    
    $log = new self;
    return $log->loadQueryList($query);
  }
  
  static function loadPeriodAggregation($start, $end, $period_format, $module_name, $action_name, $DBorNotDB = false) {
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
      
    if ($module_name) $query .= "\nAND `module` = '$module_name'";
    if ($action_name) $query .= "\nAND `action` = '$action_name'";
    
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
      USE INDEX (`period`)
      WHERE `access_log`.`period` BETWEEN '$start' AND '$end'
        AND `access_log`.`accesslog_id` = `datasource_log`.`accesslog_id`";
        
      if ($module_name) $query .= "\nAND `access_log`.`module` = '$module_name'";
      if ($action_name) $query .= "\nAND `access_log`.`action` = '$action_name'";
      
      $query .= "\nGROUP BY `gperiod`, `datasource_log`.`datasource` ORDER BY `period`";
      
      $log = new self;
      return $log->_spec->ds->loadList($query);
    }
    
    $log = new self;
    return $log->loadQueryList($query);
  }
}
?>