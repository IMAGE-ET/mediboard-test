<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

class CDoMediuserAddEdit extends CDoObjectAddEdit {
  function CDoMediuserAddEdit() {
    $this->CDoObjectAddEdit("CMediusers", "user_id");
  }
  
  function doStore () {
    global $AppUI;

    // Get older function permission
    $old = new CMediusers();
    $old->load($this->_obj->user_id);

    
    if ($msg = $this->_obj->store()) {
    	$AppUI->setMsg($msg, UI_MSG_ERROR);
    	if ($this->redirectError) {
      	//$AppUI->setMsg($msg, UI_MSG_ERROR);
        $AppUI->redirect($this->redirectError);
      }
    } 
    else {
      // Keep trace for redirections
      mbSetValueToSession($this->objectKeyGetVarName, $this->_obj->_id);
      
      // si modifDroit = 0, pas le droit de les modifier
      $modifDroit = mbGetAbsValueFromPostOrSession("modifDroit", "1");
      
      // Insert new group and function permission
      if($modifDroit){
        $this->_obj->insFunctionPermission(); 
        $this->_obj->insGroupPermission();
      }
      
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
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