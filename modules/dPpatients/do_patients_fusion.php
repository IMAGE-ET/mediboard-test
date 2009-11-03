<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $m;

class CDoPatientMerge extends CDoObjectAddEdit {
  function CDoPatientMerge() {
    $this->CDoObjectAddEdit("CPatient", "patient_id");
    
    if ($dialog = CValue::post("dialog")) {
      $this->redirectDelete .= $this->redirect."&a=pat_selector&dialog=1";
      $this->redirectStore  .= $this->redirect."&a=vw_edit_patients&dialog=1";
    }
    else {
      $this->redirectDelete .= $this->redirect."&tab=vw_edit_patients";
      $this->redirectStore  .= $this->redirect."&tab=vw_edit_patients";
    }
    
    $this->redirectError = "";
  }
  
  function doStore() {
    parent::doStore();
    
    $dialog = CValue::post("dialog");
    $isNew = !CValue::post("patient_id");
    $patient_id = $this->_obj->patient_id;
    
    if ($isNew)
      $this->redirectStore .= "&patient_id=$patient_id&created=$patient_id";
    elseif($dialog)
      $this->redirectStore .= "&name=".$this->_obj->nom."&firstname=".$this->_obj->prenom;
  }
}

$do = new CDoPatientMerge;

$patient1_id = CValue::post("patient1_id");
$patient2_id = CValue::post("patient2_id");

// Erreur sur les ID du patient
$patient1 = new CPatient;
if (!$patient1->load($patient1_id)) {
  $do->errorRedirect("Patient 1 n'existe pas ou plus");
}

$patient2 = new CPatient;
if (!$patient2->load($patient2_id)) {
  $do->errorRedirect("Patient 2 n'existe pas ou plus");
}

if (intval(CValue::post("del"))) {
  $do->errorRedirect("Fusion en mode suppression impossible");
}

// Bind au nouveau patient
$do->doBind();

// Fusion effective
if ($msg = $do->_obj->merge(array($patient1, $patient2))) {
  $do->errorRedirect($msg);
}
  
$do->doRedirect();

?>