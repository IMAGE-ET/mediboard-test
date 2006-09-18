<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage admin
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

if(!defined("PERM_DENY")) {
  define("PERM_DENY" , "0");
  define("PERM_READ" , "1");
  define("PERM_EDIT" , "2");
}

/**
 * The CPermObject class
 */
class CPermObject extends CMbObject {
  // DB Table key
  var $perm_object_id = null;
  
  // DB Fields
  var $user_id      = null;
  var $object_id    = null;
  var $object_class = null;
  var $permission   = null;
  
  // References
  var $_ref_db_user   = null;
  var $_ref_db_object = null;
  
  function CPermObject() {
    $this->CMbObject("perm_object", "perm_object_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    $this->_props["user_id"]      = "ref|notNull";
    $this->_props["object_id"]    = "ref|notNull";
    $this->_props["object_class"] = "str|notNull";
    $this->_props["permission"]   = "num|notNull";
  }
  
  function loadRefDBObject() {
    $this->_ref_db_object = new $this->object_class;
    $this->_ref_db_object->load($this->object_id);
  }

  function loadRefDBUser() {
    $this->_ref_db_user = new CUser;
    $this->_ref_db_user->load($this->user_id);
  }
  
  function loadRefsFwd() {
    $this->loadRefDBObject();
    $this->loadRefDBUser();
  }
  
  // Those functions are statics
  
  function getPermObject($object, $permType) {
    global $AppUI, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $user_id = $AppUI->user_id;
    $permObject = new CPermObject;
    $where = array();
    $where["user_id"]      = "= '$user_id'";
    $where["object_id"]    = "= '0'";
    $where["object_class"] = "= '".get_class($object)."'";
    $where["permission"]   = ">= '$permType'";
    if(count($permObject->loadList($where))) {
      $where["object_id"]  = "= '$object->_id'";
      $where["permission"] = "< '$permType'";
      return !count($permObject->loadList($where));
    } else {
      $where["object_id"]  = "= '$object->_id'";
      $where["permission"] = ">= '$permType'";
      if(count($permObject->loadList($where))) {
        return true;
      } else {
        return $object->_ref_module->getPerm($permType);
      }
    }
  }
}
?>