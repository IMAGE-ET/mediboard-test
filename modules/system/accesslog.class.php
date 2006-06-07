<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */

require_once($AppUI->getSystemClass('mbobject'));

class CAccessLog extends CMbObject {
  var $accesslog_id = null;
  
  // DB Fields
  var $module = null;
  var $action = null;
  var $period = null;
  var $hits = null;
  var $duration = null;
  var $request = null;
  
  // Form fields
  var $_average_duration = null;
  var $_average_request = null;
  
  function CAccessLog () {
    $this->CMbObject("access_log", "accesslog_id");
  }
  
  function updateFormFields() {
    if ($this->hits) {
      $this->_average_duration = $this->duration / $this->hits;
      $this->_average_request = $this->request / $this->hits;
    }
  }
}



?>