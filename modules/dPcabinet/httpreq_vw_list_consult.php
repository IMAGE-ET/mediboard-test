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

// Utilisateur sélectionné ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();

// Vérification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

// Consultation courante
$selConsult = mbGetValueFromGet("selConsult", 0);
$consult = new CConsultation();
if ($selConsult)
  $consult->consultation_id = $selConsult;
else
  $consult->consultation_id = 0;

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