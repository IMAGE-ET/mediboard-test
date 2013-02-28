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

class CDataSourceLog extends CMbObject {
  // DB Table Key
  public $datasourcelog_id;
  
  // DB Fields
  public $datasource;
  public $requests;
  public $duration;
  
  // Object Reference
  public $accesslog_id;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table    = 'datasource_log';
    $spec->key      = 'datasourcelog_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["datasource"]   = "str notNull";
    $props["requests"]     = "num";
    $props["duration"]     = "float";
    $props['accesslog_id'] = "ref notNull class|CAccessLog";

    return $props;
  }
  
  /**
   * Fast store using ON DUPLICATE KEY UPDATE MySQL feature
   * @return string Store-like message
   */
  function fastStore() {
    $columns = array();
    $inserts = array();
    $updates = array();

    $fields = $this->getPlainFields();
    unset($fields[$this->_spec->key]);
    foreach ($fields as $_name => $_value) {
      $columns[] = "$_name";
      $inserts[] = "'$_value'";

      if (!in_array($_name, array("datasource", "accesslog_id"))) {
        $updates[] = "$_name = $_name + '$_value'";
      }
    }
    
    $columns = implode(",", $columns);
    $inserts = implode(",", $inserts);
    $updates = implode(",", $updates);
    
    $query = "INSERT INTO datasource_log ($columns) 
      VALUES ($inserts)
      ON DUPLICATE KEY UPDATE $updates";
    $ds = $this->_spec->ds;
    
    if (!$ds->exec($query)) {
      return $ds->error();
    }
  }
  
  /*
  static function loadAgregation($start, $end, $groupmod = 0, $module = null) {
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
   
    $log = new self;
    return $log->loadQueryList($query);
  }
  */
  
  /*
  static function loadPeriodAggregation($start, $end, $period_format, $module_name, $action_name) {
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
    
    $log = new self;
    return $log->loadQueryList($query);
  }
  */
}
