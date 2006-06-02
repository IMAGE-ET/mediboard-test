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
  var $module = null;
  var $action = null;
  var $period = null;
  var $hits = null;
  var $duration = null;
  
  // Form fields
  var $_average = null;
  
  function CAccessLog () {
    $this->CMbObject("access_log", "accesslog_id");
  }
  
  function updateFormFields() {
    if ($this->hits) {
      $this->_average = $this->duration / $this->hits;
    }
  }
}



?>