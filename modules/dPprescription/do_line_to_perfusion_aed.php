<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = CValue::post("prescription_id");
$prescription_line_medicament_id = CValue::post("prescription_line_medicament_id");
$perfusion_id = CValue::post("perfusion_id");
$type = CValue::post("type");

$substitute_for_id = CValue::post("substitute_for_id");
$substitute_for_class = CValue::post("substitute_for_class");

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
  $perfusion->substitute_for_id = $substitute_for_id;
  $perfusion->substitute_for_class = $substitute_for_class;
  if($perfusion->substitute_for_id){
    $perfusion->substitution_active = 0;
  }
  $msg = $perfusion->store();
  $AppUI->displayMsg($msg, "CPerfusion-msg-create");
  $perfusion_id = $perfusion->_id;
} else {
  // Chargement de la perfusion
  $perfusion = new CPerfusion();
  $perfusion->load($perfusion_id);
}


/*
 * Comportement souhaité
 * --> Si la perfusion est parenterale, la premiere ligne non parenterale transforme la voie de la prise
 * --> On permet d'ajouter à une perf IV et IM une ligne parenterale
 */
$error = false;
if(($perfusion->voie == "Voie parentérale" || $line_med->voie == "Voie parentérale") && ($perfusion->voie != $line_med->voie)){
  if($perfusion->voie == "Voie parentérale"){
    if(in_array($line_med->voie, CPrescriptionLineMedicament::$corresp_voies["Voie parentérale"])){
      $perfusion->voie = $line_med->voie;
      $perfusion->store();
    } else {
      $error = true;  
    }
  }
  if($line_med->voie == "Voie parentérale"){
    if(!in_array($perfusion->voie, CPrescriptionLineMedicament::$corresp_voies["Voie parentérale"])){
      $error = true;
    }
  }
} else {
	if($perfusion->voie != $line_med->voie){
	  $error = true;
	}
}


if($error){
  $AppUI->setMsg("Attention, la voie de la ligne ne correspond pas à la voie de la perfusion", UI_MSG_WARNING);
  echo $AppUI->getMsg();
//  CApp::rip(); 
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
$perfusion_line->code_ucd = $line_med->code_ucd;
$perfusion_line->code_cis = $line_med->code_cis;
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