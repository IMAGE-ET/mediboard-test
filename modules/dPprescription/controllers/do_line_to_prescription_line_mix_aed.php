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
$prescription_line_mix_id = CValue::post("prescription_line_mix_id");
$type = CValue::post("type");

$substitute_for_id = CValue::post("substitute_for_id");
$substitute_for_class = CValue::post("substitute_for_class");

// Chargement de la ligne de medicament
$line_med = new CPrescriptionLineMedicament();
$line_med->load($prescription_line_medicament_id);

// Si pas de prescription_line_mix, creation de la prescription_line_mix
if(!$prescription_line_mix_id){
  $prescription_line_mix = new CPrescriptionLineMix();
  $prescription_line_mix->prescription_id = $prescription_id;
  $prescription_line_mix->voie = $line_med->voie;
  $prescription_line_mix->type = $type;
  $prescription_line_mix->creator_id = $AppUI->user_id;
  $prescription_line_mix->praticien_id = $line_med->praticien_id;
  $prescription_line_mix->date_debut = $line_med->debut;
  $prescription_line_mix->time_debut = $line_med->time_debut;
  $prescription_line_mix->substitute_for_id = $substitute_for_id;
  $prescription_line_mix->substitute_for_class = $substitute_for_class;
  if($prescription_line_mix->substitute_for_id){
    $prescription_line_mix->substitution_active = 0;
  }
  $msg = $prescription_line_mix->store();
  CAppUI::displayMsg($msg, "CPrescriptionLineMix-msg-create");
  $prescription_line_mix_id = $prescription_line_mix->_id;
} else {
  // Chargement de la prescription_line_mix
  $prescription_line_mix = new CPrescriptionLineMix();
  $prescription_line_mix->load($prescription_line_mix_id);
}


/*
 * Comportement souhaité
 * --> Si la prescription_line_mix est parenterale, la premiere ligne non parenterale transforme la voie de la prise
 * --> On permet d'ajouter à une perf IV et IM une ligne parenterale
 */
$error = false;
if(($prescription_line_mix->voie == "Voie parentérale" || $line_med->voie == "Voie parentérale") && ($prescription_line_mix->voie != $line_med->voie)){
  if($prescription_line_mix->voie == "Voie parentérale"){
    if(in_array($line_med->voie, CPrescriptionLineMedicament::$corresp_voies["Voie parentérale"])){
      $prescription_line_mix->voie = $line_med->voie;
      $prescription_line_mix->store();
    } else {
      $error = true;  
    }
  }
  if($line_med->voie == "Voie parentérale"){
    if(!in_array($prescription_line_mix->voie, CPrescriptionLineMedicament::$corresp_voies["Voie parentérale"])){
      $error = true;
    }
  }
} else {
	if($prescription_line_mix->voie != $line_med->voie){
	  $error = true;
	}
}


if($error){
  CAppUI::setMsg("Attention, la voie de la ligne ne correspond pas à la voie de la perfusion", UI_MSG_WARNING);
  echo CAppUI::getMsg();
}

if($prescription_line_mix->signature_prat || $prescription_line_mix->signature_pharma){
  // suppression des signatures de la prescription_line_mix
  $prescription_line_mix->signature_prat = "0";
  $prescription_line_mix->signature_pharma = "0";
  $msg = $prescription_line_mix->store();
  CAppUI::displayMsg($msg, "CPrescriptionLineMix-msg-modify");
}

// Creation de la ligne de prescription_line_mix 
$prescription_line_mix_item = new CPrescriptionLineMixItem();
$prescription_line_mix_item->prescription_line_mix_id = $prescription_line_mix_id;
$prescription_line_mix_item->code_cip = $line_med->code_cip;
$prescription_line_mix_item->code_ucd = $line_med->code_ucd;
$prescription_line_mix_item->code_cis = $line_med->code_cis;
if($line_med->unite_duree == "heure"){
  $prescription_line_mix_item->duree = $line_med->duree;
}
$msg = $prescription_line_mix_item->store();
CAppUI::displayMsg($msg, "CPrescriptionLineMixItem-msg-create");

// Suppression de la ligne de medicament
if($prescription_line_mix_item->_id){
  $msg = $line_med->delete();
  CAppUI::displayMsg($msg, "CPrescriptionLineMedicament-msg-delete");
}

echo CAppUI::getMsg();
CApp::rip();

?>