<?php

/**
* Permissions class
*/

// Permission flags used in the DB
define("PERM_EDIT", "-1");
define("PERM_DENY", "0" );
define("PERM_READ", "1" );
define("PERM_ALL" , "-1");

// Flags for Mediboard modules 
define ("HIDDEN_READNONE_EDITNONE" ,  0); // PERM_DENY / HIDE
define ("HIDDEN_READNONE_EDITALL"  ,  2); // ..
define ("HIDDEN_READALL_EDITNONE"  ,  3); // PERM_READ / HIDE
define ("HIDDEN_READALL_EDITALL"   ,  4); // PERM_EDIT / HIDE
define ("VISIBLE_READNONE_EDITNONE",  5); // PERM_DENY / SHOW
define ("VISIBLE_READNONE_EDITALL" ,  6); // ..
define ("VISIBLE_READALL_EDITNONE" ,  1); // PERM_READ / SHOW
define ("VISIBLE_READALL_EDITALL"  , -1); // PERM_EDIT / SHOW

// Access
$module_permission_matrix = 
  array( // _module_visible
    false => array ( // _module_readall
      false => array ( // _module_editall
        false => HIDDEN_READNONE_EDITNONE,
        true  => HIDDEN_READNONE_EDITALL
      ),
      true => array ( // _module_editall
        false => HIDDEN_READALL_EDITNONE,
        true  => HIDDEN_READALL_EDITALL
      )
    ),
    true => array ( // _module_readall
      false => array ( // _module_editall
        false => VISIBLE_READNONE_EDITNONE,
        true  => VISIBLE_READNONE_EDITALL
      ),
      true => array ( // _module_editall
        false => VISIBLE_READALL_EDITNONE,
        true  => VISIBLE_READALL_EDITALL
      )
    )
  );

class CPermission extends CMbObject {
  // DB Table key
  var $permission_id = null;

  // DB Fields
  var $permission_user = null;
  var $permission_grant_on = "all";
  var $permission_item = PERM_ALL;
  var $permission_value = PERM_EDIT;

  // DB Form Fields
  var $_module_visible = null;
  var $_module_editall = null;
  var $_module_readall = null;
  var $_item_deny = null;
  var $_item_read = null;
  var $_item_edit = null;
  
  function CPermission() {
    $this->CMbObject("permissions", "permission_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function updateFormFields() {
    if ($this->permission_item == PERM_ALL) {
      switch ($this->permission_value) {
        case HIDDEN_READNONE_EDITNONE : $this->_module_visible = false; $this->_module_readall = false; $this->_module_editall = false; break;
        case HIDDEN_READNONE_EDITALL  : $this->_module_visible = false; $this->_module_readall = false; $this->_module_editall = true ; break;
        case HIDDEN_READALL_EDITNONE  : $this->_module_visible = false; $this->_module_readall = true ; $this->_module_editall = false; break;
        case HIDDEN_READALL_EDITALL   : $this->_module_visible = false; $this->_module_readall = true ; $this->_module_editall = true ; break;
        case VISIBLE_READNONE_EDITNONE: $this->_module_visible = true ; $this->_module_readall = false; $this->_module_editall = false; break;
        case VISIBLE_READNONE_EDITALL : $this->_module_visible = true ; $this->_module_readall = false; $this->_module_editall = true ; break;
        case VISIBLE_READALL_EDITNONE : $this->_module_visible = true ; $this->_module_readall = true ; $this->_module_editall = false; break;
        case VISIBLE_READALL_EDITALL  : $this->_module_visible = true ; $this->_module_readall = true ; $this->_module_editall = true ; break;
      }

      $this->_item_deny = null;
      $this->_item_read = null;
      $this->_item_edit = null;
    } else {
      $this->_item_deny = $this->permission_value == PERM_DENY;
      $this->_item_read = $this->permission_value == PERM_READ;
      $this->_item_edit = $this->permission_value == PERM_EDIT;
            
      $this->_module_visible = null; 
      $this->_module_readall = null; 
      $this->_module_editall = null;
    return;
    }
  }
  
  function updateDBFields() {
    if ($this->permission_item != PERM_ALL) {
      return;
    }
 
    $this->_module_visible = $this->_module_visible ? 1 : 0; 
    $this->_module_readall = $this->_module_readall ? 1 : 0;
    $this->_module_editall = $this->_module_editall ? 1 : 0;
    
    global $module_permission_matrix;
       
    $this->permission_value = $module_permission_matrix[$this->_module_visible][$this->_module_readall][$this->_module_editall];
  }
}

?>