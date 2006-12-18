<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
  
if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date  = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();
$hour  = mbTime(null);
$board = mbGetValueFromGet("board", 0);

$prat_id    = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$selConsult = mbGetValueFromGetOrSession("selConsult", null);

$consult = new CConsultation;

// Test compliqué afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if($consult->load($selConsult)) {
    $consult->loadRefPlageConsult();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    mbSetValueToSession("selConsult");
  }
} else {
  if ($consult->load($selConsult)) {
    $consult->loadRefsFwd();
    if ($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      mbSetValueToSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->canEdit()) {
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


foreach ($listPlage as &$plage) {
  $plage->_ref_chir =& $userSel;
  $plage->loadRefsBack();
  foreach ($plage->_ref_consultations as $keyConsult => &$consultation) {
    if ($vue && ($consultation->chrono == CConsultation::TERMINE)) {
      unset($plage->_ref_consultations[$keyConsult]);
      continue;
    }

    $consultation->loadRefPatient();
    $consultation->getNumDocsAndFiles();
  }
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("tab"      , "edit_consultation");
$smarty->assign("board"    , $board);
$smarty->assign("date"     , $date);
$smarty->assign("hour"     , $hour);
$smarty->assign("vue"      , $vue);
$smarty->assign("today"    , $today);
$smarty->assign("userSel"  , $userSel);
$smarty->assign("listPlage", $listPlage);
$smarty->assign("consult"  , $consult);

$smarty->display("inc_list_consult.tpl");

?>