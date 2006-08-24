<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "plageconsult"));
require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("mediusers"));
  
if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();
$hour = mbTime(null);

$prat_id    = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$selConsult = mbGetValueFromGetOrSession("selConsult", null);

$consult = new CConsultation;

// Test compliqué afin de savoir quelle consultation charger
if(isset($_GET["selConsult"])) {
  if($consult->load($selConsult)) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    $selConsult = null;
    mbSetValueToSession("selConsult");
  }
} else {
  if($consult->load($selConsult)) {
    $consult->loadRefsFwd();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      mbSetValueToSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  mbSetValueToSession("chirSel", 0);
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if($consult->consultation_id) {
  $date = $consult->_ref_plageconsult->date;
  mbSetValueToSession("date", $date);
}

// Récupération des plages de consultation du jour et chargement des références
$listPlage = new CPlageconsult();
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$where["date"] = "= '$date'";
$order = "debut";
$listPlage = $listPlage->loadList($where, $order);

$vue = mbGetValueFromGetOrSession("vue2", 0);


foreach($listPlage as $key => $value) {
  $listPlage[$key]->loadRefs();
  foreach($listPlage[$key]->_ref_consultations as $key2 => $value2) {
    if($vue && ($listPlage[$key]->_ref_consultations[$key2]->chrono == CC_TERMINE))
      unset($listPlage[$key]->_ref_consultations[$key2]);
    else {
      $listPlage[$key]->_ref_consultations[$key2]->loadRefPatient();
      $listPlage[$key]->_ref_consultations[$key2]->loadRefsDocs();
    }
  }
}

// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("tab"      , "edit_consultation");
$smarty->assign("date"     , $date);
$smarty->assign("hour"     , $hour);
$smarty->assign("vue"      , $vue);
$smarty->assign("today"    , $today);
$smarty->assign("userSel"  , $userSel);
$smarty->assign("listPlage", $listPlage);
$smarty->assign("consult"  , $consult);

$smarty->display("inc_list_consult.tpl");

?>