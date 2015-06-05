<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Type d'admission
$type            = CValue::getOrSession("type");
$services_ids    = CValue::getOrSession("services_ids");
$prat_id         = CValue::getOrSession("prat_id");
$order_col       = CValue::getOrSession("order_col", "patient_id");
$order_way       = CValue::getOrSession("order_way", "ASC");
$date            = CValue::getOrSession("date", CMbDT::date());
$heure           = CValue::getOrSession("heure");
$next            = CMbDT::date("+1 DAY", $date);
$filterFunction  = CValue::getOrSession("filterFunction");
$enabled_service = CValue::getOrSession("active_filter_services", 0);

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);
if ($heure) {
  $date_min = CMbDT::dateTime($heure, $date);
  $date_max = CMbDT::dateTime($heure, $date);
}
else {
  $date_min = CMbDT::dateTime("00:00:00", $date);
  $date_max = CMbDT::dateTime("23:59:59", $date);
}

// Entrées de la journée
$sejour = new CSejour();

$group = CGroups::loadCurrent();

// Lien avec les patients et les praticiens
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if (count($services_ids) && $enabled_service) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
}

// Filtre sur le type du séjour
if ($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
  if ($type !== 'tous') {
    $where["sejour.type"] = " = '$type'";
  }
}
else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.entree"]   = "<= '$date_max'";
$where["sejour.sortie"]   = ">= '$date_min'";
$where["sejour.annule"]   = "= '0'";

if ($order_col != "patient_id" && $order_col != "entree" && $order_col != "sortie" && $order_col != "praticien_id") {
  $order_col = "patient_id";  
}

if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom $order_way, sejour.entree";
}

if ($order_col == "entree") {
  $order = "sejour.entree $order_way, sejour.sortie $order_way, patients.nom, patients.prenom";
}

if ($order_col == "sortie") {
  $order = "sejour.sortie $order_way, sejour.entree $order_way, patients.nom, patients.prenom";
}

if ($order_col == "praticien_id") {
  $order = "users.user_last_name $order_way, users.user_first_name";
}

$show_curr_affectation = CAppUI::conf("dPadmissions show_curr_affectation");

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);
if ($heure) {
  $date_min = CMbDT::dateTime("00:00:00", $date);
  $date_max = CMbDT::dateTime("23:59:59", $date);
  $where["sejour.entree"]   = "<= '$date_max'";
  $where["sejour.sortie"]   = ">= '$date_min'";
  $total_sejours = $sejour->loadList($where, $order, null, null, $ljoin);
}
else {
  $total_sejours = null;
}

$patients   = CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CMbObject::massLoadFwdRef($praticiens, "function_id");

// Chargement optimisée des prestations
CSejour::massCountPrestationSouhaitees($sejours);

CStoredObject::massLoadBackRefs($sejours, "notes");
CStoredObject::massLoadBackRefs($patients, "dossier_medical");

// Chargement des NDA
CSejour::massLoadNDA($sejours);
// Chargement des IPP
CPatient::massLoadIPP($patients);

// Chargement optimisé des prestations
CSejour::massCountPrestationSouhaitees($sejours);

$operations = CStoredObject::massLoadBackRefs($sejours, "operations", "date ASC", array("annulee" => "= '0'"));

CStoredObject::massLoadBackRefs($operations, "actes_ngap", "lettre_cle DESC");

$order = "code_association, code_acte,code_activite, code_phase, acte_id";
CStoredObject::massLoadBackRefs($operations, "actes_ccam", $order);

CStoredObject::massLoadBackRefs($sejours, "affectations");

foreach ($sejours as $sejour_id => $_sejour) {
  $praticien = $_sejour->loadRefPraticien();
  if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }

  $_sejour->checkDaysRelative($date);
  
  // Chargement du patient
  $patient = $_sejour->loadRefPatient();

  $dossier_medical = $patient->loadRefDossierMedical(false);
  
  // Chargement des notes du séjour
  $_sejour->loadRefsNotes();

  // Chargement des interventions
  $whereOperations = array("annulee" => "= '0'");
  $_sejour->loadRefsOperations($whereOperations);
  foreach ($_sejour->_ref_operations as $operation) {
    $operation->loadRefsActes();
  }

  // Chargement de l'affectation
  if ($show_curr_affectation) {
    $affectation = $_sejour->loadRefCurrAffectation();
  }
  else {
    $_sejour->loadRefsAffectations();
    $affectation = $_sejour->_ref_first_affectation;
  }
}

if (CAppUI::conf("dPadmissions show_deficience")) {
  $dossiers = CMbArray::pluck($sejours, "_ref_patient", "_ref_dossier_medical");
  CDossierMedical::massCountAntecedentsByType($dossiers, "deficience");
}

// Si la fonction selectionnée n'est pas dans la liste des fonction, on la rajoute
if ($filterFunction && !array_key_exists($filterFunction, $functions)) {
  $_function = new CFunctions();
  $_function->load($filterFunction);
  $functions[$filterFunction] = $_function;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"            , $hier);
$smarty->assign("demain"          , $demain);
$smarty->assign("date_min"        , $date_min);
$smarty->assign("date_max"        , $date_max);
$smarty->assign("date_demain"     , $date_demain);
$smarty->assign("date_actuelle"   , $date_actuelle);
$smarty->assign("date"            , $date);
$smarty->assign("order_col"       , $order_col);
$smarty->assign("order_way"       , $order_way);
$smarty->assign("sejours"         , $sejours);
$smarty->assign("total_sejours"   , $total_sejours);
$smarty->assign("prestations"     , CPrestation::loadCurrentList());
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("functions"       , $functions);
$smarty->assign("filterFunction"  , $filterFunction);
$smarty->assign("which"           , $show_curr_affectation ? "curr" : "first");
$smarty->assign("heure"           , $heure);
$smarty->assign('enabled_service' , $enabled_service);

$smarty->display("inc_vw_presents.tpl");
