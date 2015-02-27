<?php
/**
 * $Id:$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Initialisation de variables
$order_col_pre = CValue::getOrSession("order_col_pre", "heure");
$order_way_pre = CValue::getOrSession("order_way_pre", "ASC");
$date          = CValue::getOrSession("date", CMbDT::date());
$next          = CMbDT::date("+1 DAY", $date);
$filter        = CValue::getOrSession("filter");
$is_modal      = CValue::get("is_modal", 0);

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

$date_min = CMbDT::dateTime("00:00:00", $date);
$date_max = CMbDT::dateTime("23:59:59", $date);

// Récupération de la liste des anesthésistes
$mediuser = new CMediusers();
$anesthesistes = $mediuser->loadAnesthesistes(PERM_READ);

$consult = new CConsultation();

// Récupération des consultation d'anesthésie du jour
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$ljoin["patients"]     = "consultation.patient_id = patients.patient_id";

$where = array();
$where["consultation.patient_id"] = "IS NOT NULL";
$where["consultation.annule"] = "= '0'";
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($anesthesistes));
$where["plageconsult.date"] = "= '$date'";

if ($order_col_pre == "patient_id") {
  $order = "patients.nom $order_way_pre, patients.prenom $order_way_pre, consultation.heure";
}
else {
  $order = "consultation.".$order_col_pre." ".$order_way_pre;
}

/** @var CConsultation[] $listConsultations */
$listConsultations = $consult->loadList($where, $order, null, null, $ljoin);
$dossiers_anesth = CStoredObject::massLoadBackRefs($listConsultations, "consult_anesth");

// Optimisation des chargements
CStoredObject::massLoadFwdRef($listConsultations, "patient_id");
CStoredObject::massLoadFwdRef($listConsultations, "plageconsult_id");

foreach ($listConsultations as $_consult) {
  $dossiers_anesth_consult = $_consult->loadRefsDossiersAnesth();

  if (!count($dossiers_anesth_consult)) {
    unset($listConsultations[$_consult->_id]);
    continue;
  }

  $_consult->loadRefPatient();
  $_consult->loadRefPlageconsult();
  $_consult->_ref_chir->loadRefFunction();
}

$operations = CStoredObject::massLoadFwdRef($dossiers_anesth, "operation_id");
CStoredObject::massLoadFwdRef($dossiers_anesth, "sejour_id");
CStoredObject::massLoadFwdRef($operations, "plageop_id");
CStoredObject::massLoadFwdRef($operations, "sejour_id");

/** @var CSejour[] $sejours_total */
$sejours_total = array();

foreach ($listConsultations as $_consult) {
  $dossier_empty = false;
  $dossiers_anesth = $_consult->_refs_dossiers_anesth;
  foreach ($dossiers_anesth as $_dossier) {
    $_dossier->loadRefOperation();
    $_sejour = $_dossier->_ref_sejour;
    $_sejour->loadRefsOperations();
    if ($_sejour->_id) {
      $sejours_total[$_sejour->_id] = $_sejour;
    }
    else {
      $dossier_empty = true;
    }
  }
  $_consult->_next_sejour_and_operation = null;
  if ($dossier_empty) {
    $next = $_consult->_ref_patient->getNextSejourAndOperation($_consult->_ref_plageconsult->date);

    if ($next["COperation"]->_id) {
      $next["COperation"]->loadRefSejour();
      $next["COperation"]->_ref_sejour->loadRefPraticien();
      $next["COperation"]->_ref_sejour->loadNDA();
      $next["COperation"]->_ref_sejour->loadRefsNotes();
      if ($filter == "dhe") {
        unset($listConsultations[$_consult->_id]);
      }
    }
    if ($next["CSejour"]->_id) {
      $next["CSejour"]->loadRefPraticien();
      $next["CSejour"]->loadNDA();
      $next["CSejour"]->loadRefsNotes();
      if ($filter == "dhe") {
        unset($listConsultations[$_consult->_id]);
      }
    }
    $_consult->_next_sejour_and_operation = $next;
  }
  elseif ($filter == "dhe") {
    unset($listConsultations[$_consult->_id]);
  }
}

CStoredObject::massLoadFwdRef($sejours_total, "patient_id");
CStoredObject::massLoadFwdRef($sejours_total, "praticien_id");
CStoredObject::massLoadBackRefs($sejours_total, "notes");
CStoredObject::massLoadBackRefs($sejours_total, "affectations", "sortie DESC");

// Chargement des NDA
CSejour::massLoadNDA($sejours_total);

// Chargement optimisé des prestations
CSejour::massCountPrestationSouhaitees($sejours_total);

foreach ($sejours_total as $_sejour) {
  $_sejour->loadRefPatient();
  $_sejour->loadRefPraticien();
  $_sejour->loadRefsNotes();
  $_sejour->loadRefFirstAffectation();
  $_sejour->getDroitsCMU();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);
$smarty->assign("filter", $filter);
$smarty->assign("is_modal", $is_modal);
$smarty->assign("date_min"         , $date_min);
$smarty->assign("date_max"         , $date_max);
$smarty->assign("date_demain"      , $date_demain);
$smarty->assign("date_actuelle"    , $date_actuelle);
$smarty->assign("date"             , $date);
$smarty->assign("order_col_pre"    , $order_col_pre);
$smarty->assign("order_way_pre"    , $order_way_pre);
$smarty->assign("listConsultations", $listConsultations);
$smarty->assign("prestations"      , CPrestation::loadCurrentList());
$smarty->assign("canAdmissions"    , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"      , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp"    , CModule::getCanDo("dPplanningOp"));

$smarty->display("inc_vw_preadmissions.tpl");
