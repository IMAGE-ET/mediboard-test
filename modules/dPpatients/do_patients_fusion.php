<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPpatients", "patients"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

class CDoPatientAddEdit extends CDoObjectAddEdit {
  function CDoPatientAddEdit() {
    $this->CDoObjectAddEdit("CPatient", "patient_id");
    
    $this->createMsg = "Patient créé";
    $this->modifyMsg = "Patient modifié";
    $this->deleteMsg = "Patient supprimé";
    
    if ($dialog = dPgetParam($_POST, "dialog")) {
      $this->redirectDelete .= $this->redirect."&a=pat_selector&dialog=1";
      $this->redirectStore  .= $this->redirect."&a=vw_edit_patients&dialog=1";
    }
    else {
      $this->redirectDelete .= $this->redirect."&tab=vw_edit_patients";
      $this->redirectStore  .= $this->redirect."&tab=vw_edit_patients";
    }
  }
  
  function doStore() {
    parent::doStore();
    
    $dialog = dPgetParam($_POST, "dialog");
    $isNew = !dPgetParam($_POST, "patient_id");
    $patient_id = $this->_obj->patient_id;
    
    if ($isNew) {
      $this->redirectStore .= "&patient_id=$patient_id&created=$patient_id";
    } elseif($dialog) {
      $this->redirectStore .= "&name=".$this->_obj->nom."&firstname=".$this->_obj->prenom;
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

$patient1 = new CPatient;
$patient1->load($_POST["patient1_id"]);
$patient2 = new CPatient;
$patient2->load($_POST["patient2_id"]);

$do = new CDoPatientAddEdit;
$do->doBind();

// Création du nouveau patient
if (intval(dPgetParam($_POST, "del"))) {
  $do->doDelete();
} else {
  $do->doStore();
}

$patient_id = $do->_obj->patient_id;

// Régularisation des liens étrangers
$sql = "UPDATE sejour SET" .
    "\npatient_id = '$patient_id'" .
    "\nWHERE patient_id = '$patient1->patient_id'";
db_exec( $sql ); $msg = db_error();

$sql = "UPDATE sejour SET" .
    "\npatient_id = '$patient_id'" .
    "\nWHERE patient_id = '$patient2->patient_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE consultation SET" .
    "\npatient_id = '$patient_id'" .
    "\nWHERE patient_id = '$patient1->patient_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE consultation SET" .
    "\npatient_id = '$patient_id'" .
    "\nWHERE patient_id = '$patient2->patient_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE antecedent SET" .
    "\npatient_id = '$patient_id'" .
    "\nWHERE patient_id = '$patient1->patient_id'";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE antecedent SET" .
    "\npatient_id = '$patient_id'" .
    "\nWHERE patient_id = '$patient2->patient_id'";
db_exec( $sql ); $msg .= db_error();

if($msg) {
  mbTrace($msg, "erreur sql", true);
  exit(0);
}

$patient1->delete();
$patient2->delete();

$do->doRedirect();

?>