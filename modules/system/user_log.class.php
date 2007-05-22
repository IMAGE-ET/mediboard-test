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
class CUserLog extends CMbObject {
  // DB Table key
  var $user_log_id = null;

  // DB Fields
  var $user_id      = null;
  var $date         = null;
  var $object_id    = null;
  var $object_class = null;
  var $type         = null;
  var $fields       = null;

  // Object References
  var $_fields = null;
  var $_ref_user = null;
  var $_ref_object = null;

  function CUserLog() {
    $this->CMbObject("user_log", "user_log_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "user_id"      => "notNull ref class|CUser",
      "date"         => "notNull dateTime",
      "object_id"    => "notNull ref class|CMbObject meta|object_class unlink",
      "object_class" => "notNull str maxLength|25",
      "type"         => "notNull enum list|create|store|delete",
      "fields"       => "text"
    );
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables = array ();
    
    return parent::canDelete( $msg, $oid, $tables );
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
    $this->_ref_user = new CUser;
    $this->_ref_user->load($this->user_id);

    $this->_ref_object = new $this->object_class;
    if(!$this->_ref_object->load($this->object_id)) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprim�";
    }
  }
}
?>