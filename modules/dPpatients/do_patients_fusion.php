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


$do = new CDoPatientAddEdit;

// Test sur les Patient
$patient1 = new CPatient;
$patient2 = new CPatient;
if (!$patient1->load($_POST["patient1_id"]) || !$patient2->load($_POST["patient2_id"])){
  // Erreur sur les ID du patient
  $AppUI->setMsg("Fusion Impossible", UI_MSG_ERROR );
  $do->redirect = "";
  $do->doRedirect();
}


$do->doBind();

// Création du nouveau patient
if (intval(dPgetParam($_POST, "del"))) {
  // Suppression Impossible
  $AppUI->setMsg("Fusion Impossible", UI_MSG_ERROR );
  $do->redirect = "";
  $do->doRedirect();
} else {
  $do->doStore();
}


$patient_id = $do->_obj->patient_id;

// Régularisation des liens étrangers
$sql = "UPDATE sejour" .
    "\nSET patient_id = '$patient_id'" .
    "\nWHERE patient_id IN ('$patient1->patient_id','$patient2->patient_id')";
db_exec( $sql ); $msg = db_error();

$sql = "UPDATE consultation" .
    "\nSET patient_id = '$patient_id'" .
    "\nWHERE patient_id IN ('$patient1->patient_id','$patient2->patient_id')";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE antecedent" .
    "\nSET patient_id = '$patient_id'" .
    "\nWHERE patient_id IN ('$patient1->patient_id','$patient2->patient_id')";
db_exec( $sql ); $msg .= db_error();

$sql = "UPDATE traitement" .
    "\nSET patient_id = '$patient_id'" .
    "\nWHERE patient_id IN ('$patient1->patient_id','$patient2->patient_id')";
db_exec( $sql ); $msg .= db_error();

if(CModule::getInstalled("dPfiles")) {
  $sql = "UPDATE files_mediboard" .
      "\nSET file_object_id = '$patient_id'" .
      "\nWHERE file_object_id IN ('$patient1->patient_id','$patient2->patient_id')" .
      "\nAND file_class = 'CPatient'";
  db_exec( $sql ); $msg .= db_error();
}


if(CModule::getInstalled("dPcompteRendu")) {
  $sql = "UPDATE compte_rendu" .
      "\nSET object_id = '$patient_id'" .
      "\nWHERE object_id IN ('$patient1->patient_id','$patient2->patient_id')" .
      "\nAND type = 'patient'";
  db_exec( $sql ); $msg .= db_error();
}

if(CModule::getInstalled("dPsante400")) {
  $sql = "UPDATE id_sante400" .
      "\nSET object_id = '$patient_id'" .
      "\nWHERE object_id IN ('$patient1->patient_id','$patient2->patient_id')" .
      "\nAND object_class = 'CPatient'";
  db_exec( $sql ); $msg .= db_error();
}

if($msg) {
  $AppUI->setMsg($msg, UI_MSG_ERROR );
  $do->redirect = "";
}else{
  $patient1->delete();
  $patient2->delete();
}

$do->doRedirect();

?>