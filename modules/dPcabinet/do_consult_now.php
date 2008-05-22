<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

mbExport(CPlageconsult::$minutes_interval);

// Permissions ?
$module = CModule::getInstalled($m);
$canModule = $module->canDo();
$canModule->needsEdit();

$chir = new CMediusers;
$chir->load($_POST["prat_id"]);

$day_now = strftime("%Y-%m-%d");
$time_now = strftime("%H:%M:00");
$hour_now = strftime("%H:00:00");

$plage = new CPlageconsult();
$plageBefore = new CPlageconsult();
$plageAfter = new CPlageconsult();
// Cas ou une plage correspond
$where = array();
$where["chir_id"] = "= '$chir->user_id'";
$where["date"]    = "= '$day_now'";
$where["debut"]   = "<= '$time_now'";
$where["fin"]     = "> '$time_now'";
$plage->loadObject($where);

if(!$plage->plageconsult_id) {
  // Cas ou on a des plage en collision
	$where = array();
	$where["chir_id"] = "= '$chir->user_id'";
	$where["date"]    = "= '$day_now'";
	$where["debut"]   = "<= '$hour_now'";
	$where["fin"]     = ">= '$hour_now'";
  $plageBefore->loadObject($where);
	$where["debut"]   = "<= '".mbTime("+1 HOUR", $hour_now)."'";
	$where["fin"]     = ">= '".mbTime("+1 HOUR", $hour_now)."'";
  $plageAfter->loadObject($where);
  if($plageBefore->plageconsult_id) {
    if($plageAfter->plageconsult_id) {
      $plageBefore->fin = $plageAfter->debut;
    } else {
      $plageBefore->fin = max($plageBefore->fin, mbTime("+1 HOUR", $hour_now));
    }
    $plage =& $plageBefore;
  } elseif($plageAfter->plageconsult_id) {
    $plageAfter->debut = min($plageAfter->debut, $hour_now);
    $plage =& $plageAfter;
  } else {
    $plage->chir_id = $chir->user_id;
    $plage->date    = $day_now;
    $plage->freq    = "00:15:00";
    $plage->debut   = $hour_now;
    $plage->fin     = mbTime("+1 HOUR", $hour_now);
    $plage->libelle = "automatique";
  }
  $plage->updateFormFields();
  $plage->store();
}

$plage->loadRefsFwd();

$ref_chir = $plage->_ref_chir;

$consult = new CConsultation;
$consult->plageconsult_id = $plage->plageconsult_id;
$consult->patient_id = $_POST["patient_id"];

// Sejour_id dans le cas d'une urgence
if(isset($_POST["sejour_id"])){
  $consult->sejour_id = $_POST["sejour_id"];
}

$consult->heure = $time_now;
$consult->arrivee = $day_now." ".$time_now;
$consult->duree = 1;
$consult->chrono = CConsultation::PATIENT_ARRIVE;


// Cas des urgences
if(isset($_POST["sejour_id"])){
  // Changement de praticien pour le sejour
  $sejour = new CSejour();
  $sejour->load($_POST["sejour_id"]);
  $sejour->praticien_id = $_POST["prat_id"];
  $sejour->store();
  $sejour->loadRefRPU();
  
  // Motif de la consultation
  $consult->motif = "RPU: ";
  $consult->motif.= $sejour->_ref_rpu->diag_infirmier;
} 

// Cas standard
else {
  $consult->motif = "Consultation immédiate";
}

$consult->store();

if($ref_chir->isFromType(array("Anesthésiste"))) {
  // Un Anesthesiste a été choisi 
  $consultAnesth = new CConsultAnesth;
  $where = array();
  $where["consultation_id"] = "= '".$consult->consultation_id."'";
  $consultAnesth->loadObject($where);  
  $consultAnesth->consultation_id = $consult->consultation_id;
  $consultAnesth->operation_id = "";
  $consultAnesth->store();
}

// Si module d'urgencen changement de redirect
if(isset($_POST["sejour_id"])){
  $current_m = "dPurgences";  
} else {
  $current_m = "dPcabinet";
}

$AppUI->redirect("m=$current_m&tab=edit_consultation&selConsult=$consult->consultation_id&chirSel=$chir->user_id");

?>