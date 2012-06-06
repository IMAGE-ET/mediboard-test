<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$type           = CValue::getOrSession("type");
$service_id     = CValue::getOrSession("service_id");
$prat_id        = CValue::getOrSession("prat_id");
$selSortis      = CValue::getOrSession("selSortis", "0");
$order_col      = CValue::getOrSession("order_col", "patient_id");
$order_way      = CValue::getOrSession("order_way", "ASC");
$date           = CValue::getOrSession("date", mbDate());
$next           = mbDate("+1 DAY", $date);
$filterFunction = CValue::getOrSession("filterFunction");

$service_id = explode(",", $service_id);
CMbArray::removeValue("", $service_id);

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:00", $date);

// Sorties de la journée
$sejour = new CSejour;

$group = CGroups::loadCurrent();

// Lien avec les patients et les praticiens
$ljoin["patients"]    = "sejour.patient_id = patients.patient_id";
$ljoin["users"]       = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if (count($service_id)) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie_prevue";
  $ljoin["lit"]                = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"]            = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"]            = "chambre.service_id = service.service_id";
  $where["service.service_id"] = CSQLDataSource::prepareIn($service_id);
}

// Filtre sur le type du séjour
if($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($type) {
  $where["sejour.type"] = " = '$type'";
} else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}
$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.sortie"]   = "BETWEEN '$date' AND '$next'";
$where["sejour.annule"]   = "= '0'";

if ($selSortis != "0") {
  $where[] = "(sortie_reelle IS NULL)";
}


if ($order_col != "patient_id" && $order_col != "entree_prevue" && $order_col != "praticien_id"){
  $order_col = "patient_id";  
}

if ($order_col == "patient_id"){
  $order = "patients.nom $order_way, patients.prenom $order_way, sejour.sortie_prevue";
}

if ($order_col == "entree_prevue"){
  $order = "sejour.sortie_prevue $order_way, patients.nom, patients.prenom";
}

if ($order_col == "praticien_id"){
  $order = "users.user_last_name $order_way, users.user_first_name";
}

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CMbObject::massLoadFwdRef($praticiens, "function_id");

$maternite_active = CModule::getActive("maternite");

foreach ($sejours as $sejour_id => $_sejour) {
  // Filtre sur la fonction du praticien
  $praticien = $_sejour->loadRefPraticien(1);
	if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }
  
  // Chargement du patient
  $_sejour->loadRefPatient(1)->loadIPP();
  
  // Chargment du numéro de dossier
  $_sejour->loadNDA();
  
  // Chargements des notes sur le séjour
  $_sejour->loadRefsNotes();

  // Chargement des interventions
  $whereOperations = array("annulee" => "= '0'");
  $_sejour->loadRefsOperations($whereOperations);
  $operation = new COperation();
  foreach ($_sejour->_ref_operations as $operation) {
    $operation->loadRefsActes();
    $operation->loadRefsConsultAnesth();
    $consult_anesth =& $operation->_ref_consult_anesth; 
    $consult_anesth->loadRefConsultation();
    $consult_anesth->_ref_consultation->loadRefPlageConsult(1);
    $consult_anesth->_date_consult =& $consult_anesth->_ref_consultation->_date;
  }

  // Chargement des affectation
  $_sejour->loadRefsAffectations();
  foreach($_sejour->_ref_affectations as $_aff) {
    if ($_aff->_id) {
      $_aff->loadRefLit()->loadCompleteView();
    }
  }
  
  if ($maternite_active && $_sejour->grossesse_id) {
    $_sejour->_sejours_enfants_ids = CMbArray::pluck($_sejour->loadRefsNaissances(), "sejour_enfant_id");
  }
  
  // Chargement des modes de sortie
  $_sejour->loadRefEtablissementTransfert();
  $_sejour->loadRefServiceMutation();  
}

// Si la fonction selectionnée n'est pas dans la liste des fonction, on la rajoute
if ($filterFunction && !array_key_exists($filterFunction, $functions)){
  $_function = new CFunctions();
  $_function->load($filterFunction);
  $functions[$filterFunction] = $_function;
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
$smarty->assign("type"          , $type);
$smarty->assign("selSortis"     , $selSortis);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("sejours"       , $sejours);
$smarty->assign("canAdmissions" , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"   , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));
$smarty->assign("functions"     , $functions);
$smarty->assign("filterFunction", $filterFunction);

$smarty->display("inc_vw_sorties.tpl");

?>