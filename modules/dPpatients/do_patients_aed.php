<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

class CDoPatientAddEdit extends CDoObjectAddEdit {
  function CDoPatientAddEdit() {
    $this->CDoObjectAddEdit("CPatient", "patient_id");
    
    $this->createMsg = "Patient créé";
    $this->modifyMsg = "Patient modifié";
    $this->deleteMsg = "Patient supprimé";
	  
    if ($dialog = dPgetParam($_POST, "dialog")) {
      $this->redirectDelete .= $this->redirect."&a=pat_selector&dialog=1";
      $this->redirectStore  .= $this->redirect."&a=vw_edit_patients&dialog=1";
    }else {
      $tab = dPgetParam($_POST, "tab", "vw_edit_patients");
      $this->redirectDelete .= $this->redirect."&tab=$tab";
      $this->redirectStore  .= $this->redirect."&tab=$tab";
    }
  }
  
  function doStore() {
    parent::doStore();
    
    $dialog = dPgetParam($_POST, "dialog");
    
    if ($dialog) {
      $this->redirectStore .= "&a=pat_selector&dialog=1&name=".$this->_obj->nom."&firstName=".$this->_obj->prenom."&useVitale=".$this->_obj->_bind_vitale;
    }else{
      $this->redirectStore .= "m=dPpatients&tab=vw_idx_patients&id=".$this->_obj->patient_id."&nom=&prenom=";
    }
  }
  
  function doDelete() {
    parent::doDelete();
    
    $dialog = dPgetParam($_POST, "dialog");
    if($dialog) {
      $this->redirectDelete .= "&name=".$this->_obj->nom."&firstName=".$this->_obj->prenom."&id=0";
    }
  }
}


$do = new CDoPatientAddEdit;
$do->doIt();