<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$module = CModule::getInstalled($m);
$canEdit = $module->canEdit();

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$chir = new CMediusers;
$chir->load($_POST["prat_id"]);

$day_now = strftime("%Y-%m-%d");
$hour_now = strftime("%H:%M:00");
$debut = strftime("%H:00:00");

$plage = new CPlageconsult();
$plageBefore = new CPlageconsult();
$plageAfter = new CPlageconsult();
// Cas ou une plage correspond
$where = array();
$where["chir_id"] = "= '$chir->user_id'";
$where["date"]    = "= '$day_now'";
$where["debut"]   = "<= '$hour_now'";
$where["fin"]     = "> '$hour_now'";
$plage->loadObject($where);
if(!$plage->plageconsult_id) {
  // Cas ou on a des plage en collision
	$where = array();
	$where["chir_id"] = "= '$chir->user_id'";
	$where["date"]    = "= '$day_now'";
	$where["debut"]   = "<= '$debut'";
	$where["fin"]     = ">= '$debut'";
  $plageBefore->loadObject($where);
	$where["debut"]   = "<= '".mbTime("+1 HOUR", $debut)."'";
	$where["fin"]     = ">= '".mbTime("+1 HOUR", $debut)."'";
  $plageAfter->loadObject($where);
  if($plageBefore->plageconsult_id) {
    if($plageAfter->plageconsult_id) {
      $plageBefore->fin = $plageAfter->debut;
    } else {
      $plageBefore->fin = max($plageBefore->fin, mbTime("+1 HOUR", $debut));
    }
    $plage =& $plageBefore;
  } elseif($plageAfter->plageconsult_id) {
    $plageAfter->debut = min($plageAfter->debut, $debut);
    $plage =& $plageAfter;
  } else {
    $plage->chir_id = $chir->user_id;
    $plage->date    = $day_now;
    $plage->freq    = "00:15:00";
    $plage->debut   = $debut;
    $plage->fin     = mbTime("+1 HOUR", $debut);
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
$consult->heure = $hour_now;
$consult->arrivee = $day_now." ".$hour_now;
$consult->duree = 1;
$consult->chrono = CConsultation::PATIENT_ARRIVE;
$consult->motif = "Consultation immédiate";
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


$AppUI->redirect("m=dPcabinet&tab=edit_consultation&selConsult=$consult->consultation_id&chirSel=$chir->user_id");

?>