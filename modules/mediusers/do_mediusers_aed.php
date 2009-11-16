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
    	CAppUI::setMsg($msg, UI_MSG_ERROR);
    	if ($this->redirectError) {
      	//CAppUI::setMsg($msg, UI_MSG_ERROR);
        CAppUI::redirect($this->redirectError);
      }
    } 
    else {
      // Keep trace for redirections
      CValue::setSession($this->objectKeyGetVarName, $this->_obj->_id);
      
      // si modifDroit = 0, pas le droit de les modifier
      $modifDroit = CValue::postOrSessionAbs("modifDroit", "1");
      
      // Insert new group and function permission
      if($modifDroit){
        $this->_obj->insFunctionPermission(); 
        $this->_obj->insGroupPermission();
      }
      
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
      CAppUI::setMsg( $isNotNew ? $this->modifyMsg : $this->createMsg, UI_MSG_OK);
      if ($this->redirectStore) {
        CAppUI::redirect($this->redirectStore);
      }
    }
  }
}

$do = new CDoMediuserAddEdit();
$do->doIt();
?>