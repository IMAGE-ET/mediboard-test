<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
 
$current_m = CValue::get("current_m", $m);

$can->needsEdit();
$ds = CSQLDataSource::get("std");
$date      = CValue::getOrSession("date", mbDate());
$hour      = mbTime();
$board     = CValue::get("board", 0);
$boardItem = CValue::get("boardItem", 0);
$plageconsult_id = CValue::get("plageconsult_id");

$prat_id    = CValue::getOrSession("chirSel", $AppUI->user_id);
$selConsult = CValue::getOrSession("selConsult");

$consult = new CConsultation;

// Test compliqué afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if($consult->load($selConsult)) {
    $consult->loadRefPlageConsult(1);
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  } else {
    CValue::setSession("selConsult");
  }
} else {
  if ($consult->load($selConsult)) {
    $consult->loadRefPlageConsult(1);
    if ($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      CValue::setSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$canUserSel = $userSel->canDo();

if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  if($current_m != "dPurgences"){
    CAppUI::redirect("m=dPcabinet&tab=0");
  }
}

$canUserSel->needsEdit(array("chirSel"=>0));

if ($consult->_id) {
  $date = $consult->_ref_plageconsult->date;
  CValue::setSession("date", $date);
}

// Récupération des plages de consultation du jour et chargement des références
$listPlage = new CPlageconsult();
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$where["date"] = "= '$date'";
if ($plageconsult_id && $boardItem) {
  $where["plageconsult_id"] =  $ds->prepare("= %", $plageconsult_id);
}
$order = "debut";
$listPlage = $listPlage->loadList($where, $order);

$vue = CValue::getOrSession("vue2", 0);

foreach ($listPlage as &$plage) {
  $plage->_ref_chir =& $userSel;
  $plage->loadRefsConsultations(true, !$vue);
	$plage->loadRefsNotes();
	
	// Mass preloading
  CMbObject::massLoadFwdRef($plage->_ref_consultations, "patient_id");
  CMbObject::massLoadFwdRef($plage->_ref_consultations, "sejour_id");
  CMbObject::massLoadFwdRef($plage->_ref_consultations, "categorie_id");

  foreach ($plage->_ref_consultations as &$consultation) {
    $consultation->loadRefPatient(1);
		$consultation->loadRefSejour(1);
    $consultation->loadRefCategorie(1);
    $consultation->countDocItems();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("boardItem", $boardItem);
$smarty->assign("tab"      , "edit_consultation");
$smarty->assign("board"    , $board);
$smarty->assign("date"     , $date);
$smarty->assign("hour"     , $hour);
$smarty->assign("vue"      , $vue);
$smarty->assign("userSel"  , $userSel);
$smarty->assign("listPlage", $listPlage);
$smarty->assign("consult"  , $consult);
$smarty->assign("canCabinet"  , CModule::getCanDo("dPcabinet"));
$smarty->assign("current_m", $current_m);
$smarty->assign("fixed_width", CValue::get("fixed_width", "0"));
$smarty->assign("mode_urgence", false);

$smarty->display("inc_list_consult.tpl");

?>