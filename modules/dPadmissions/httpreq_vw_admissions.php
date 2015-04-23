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
$type           = CValue::getOrSession("type");
$services_ids   = CValue::getOrSession("services_ids");
$prat_id        = CValue::getOrSession("prat_id");
$selAdmis       = CValue::getOrSession("selAdmis", "0");
$selSaisis      = CValue::getOrSession("selSaisis", "0");
$order_col      = CValue::getOrSession("order_col", "patient_id");
$order_way      = CValue::getOrSession("order_way", "ASC");
$date           = CValue::getOrSession("date", CMbDT::date());
$filterFunction = CValue::getOrSession("filterFunction");
$period         = CValue::getOrSession("period");
$type_pec       = CValue::get("type_pec" , array("M", "C", "O"));

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

$date_min = CMbDT::dateTime("00:00:00", $date);
$date_max = CMbDT::dateTime("23:59:59", $date);

if ($period) {
  $hour = CAppUI::conf("dPadmissions hour_matin_soir");
  if ($period == "matin") {
    // Matin
    $date_max = CMbDT::dateTime($hour, $date);
  }
  else {
    // Soir
    $date_min = CMbDT::dateTime($hour, $date);
  }
}

// Entrées de la journée
$sejour = new CSejour();

$group = CGroups::loadCurrent();

// Lien avec les patients et les praticiens
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if (count($services_ids)) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
}

// Filtre sur le type du séjour
if ($type == "ambucomp") {
  $where["sejour.type"] = " = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
  $where["sejour.type"] = " = '$type'";
}
else {
  $where["sejour.type"] = " != 'urg' AND `sejour`.`type` != 'seances'";
}

// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.entree"]   = "BETWEEN '$date_min' AND '$date_max'";
$where["sejour.annule"]   = "= '0'";

if ($selAdmis != "0") {
  $where["sejour.entree_reelle"] = " IS NULL OR `sejour`.`entree_reelle` = '0000-00-00 00:00:00'";
}

if ($selSaisis != "0") {
  $where["sejour.entree_preparee"] = "= '0'";
}

if ($order_col != "patient_id" && $order_col != "entree_prevue" && $order_col != "praticien_id") {
  $order_col = "patient_id";
}

if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom $order_way, sejour.entree_prevue";
}

if ($order_col == "entree_prevue") {
  $order = "sejour.entree_prevue $order_way, patients.nom, patients.prenom";
}

if ($order_col == "praticien_id") {
  $order = "users.user_last_name $order_way, users.user_first_name";
}
$where["sejour.type_pec"] = CSQLDataSource::prepareIn($type_pec);

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

// Mass preloading
$patients   = CStoredObject::massLoadFwdRef($sejours, "patient_id");
CStoredObject::massLoadFwdRef($sejours, "etablissement_entree_id");
$praticiens = CStoredObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CStoredObject::massLoadFwdRef($praticiens, "function_id");
CStoredObject::massLoadBackRefs($sejours, "affectations");

// Chargement optimisé des prestations
CSejour::massCountPrestationSouhaitees($sejours);

CStoredObject::massLoadBackRefs($sejours, "notes");
CStoredObject::massLoadBackRefs($patients, "dossier_medical");

$operations = CStoredObject::massLoadBackRefs($sejours, "operations", "date ASC", array("annulee" => "= '0'"));
CStoredObject::massLoadBackRefs($operations, "actes_ngap", "lettre_cle DESC");

$order = "code_association, code_acte,code_activite, code_phase, acte_id";
CStoredObject::massLoadBackRefs($operations, "actes_ccam", $order);

/** @var COperation[] $operations_total */
$operations_total = array();

// Chargement des NDA
CSejour::massLoadNDA($sejours);
// Chargement des IPP
CPatient::massLoadIPP($patients);
foreach ($sejours as $sejour_id => $_sejour) {
  $praticien = $_sejour->loadRefPraticien();
  if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }
  // Chargement du patient
  $patient = $_sejour->loadRefPatient();

  // Dossier médical
  $dossier_medical = $patient->loadRefDossierMedical(false);

  // Chargement des notes sur le séjourw
  $_sejour->loadRefsNotes();

  // Chargement des modes d'entrée
  $_sejour->loadRefEtablissementProvenance();

  // Chargement du praticien référent
  $_sejour->loadRefAdresseParPraticien();

  // Chargement de l'affectation
  $affectation = $_sejour->loadRefFirstAffectation();

  // Chargement des interventions
  $whereOperations = array("annulee" => "= '0'");
  $operations = $_sejour->loadRefsOperations($whereOperations);
  $operations_total += $operations;
}

// Optimisation du chargement des interventions
/** @var CConsultAnesth[] $dossiers_anesth_total */
$dossiers_anesth_total = array();

$ljoin = array(
  "consultation" => "consultation.consultation_id = consultation_anesth.consultation_id",
  "plageconsult" => "consultation.plageconsult_id = plageconsult.plageconsult_id"
);
CStoredObject::massLoadBackRefs($operations_total, "dossiers_anesthesie", "date DESC", null, $ljoin);

foreach ($operations_total as $operation) {
  $operation->loadRefsActes();
  $consult_anesth = $operation->loadRefsConsultAnesth();
  $dossiers_anesth_total[$consult_anesth->_id] = $consult_anesth;
}

// Optimisation du chargement des dossiers d'anesthésie
$consultations = CStoredObject::massLoadFwdRef($dossiers_anesth_total, "consultation_id");
CStoredObject::massLoadFwdRef($consultations, "plageconsult_id");
foreach ($dossiers_anesth_total as $dossier_anesth) {
  $consultation = $dossier_anesth->loadRefConsultation();
  $consultation->loadRefPlageConsult();
  $consultation->loadRefPraticien()->loadRefFunction();
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

$list_mode_entree = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_entree")) {
  $mode_entree = new CModeEntreeSejour();
  $where = array(
    "actif" => "= '1'",
  );
  $list_mode_entree = $mode_entree->loadGroupList($where);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hier"          , $hier);
$smarty->assign("demain"        , $demain);
$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->assign("date_demain"   , $date_demain);
$smarty->assign("date_actuelle" , $date_actuelle);
$smarty->assign("date"          , $date);
$smarty->assign("selAdmis"      , $selAdmis);
$smarty->assign("selSaisis"     , $selSaisis);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("sejours"       , $sejours);
$smarty->assign("prestations"   , CPrestation::loadCurrentList());
$smarty->assign("canAdmissions" , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"   , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));
$smarty->assign("functions"     , $functions);
$smarty->assign("filterFunction", $filterFunction);
$smarty->assign("period"        , $period);
$smarty->assign("list_mode_entree", $list_mode_entree);

$smarty->display("inc_vw_admissions.tpl");
