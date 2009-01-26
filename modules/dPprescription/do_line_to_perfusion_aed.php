<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI;

$prescription_id = mbGetValueFromPost("prescription_id");
$prescription_line_medicament_id = mbGetValueFromPost("prescription_line_medicament_id");
$perfusion_id = mbGetValueFromPost("perfusion_id");
$type = mbGetValueFromPost("type");

// Chargement de la ligne de medicament
$line_med = new CPrescriptionLineMedicament();
$line_med->load($prescription_line_medicament_id);

// Si pas de perfusion, creation de la perfusion
if(!$perfusion_id){
  $perfusion = new CPerfusion();
  $perfusion->prescription_id = $prescription_id;
  $perfusion->voie = $line_med->voie;
  $perfusion->type = $type;
  $perfusion->creator_id = $AppUI->user_id;
  $perfusion->praticien_id = $line_med->praticien_id;
  $perfusion->date_debut = $line_med->debut;
  $perfusion->time_debut = $line_med->time_debut;
  $msg = $perfusion->store();
  $AppUI->displayMsg($msg, "CPerfusion-msg-create");
  $perfusion_id = $perfusion->_id;
} else {
  // Chargement de la perfusion
  $perfusion = new CPerfusion();
  $perfusion->load($perfusion_id);
}

// On empeche qu'une ligne soit rajoutée dans la perf si la voie selectionnée est mauvaise
if($perfusion->voie != $line_med->voie){
  $AppUI->setMsg("La voie de la ligne ne correspond pas à la voie de la perfusion", UI_MSG_ERROR);
  echo $AppUI->getMsg();
  CApp::rip(); 
}

if($perfusion->signature_prat || $perfusion->signature_pharma){
  // suppression des signatures de la perfusion
  $perfusion->signature_prat = "0";
  $perfusion->signature_pharma = "0";
  $msg = $perfusion->store();
  $AppUI->displayMsg($msg, "CPerfusion-msg-modify");
}

// Creation de la ligne de perfusion 
$perfusion_line = new CPerfusionLine();
$perfusion_line->perfusion_id = $perfusion_id;
$perfusion_line->code_cip = $line_med->code_cip;
if($line_med->unite_duree == "heure"){
  $perfusion_line->duree = $line_med->duree;
}
$msg = $perfusion_line->store();
$AppUI->displayMsg($msg, "CPerfusionLine-msg-create");

// Suppression de la ligne de medicament
if($perfusion_line->_id){
  $msg = $line_med->delete();
  $AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-delete");
}

echo $AppUI->getMsg();
CApp::rip();

?>