<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

class CDoPatientMerge extends CDoObjectAddEdit {
  function CDoPatientMerge() {
    $this->CDoObjectAddEdit("CPatient", "patient_id");
    
    if ($dialog = dPgetParam($_POST, "dialog")) {
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
    
    $dialog = dPgetParam($_POST, "dialog");
    $isNew = !dPgetParam($_POST, "patient_id");
    $patient_id = $this->_obj->patient_id;
    
    if ($isNew) {
      $this->redirectStore .= "&patient_id=$patient_id&created=$patient_id";
    } 
    elseif($dialog) {
      $this->redirectStore .= "&name=".$this->_obj->nom."&firstname=".$this->_obj->prenom;
    }
  }
}

$do = new CDoPatientMerge;

// Erreur sur les ID du patient
$patient1 = new CPatient;
if (!$patient1->load($_POST["patient1_id"])) {
  $do->errorRedirect("Patient 1 n'existe pas ou plus");
}

$patient2 = new CPatient;
if (!$patient2->load($_POST["patient2_id"])) {
  $do->errorRedirect("Patient 2 n'existe pas ou plus");
}

if($testMerge = $patient1->checkMerge($patient1, $patient2)) {
  $do->errorRedirect($testMerge);
}

$do->doBind();

// Création du nouveau patient
if (intval(dPgetParam($_POST, "del"))) {
  $do->errorRedirect("Fusion en mode suppression impossible");
}

$do->doStore();

$newPatient =& $do->_obj;

// Transfert de toutes les backrefs
if ($msg = $newPatient->transferBackRefsFrom($patient1)) {
  $do->errorRedirect($msg);
}

if ($msg = $newPatient->transferBackRefsFrom($patient2)) {
  $do->errorRedirect($msg);
}

$newPatient->onMerge();

// Suppression des anciens objets
if ($msg = $patient1->delete()) {
  $do->errorRedirect($msg);
}
  
if ($msg = $patient2->delete()) {
  $do->errorRedirect($msg);
}
  
$do->doRedirect();

?>