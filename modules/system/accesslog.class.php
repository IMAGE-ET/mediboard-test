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
  var $module   = null;
  var $action   = null;
  var $period   = null;
  var $hits     = null;
  var $duration = null;
  var $request  = null;
  var $size     = null;
  var $errors   = null;
  var $warnings = null;
  var $notices  = null;
  
  // Form fields
  var $_average_duration = null;
  var $_average_request  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'access_log';
    $spec->key   = 'accesslog_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["module"]   = "str notNull";
    $specs["action"]   = "str notNull";
    $specs["period"]   = "dateTime notNull";
    $specs["hits"]     = "num pos";
    $specs["duration"] = "float";
    $specs["request"]  = "float";
    $specs["size"]     = "num min|0";
    $specs["errors"]   = "num min|0";
    $specs["warnings"] = "num min|0";
    $specs["notices"]  = "num min|0";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->hits) {
      $this->_average_duration = $this->duration / $this->hits; // Response time
      $this->_average_request  = $this->request  / $this->hits; // DB time
      $this->_average_errors   = $this->errors   / $this->hits;
      $this->_average_warnings = $this->warnings / $this->hits;
      $this->_average_notices  = $this->notices  / $this->hits;
    }
    // If time period == 1 hour
    $this->_average_hits = $this->hits / 60;
    $this->_average_size = $this->size / 3600;
  }
  
  function loadAgregation($start, $end, $groupmod = 0, $module = 0) {
    $sql = "SELECT accesslog_id, module, action,
              SUM(hits)     AS hits, 
              SUM(size)     AS size, 
              SUM(duration) AS duration, 
              SUM(request)  AS request, 
              SUM(errors)   AS errors, 
              SUM(warnings) AS warnings, 
              SUM(notices)  AS notices, 
              0 AS grouping
            FROM access_log
            WHERE period BETWEEN '$start' AND '$end' ";
            
    if($module && !$groupmod) {
      $sql .= "AND module = '$module' ";
    }
    if($groupmod == 2) {
      $sql .= "GROUP BY grouping ";
    }
    else if($groupmod == 1) {
      $sql .= "GROUP BY module ORDER BY module ";
    } else {
      $sql .= "GROUP BY module, action ORDER BY module, action ";
    }
    return $this->loadQueryList($sql);
  }
}
?>