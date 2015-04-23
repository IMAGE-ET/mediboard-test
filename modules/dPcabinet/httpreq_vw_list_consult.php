<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDO::checkEdit();

global $m;
$current_m = CValue::get("current_m", $m);

$today            = CMbDT::date();
$ds               = CSQLDataSource::get("std");

// get
$user             = CUser::get();
$boardItem        = CValue::get("boardItem", 0);
$plageconsult_id  = CValue::get("plageconsult_id");

// get or session
$date             = CValue::getOrSession("date", $today);
$prat_id          = CValue::getOrSession("chirSel", $user->_id);
$selConsult       = CValue::getOrSession("selConsult");
$vue              = CValue::getOrSession("vue2", 0);

$consult = new CConsultation();
// Test compliqué afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if ($consult->load($selConsult)) {
    $consult->loadRefPlageConsult(1);
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  }
  else {
    CValue::setSession("selConsult");
  }
}
else {
  if ($consult->load($selConsult)) {
    $consult->loadRefPlageConsult(1);
    if ($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      CValue::setSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = CMediusers::get($prat_id);
$canUserSel = $userSel->canDo();

if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de santé", UI_MSG_ALERT);
  if ($current_m != "dPurgences") {
    CAppUI::redirect("m=dPcabinet&tab=0");
  }
}

$canUserSel->needsEdit(array("chirSel"=>0));

if ($consult->_id) {
  $date = $consult->_ref_plageconsult->date;
  CValue::setSession("date", $date);
}

// Récupération des plages de consultation du jour et chargement des références
$plage = new CPlageconsult();
$plage->chir_id = $userSel->_id;
$plage->date = $date;
if ($plageconsult_id && $boardItem) {
  $plage->plageconsult_id = $plageconsult_id;
}
$order = "debut";
/** @var CPlageconsult[] $listPlage */
$listPlage = $plage->loadMatchingList($order);

CMbObject::massCountBackRefs($listPlage, "notes");

foreach ($listPlage as $_plage) {
  $_plage->_ref_chir =& $userSel;
  $consultations = $_plage->loadRefsConsultations(false, !$vue);
  $_plage->loadRefsNotes();
  
  // Mass preloading
  CStoredObject::massLoadFwdRef($consultations, "patient_id");
  CStoredObject::massLoadFwdRef($consultations, "sejour_id");
  CStoredObject::massLoadFwdRef($consultations, "categorie_id");
  CMbObject::massCountDocItems($consultations);
  /** @var CConsultAnesth[] $dossiers */
  $dossiers = CStoredObject::massLoadBackRefs($consultations, "consult_anesth");
  $count = CMbObject::massCountDocItems($dossiers);

  foreach ($consultations as $_consultation) {
    $_consultation->loadRefPatient();
    $_consultation->loadRefSejour();
    $_consultation->loadRefCategorie();
    $_consultation->countDocItems();
    $_consultation->loadRefBrancardage();
  }
}

// Récupération de la date du jour si $date
$current_date = ($date != $today) ? $today : null;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("boardItem", $boardItem);
$smarty->assign("tab"      , "edit_consultation");
$smarty->assign("board"    , CValue::get("board"   , 0));
$smarty->assign("date"     , $date);
$smarty->assign("hour"     , CMbDT::time());
$smarty->assign("vue"      , $vue);
$smarty->assign("userSel"  , $userSel);
$smarty->assign("listPlage", $listPlage);
$smarty->assign("consult"  , $consult);
$smarty->assign("canCabinet"  , CModule::getCanDo("dPcabinet"));
$smarty->assign("current_m", $current_m);
$smarty->assign("fixed_width", CValue::get("fixed_width", "0"));
$smarty->assign("mode_urgence", false);
$smarty->assign("current_date", $current_date);

$smarty->display("inc_list_consult.tpl");
