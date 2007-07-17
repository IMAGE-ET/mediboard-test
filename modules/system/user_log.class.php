<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

/**
 * The CUserLog Class
 */
class CUserLog extends CMbMetaObject {
  // DB Table key
  var $user_log_id = null;

  // DB Fields
  var $user_id      = null;
  var $date         = null;
  var $type         = null;
  var $fields       = null;

  // Object References
  var $_fields = null;
  var $_ref_user = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    return $spec;
  }

  function CUserLog() {
    $this->CMbObject("user_log", "user_log_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
  	$specs["object_id"]    = "notNull ref class|CMbObject meta|object_class unlink";
    $specs["user_id"]      = "notNull ref class|CUser";
    $specs["date"]         = "notNull dateTime";
    $specs["type"]         = "notNull enum list|create|store|delete";
    $specs["fields"]       = "text";
    return $specs;
  }
  
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->fields) {
      $this->_fields = split(" ", $this->fields);
    }
  }
  
  function updateDBFields() {
    parent::updateDBFields();
    if ($this->_fields) {
      $this->fields = join($this->_fields, " ");
    }
  }
  
  /**
   * Initializes id and class for given CMbObject
   */
  function setObject($mbObject) {
    assert(is_a($mbObject, "CMbObject"));
    $this->object_id = $mbObject->_id;
    $this->object_class = get_class($mbObject);
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->_ref_user = new CUser;
    $this->_ref_user->load($this->user_id);
  }
  
}
?>