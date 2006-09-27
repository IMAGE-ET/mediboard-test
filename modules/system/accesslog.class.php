<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
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
  
  // Form fields
  var $_average_duration = null;
  var $_average_request = null;
  
  function CAccessLog () {
    $this->CMbObject("access_log", "accesslog_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->hits) {
      $this->_average_duration = $this->duration / $this->hits;
      $this->_average_request = $this->request / $this->hits;
    }
  }
  
  function loadAgregation($start, $end, $groupmod = 0, $module = 0) {
    $sql = "SELECT accesslog_id, module, action," .
        "\nSUM(hits) AS hits, SUM(duration) AS duration, SUM(request) AS request," .
        "\n0 AS grouping" .
        "\nFROM access_log" .
        "\nWHERE period BETWEEN '$start' AND '$end'";
    if($module && !$groupmod) {
      $sql .= "\nAND module = '$module'";
    }
    if($groupmod == 2) {
      $sql .= "\nGROUP BY grouping";
    }
    else if($groupmod == 1) {
      $sql .= "\nGROUP BY module" .
          "\nORDER BY module";
    } else {
      $sql .= "\nGROUP BY module, action" .
          "\nORDER BY module, action";
    }
    $list = db_loadObjectList($sql, $this);
    return $list;
  }
}



?>