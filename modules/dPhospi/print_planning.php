<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

CCanDo::checkRead();

$group = CGroups::loadCurrent();

$filter = new CSejour();
$filter->_date_min      = CValue::get("_date_min", CMbDT::date() ." 06:00:00");
$filter->_date_max      = CValue::get("_date_max", CMbDT::date() ." 21:00:00");
$filter->_horodatage    = CValue::get("_horodatage", "entree_prevue");
$filter->_service       = CValue::get("_service", 0);
$filter->_filter_type   = CValue::get("_filter_type", 0);
$filter->praticien_id   = CValue::get("praticien_id", 0);
$filter->_specialite    = CValue::get("_specialite", 0);
$filter->convalescence  = CValue::get("convalescence", 0);
$filter->consult_accomp = CValue::get("consult_accomp", 0);
$filter->_admission     = CValue::get("_admission", "heure");
$filter->_ccam_libelle  = CValue::get("_ccam_libelle", "1");
$filter->_coordonnees   = CValue::get("_coordonnees", 0);
$filter->_notes         = CValue::get("_notes", 0);
$filter->_nb_days       = CValue::get("_nb_days", 0);
$filter->_by_date       = CValue::get("_by_date", 0);

if ($filter->_nb_days) {
  $filter->_date_max = CMbDT::date("+$filter->_nb_days days", CMbDT::date($filter->_date_min)) . " 21:00:00";
}

$filter->_service     = explode(",", $filter->_service);
$filter->praticien_id = explode(",", $filter->praticien_id);
CMbArray::removeValue(0, $filter->praticien_id);
CMbArray::removeValue(0, $filter->_service);

$total   = 0;

$sejours = new CSejour();

$sejourReq = new CRequest();

$sejourReq->addLJoinClause("patients", "patients.patient_id = sejour.patient_id");
$sejourReq->addLJoinClause("users", "users.user_id = sejour.praticien_id");

$sejourReq->addWhereClause("sejour.$filter->_horodatage", "BETWEEN '$filter->_date_min' AND '$filter->_date_max'");
$sejourReq->addWhereClause("sejour.group_id", "= '$group->_id'");
$sejourReq->addWhereClause("sejour.annule", "= '0'");

// On supprime les sejours d'urgence
$sejourReq->addWhereClause("sejour.type", "!= 'urg'");


// Clause de filtre par spécialité / chir
if ($filter->_specialite or $filter->praticien_id) {
  $speChirs = new CMediusers();
  $speChirs = $speChirs->loadList(array ("function_id" => "= '$filter->_specialite'"));

  if (count($filter->praticien_id)) {
    $sejourReq->addWhereClause("sejour.praticien_id", CSQLDataSource::prepareIn($filter->praticien_id));
  }
  else {
    $sejourReq->addWhereClause("sejour.praticien_id", CSQLDataSource::prepareIn(array_keys($speChirs)));
  }
}

if ($filter->_filter_type) {
  $sejourReq->addWhereClause("sejour.type", "= '$filter->_filter_type'");
}

if ($filter->convalescence == "o") {
  $sejourReq->addWhereClause(null, "(sejour.convalescence IS NOT NULL AND sejour.convalescence != '')");
}

if ($filter->convalescence == "n") {
  $sejourReq->addWhereClause(null, "(sejour.convalescence IS NULL OR sejour.convalescence = '')");
}

if ($filter->consult_accomp) {
  $sejourReq->addWhereClause(null, "(sejour.consult_accomp = '".$filter->consult_accomp."')");
}

$sejourReq->addOrder("DATE(sejour.$filter->_horodatage)");
$sejourReq->addOrder("users.user_last_name");
$sejourReq->addOrder("users.user_first_name");

if ($filter->_admission  == "heure") {
  $sejourReq->addOrder("TIME(sejour.$filter->_horodatage)");
}
else {
  $sejourReq->addOrder("patients.nom");
  $sejourReq->addOrder("patients.prenom");
  $sejourReq->addOrder("DATE(sejour.$filter->_horodatage)");
}

$sejours = $sejours->loadListByReq($sejourReq);

$listDays = array();
$listPrats = array();

// Liste des services
$service = new CService();
$where = array();
$where["group_id"]  = "= '$group->_id'";
$where["cancelled"] = "= '0'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ, $where, $order);

$prestation_id = CAppUI::pref("prestation_id_hospi");

if (CAppUI::conf("dPhospi systeme_prestations") == "standard" || $prestation_id == "all") {
  $prestation_id = "";
}

$prestation = new CPrestationJournaliere();
$prestation->load($prestation_id);

CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($sejours, "praticien_id");

// ATTENTION ne pas supprimer le "&" car pose des problemes
foreach ($sejours as $key => &$sejour) {
  /** @var CSejour $sejour*/
  $sejour->loadRefsAffectations();
  $sejour->loadRefsOperations();
  $sejour->loadRefPatient();
  $sejour->_ref_first_affectation->loadRefLit()->loadRefChambre();
  $affectation = $sejour->_ref_first_affectation;
  $affectation->_ref_lit->loadCompleteView();

  if ($prestation_id) {
    $sejour->loadLiaisonsForPrestation($prestation_id);
  }

  $service_id = $affectation->service_id ? $affectation->service_id : $affectation->_ref_lit->_ref_chambre->service_id;
  if (count($filter->_service) && !in_array($service_id, $filter->_service)) {
    unset($sejours[$key]);
    continue;
  }
  elseif (!$filter->_service && $affectation->_id && !in_array($service_id, array_keys($services))) {
    unset($sejours[$key]);
    continue;
  }
  $sejour->loadRefPraticien();

  foreach ($sejour->_ref_operations as $operation) {
    $operation->loadRefsFwd();
  }

  if ($filter->_notes) {
    $sejour->loadRefsNotes();
    foreach ($sejour->_ref_notes as $_id => $_note) {
      if (!$_note->public) {
        unset($sejour->_ref_notes[$_id]);
      }
    }
  }

  $curr_date = CMbDT::date(null, $sejour->{$filter->_horodatage});
  $curr_prat = $sejour->praticien_id;
  $listDays[$curr_date][$curr_prat]["praticien"] =& $sejour->_ref_praticien;
  $listDays[$curr_date][$curr_prat]["sejours"][] =& $sejour;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"    , $filter);
$smarty->assign("listDays"  , $listDays);
$smarty->assign("listPrats" , $listPrats);
$smarty->assign("total"     , count($sejours));
$smarty->assign("prestation", $prestation);

$smarty->display("print_planning.tpl");