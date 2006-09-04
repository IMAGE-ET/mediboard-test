<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("admin"));

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
    
    $this->_props["user_id"]      = "ref|notNull";
    $this->_props["date"]         = "dateTime|notNull";
    $this->_props["object_id"]    = "ref|notNull";
    $this->_props["object_class"] = "str|maxLength|25|notNull";
    $this->_props["type"]         = "enum|create|store|delete|notNull";
    $this->_props["fields"]       = "text";
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
  
  function loadRefsFwd() {
    $this->_ref_user = new CUser;
    $this->_ref_user->load($this->user_id);

    $this->_ref_object = new $this->object_class;
    if(!$this->_ref_object->load($this->object_id)) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprimé";
    }
  }
}
?>