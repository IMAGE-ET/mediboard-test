<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

CAppUI::requireModuleFile($m, "inc_vw_affectations");

$can->needsRead();
$ds = CSQLDataSource::get("std");
$filter = new CSejour();
$filter->_date_min     = mbGetValueFromGet("_date_min", mbDate() ." 06:00:00");
$filter->_date_max     = mbGetValueFromGet("_date_max", mbDate() ." 21:00:00");
$filter->_horodatage   = mbGetValueFromGet("_horodatage", "entree_prevue");
$filter->_service      = mbGetValueFromGet("_service", 0);
$filter->_filter_type  = mbGetValueFromGet("_filter_type", 0);
$filter->praticien_id  = mbGetValueFromGet("praticien_id", 0);
$filter->_specialite   = mbGetValueFromGet("_specialite", 0);
$filter->convalescence = mbGetValueFromGet("convalescence", 0);
$filter->_admission    = mbGetValueFromGet("_admission", "heure");
$filter->_ccam_libelle = mbGetValueFromGet("_ccam_libelle", "1");
$filter->_coordonnees  = mbGetValueFromGet("_coordonnees", 0);

$total   = 0;

$sejours = new CSejour;

$sejourReq = new CRequest;

$sejourReq->addLJoinClause("patients", "patients.patient_id = sejour.patient_id");
$sejourReq->addLJoinClause("users", "users.user_id = sejour.praticien_id");

$sejourReq->addWhereClause("sejour.$filter->_horodatage", "BETWEEN '$filter->_date_min' AND '$filter->_date_max'");
$sejourReq->addWhereClause("sejour.group_id", "= '$g'");
$sejourReq->addWhereClause("sejour.annule", "= '0'");

// On supprime les sejours d'urgence
$sejourReq->addWhereClause("sejour.type", "!= 'urg'");


// Clause de filtre par spécialité / chir
if ($filter->_specialite or $filter->praticien_id) {
  $speChirs = new CMediusers;
  $speChirs = $speChirs->loadList(array ("function_id" => "= '$filter->_specialite'"));
  $sejourReq->addWhereClause("sejour.praticien_id", $ds->prepareIn(array_keys($speChirs), $filter->praticien_id));
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
$services = new CService;
$where = array();
$where["group_id"] = "= '$g'";
$order = "nom";
$services = $services->loadListWithPerms(PERM_READ,$where, $order);

foreach ($sejours as $key => &$sejour) {
  $sejour->loadRefsAffectations();
  $sejour->loadRefsOperations();
  $sejour->loadRefPatient();
  $sejour->_ref_first_affectation->loadRefLit();
  $affectation =& $sejour->_ref_first_affectation;
  $affectation->_ref_lit->loadCompleteView();

  if ($filter->_service  && ($affectation->_ref_lit->_ref_chambre->service_id != $filter->_service)) {
    unset($sejours[$key]);
    continue;
  }elseif(!$filter->_service && $affectation->_id && !in_array($affectation->_ref_lit->_ref_chambre->service_id, array_keys($services))){
    unset($sejours[$key]);
    continue;
  } 

  $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);

  foreach($sejour->_ref_operations as &$operation) {
    $operation->loadRefsFwd();
  }

  $curr_date = mbDate(null, $sejour->{$filter->_horodatage});
  $curr_prat = $sejour->praticien_id;
  $listDays[$curr_date][$curr_prat]["praticien"] =& $sejour->_ref_praticien;
  $listDays[$curr_date][$curr_prat]["sejours"][] =& $sejour;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter" 	 , $filter	      );
$smarty->assign("listDays" , $listDays      );
$smarty->assign("listPrats", $listPrats     );
$smarty->assign("total"    , count($sejours));

$smarty->display("print_planning.tpl");

?>