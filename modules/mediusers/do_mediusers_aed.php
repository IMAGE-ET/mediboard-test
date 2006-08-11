<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));

class CDoMediuserAddEdit extends CDoObjectAddEdit {
  function CDoMediuserAddEdit() {
    $this->CDoObjectAddEdit("CMediusers", "user_id");
    $this->createMsg = "Utilisateur créé";
    $this->modifyMsg = "Utilisateur modifié";
    $this->deleteMsg = "Utilisateur supprimé";
  }
  
  function doStore () {
    global $AppUI;

    // get older function permission
    $old = new CMediusers();
    $old->load($this->_obj->user_id);

    if ($msg = $this->_obj->store()) {
      if ($this->redirectError) {
        $AppUI->setMsg($msg, UI_MSG_ERROR);
        $AppUI->redirect($this->redirectError);
      }
    } else {
      // delete older function permission
      $old->delFunctionPermission();

      // copy permissions
      if ($profile_id = dPgetParam($_POST, "_profile_id")) {
        $user = new CUser;
        $user->load($this->_obj->user_id);
        $msg = $user->copyPermissionsFrom($profile_id, true);
      }
        
      // insert new group and function permission
      $this->_obj->insFunctionPermission();
      $this->_obj->insGroupPermission();
      
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
      $this->doLog("store");
      if ($this->redirectStore) {
        $AppUI->setMsg( $isNotNew ? $this->createMsg : $this->modifyMsg, UI_MSG_OK);
        $AppUI->redirect($this->redirectStore);
      }
    }
  }
}

$do = new CDoMediuserAddEdit();
$do->doIt();
?>