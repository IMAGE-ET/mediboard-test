<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

// Type d'admission
$type           = CValue::getOrSession("type");
$service_id     = CValue::getOrSession("service_id");
$prat_id        = CValue::getOrSession("prat_id");
$selAdmis       = CValue::getOrSession("selAdmis", "0");
$selSaisis      = CValue::getOrSession("selSaisis", "0");
$order_col      = CValue::getOrSession("order_col", "patient_id");
$order_way      = CValue::getOrSession("order_way", "ASC");
$date           = CValue::getOrSession("date", mbDate());
$filterFunction = CValue::getOrSession("filterFunction");
$period         = CValue::getOrSession("period");

$service_id = explode(",", $service_id);
CMbArray::removeValue("", $service_id);

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00", "+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:59", $date);

if ($period) {
  $hour = CAppUI::conf("dPadmissions hour_matin_soir");
  // Matin
  if ($period == "matin") {
    $date_max = mbDateTime($hour, $date);
  }
  // Soir
  else {
    $date_min = mbDateTime($hour, $date);
  }
}

// Entrées de la journée
$sejour = new CSejour;

$group = CGroups::loadCurrent();

// Lien avec les patients et les praticiens
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if (count($service_id)) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie";
  $ljoin["lit"]                = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"]            = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"]            = "chambre.service_id = service.service_id";
  $in_services = CSQLDataSource::prepareIn($service_id);
  $where[] = "service.service_id $in_services OR affectation.service_id $in_services";
}

// Filtre sur le type du séjour
if ($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
  $where["sejour.type"] = " = '$type'";
}
else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.entree"]   = "BETWEEN '$date_min' AND '$date_max'";
$where["sejour.annule"]   = "= '0'";

if ($selAdmis != "0") {
  $where[] = "(entree_reelle IS NULL OR entree_reelle = '0000-00-00 00:00:00')";
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

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

// Mass preloading
CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CMbObject::massLoadFwdRef($praticiens, "function_id");

// Chargement optimisée des prestations
CSejour::massCountPrestationSouhaitees($sejours);

foreach ($sejours as $sejour_id => $_sejour) {
  $praticien = $_sejour->loadRefPraticien(1);
  if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }

  // Chargement du patient
  $patient = $_sejour->loadRefPatient(true);
  $patient->loadIPP();

  // Dossier médical
  $dossier_medical = $patient->loadRefDossierMedical(false);

  if (CAppUI::conf("dPadmissions show_deficience")) {
    $deficiences = $dossier_medical->loadRefsDeficiences();
    $dossier_medical->_ref_antecedents_by_type["deficience"] = $deficiences;
  }

  // Chargement du numéro de dossier
  $_sejour->loadNDA();

  // Chargement des notes sur le séjour
  $_sejour->loadRefsNotes();

  // Chargement des modes d'entrée
  $_sejour->loadRefEtablissementProvenance();

  // Chargement des interventions
  $whereOperations = array("annulee" => "= '0'");
  $_sejour->loadRefsOperations($whereOperations);
  foreach ($_sejour->_ref_operations as $operation) {
    $operation->loadRefsActes();
    $consult_anesth = $operation->loadRefsConsultAnesth();
    $consultation = $consult_anesth->loadRefConsultation();
    $consultation->loadRefPlageConsult(1);
    $consult_anesth->_date_consult = $consultation->_date;
  }

  // Chargement de l'affectation
  $affectation = $_sejour->loadRefFirstAffectation();
  if ($affectation->_id) {
    $affectation->loadRefLit(1)->loadCompleteView();
  }    
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
