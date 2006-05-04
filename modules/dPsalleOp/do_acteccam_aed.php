<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Thomas Despoix
*/

require_once($AppUI->getModuleClass("dPsalleOp", "acteccam"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

class CDoActeCCAMAddEdit extends CDoObjectAddEdit {
  function CDoActeCCAMAddEdit() {
    $this->CDoObjectAddEdit("CActeCCAM", "acte_id");
    
    $this->createMsg = "Acte CCAM créé";
    $this->modifyMsg = "Acte CCAM modifié";
    $this->deleteMsg = "Acte supprimé";
	  
  }
  
  function doBind() {
    parent::doBind();
    
    $this->_obj->modificateurs = "";
    foreach ($_POST as $propName => $propValue) {
      if (preg_match("/modificateur_(.)/", $propName, $matches)) {
        $modificateur = $matches[1];
        $this->_obj->modificateurs .= $modificateur;
      }
    }
  }
}

$do = new CDoActeCCAMAddEdit();
$do->doIt();
?>