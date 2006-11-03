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
  }
  
  function getSpecs() {
    return array (
      "user_id"      => "ref|notNull",
      "object_id"    => "ref",
      "object_class" => "str|notNull",
      "permission"   => "numchar|maxLength|1|notNull"
    );
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
  
  function loadUserPerms() {
    global $AppUI, $userPermsObjects, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $perm = new CPermObject;
    $listPermsModules = array();
    $where = array();
    $where["user_id"] = "= '$AppUI->user_id'";
    $listPermsObjects = $perm->loadList($where);
    $userPermsObjects = array();
    foreach($listPermsObjects as $perm_obj) {
      $userPermsObjects[$perm_obj->object_class][$perm_obj->object_id] = $perm_obj;
    }
  }
  
  function getPermObject($object, $permType) {
    global $AppUI, $userPermsObjects, $permissionSystemeDown;
    if($permissionSystemeDown) {
      return true;
    }
    $result = PERM_DENY;
    $object_class = get_class($object);
    $object_id    = $object->_id;
    if(isset($userPermsObjects[$object_class][0]) || isset($userPermsObjects[$object_class][$object_id])) {
      if(isset($userPermsObjects[$object_class][0])) {
        $result = $userPermsObjects[$object_class][0]->permission;
      }
      if(isset($userPermsObjects[$object_class][$object_id])) {
        $result = $userPermsObjects[$object_class][$object_id]->permission;
      }
    } else {
      return $object->_ref_module->getPerm($permType);
    }
    return $result >= $permType;
  }
}

CPermObject::loadUserPerms();

?>