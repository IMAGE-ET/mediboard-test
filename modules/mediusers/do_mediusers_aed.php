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
  	// keep track of former values for fieldModified below
		$this->_obj->loadOldObject();
		
    if ($msg = $this->_obj->store()) {
    	CAppUI::setMsg($msg, UI_MSG_ERROR);
    	if ($this->redirectError) {
        CAppUI::redirect($this->redirectError);
      }
    } 
    else {
      // Keep trace for redirections
      CValue::setSession($this->objectKeyGetVarName, $this->_obj->_id);
      
      $isNotNew = @$_POST[$this->objectKeyGetVarName];
      
      // Insert new group and function permission
      if ($this->_obj->fieldModified("function_id") || !$isNotNew) {
        $this->_obj->insFunctionPermission(); 
        $this->_obj->insGroupPermission();
      }
      
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