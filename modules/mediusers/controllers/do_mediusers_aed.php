<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

// we don't allow anybody to change his user type or profile
if ($_POST["user_id"] && !CAppUI::$user->isAdmin() && !CModule::getCanDo("admin")->admin) {
  unset($_POST['_user_type']);
  unset($_POST['_profile_id']);
}

class CDoMediuserAddEdit extends CDoObjectAddEdit {
  function CDoMediuserAddEdit() {
    $this->CDoObjectAddEdit("CMediusers", "user_id");
  }
  
  function doStore () {
    // keep track of former values for fieldModified below
    $obj = $this->_obj;
    $old = $obj->loadOldObject();

    if ($msg = $obj->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      if ($this->redirectError) {
        CAppUI::redirect($this->redirectError);
      }
    }
    else {
      // Keep trace for redirections
      CValue::setSession($this->objectKey, $obj->_id);
      
      // Insert new group and function permission
      if ($obj->fieldModified("function_id") || !$old->_id) {
        $obj->insFunctionPermission(); 
        $obj->insGroupPermission();
      }
      
      // Message
      CAppUI::setMsg($old->_id ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      
      // Redirection
      if ($this->redirectStore) {
        CAppUI::redirect($this->redirectStore);
      }
    }
  }
}

$do = new CDoMediuserAddEdit();
$do->doIt();
