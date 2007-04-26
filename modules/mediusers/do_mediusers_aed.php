<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;


class CDoMediuserAddEdit extends CDoObjectAddEdit {
  function CDoMediuserAddEdit() {
    $this->CDoObjectAddEdit("CMediusers", "user_id");
    $this->createMsg = "Utilisateur cr��";
    $this->modifyMsg = "Utilisateur modifi�";
    $this->deleteMsg = "Utilisateur supprim�";
  }
  
  function doStore () {
    global $AppUI;

    // Get older function permission
    $old = new CMediusers();
    $old->load($this->_obj->user_id);

    if ($msg = $this->_obj->store()) {
      if ($this->redirectError) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
        $AppUI->redirect($this->redirectError);
      }
    } else {
      // Copy permissions
      if ($profile_id = dPgetParam($_POST, "_profile_id")) {
        $user = new CUser;
        $user->load($this->_obj->user_id);
        $msg = $user->copyPermissionsFrom($profile_id, true);
      }
        
      // Insert new group and function permission
      $this->_obj->insFunctionPermission();
      $this->_obj->insGroupPermission();
      
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
      $this->doLog("store");
      $AppUI->setMsg( $isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      if ($this->redirectStore) {
        $AppUI->redirect($this->redirectStore);
      }
    }
  }
}

$do = new CDoMediuserAddEdit();
$do->doIt();
?>