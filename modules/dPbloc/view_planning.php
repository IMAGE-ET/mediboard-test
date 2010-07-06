<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;
$ds = CSQLDataSource::get("std");

$can->needsRead();

$now       = mbDate();
$filter = new COperation;
$filter->_date_min     = CValue::get("_date_min", $now);
$filter->_date_max     = CValue::get("_date_max", $now);
$filter->_prat_id      = CValue::get("_prat_id");
$filter->_bloc_id      = CValue::get("_bloc_id");
$filter->salle_id      = CValue::get("salle_id");
$filter->_plage        = CValue::get("_plage");
$filter->_intervention = CValue::get("_intervention");
$filter->_specialite   = CValue::get("_specialite");
$filter->_codes_ccam   = CValue::get("_codes_ccam");
$filter->_ccam_libelle = CValue::get("_ccam_libelle", 1);
$filter->_planning_perso = CValue::get("planning_perso");
$_coordonnees  = CValue::get("_coordonnees");

$filterSejour = new CSejour;
$filterSejour->type = CValue::get("type");

// On sort les plages opératoires et les interventions hors plage
//  date - salle - horaires

$plagesop   = new CPlageOp();
$operations = new COperation();

$affectations_plage = array();

$wherePlagesop   = array();
$whereOperations = array();

$wherePlagesop["plagesop.date"]     =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);
$whereOperations["operations.date"] =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);

$user = new CMediusers();
$user->load($AppUI->user_id);

$praticien = new CMediusers();
$praticien->load($filter->_prat_id);

//dans le cas d'un anesthesiste, vider le prat_id si l'anesthesiste veut voir tous 
//les plannings sinon laisser son prat_id pour afficher son planning perso
if($praticien->isFromType(array("Anesthésiste"))&& !$filter->_planning_perso) {
  $filter->_prat_id = null;
}

// Filtre sur les praticiens ou les spécialités
$function = new CFunctions();
$functions = array();
$praticiens = array();
// Aucun filtre de séléctionné : tous les éléments auxquels on a le droit
if(!$filter->_specialite && !$filter->_prat_id) {
  if(!$user->isFromType(array("Anesthésiste")) && !$praticien->isFromType(array("Anesthésiste"))) {
    $functions  = $function->loadListWithPerms(PERM_READ);
    $praticiens = $user->loadPraticiens();
  } else {
    $functions = $function->loadList();
    $praticiens = $praticien->loadList();
  }
// Filtre sur la specialité : la spec et ses chirs primaires et secondaires
} elseif($filter->_specialite) {
  $function->load($filter->_specialite);
  $function->loadBackRefs("users");
  $function->loadBackRefs("secondary_functions");
  $functions[$function->_id] = $function;
  $praticiens = $function->_back["users"];
  foreach($function->_back["secondary_functions"] as $sec_func) {
    if(!isset($praticiens[$sec_func->user_id])) {
      $sec_func->loadRefUser();
      $praticiens[$sec_func->user_id] = $sec_func->_ref_user;
    }
  }
// Filtre sur le chir : le chir et ses specs primaires et secondaires
} elseif($filter->_prat_id) {
  $praticien->loadRefFunction();
  $praticien->loadBackRefs("secondary_functions");
  $praticiens[$praticien->_id] = $praticien;
  $functions[$praticien->function_id] = $praticien->_ref_function;
  foreach($praticien->_back["secondary_functions"] as $sec_func) {
    if(!isset($functions[$sec_func->function_id])) {
      $sec_func->loadRefFunction();
      $functions[$sec_func->function_id] = $sec_func->_ref_function;
    }
  }
}

// Liste des praticiens et fonctions à charger
$wherePlagesop[]                       = "plagesop.chir_id ".CSQLDataSource::prepareIn(array_keys($praticiens))." OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($functions));
$whereOperations["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($praticiens));

// En fonction de la salle
$salle = new CSalle();
$whereSalle = array();
$whereSalle["sallesbloc.bloc_id"] = CSQLDataSource::prepareIn(array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ)), $filter->_bloc_id);
if($filter->salle_id) {
  $whereSalle["sallesbloc.salle_id"] = "= $filter->salle_id";
}
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);
if($filter->salle_id || $filter->_bloc_id) {
  $whereOperations["operations.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
}

$whereOperations["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$wherePlagesop["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));

$orderPlagesop = "date, salle_id, debut";

$plagesop   = $plagesop->loadList($wherePlagesop, $orderPlagesop);

$ljoin = array();
$ljoin["sejour"] = "operations.sejour_id = sejour.sejour_id";
$where = array();
$where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($praticiens));
switch ($filter->_intervention) {
  case "1" : $where["operations.rank"] = "!= '0'"; break;
  case "2" : $where["operations.rank"] = "= '0'"; break;
}
if ($filter->_codes_ccam) {
  $where["operations.codes_ccam"]           = "LIKE '%$filter->_codes_ccam%'";
  $whereOperations["operations.codes_ccam"] = "LIKE '%$filter->_codes_ccam%'";
}
if($filterSejour->type) {
  $where["sejour.type"]           = "= '$filterSejour->type'";
  $whereOperations["sejour.type"] = "= '$filterSejour->type'";
}

$orderOperations = "date, salle_id, chir_id";

$operations = $operations->loadList($whereOperations, $orderOperations, null, null, $ljoin);

$order = "operations.rank, operations.horaire_voulu, sejour.entree_prevue";

$listDates = array();

// Operations de chaque plage
foreach($plagesop as &$plage) {
  $plage->loadRefsFwd(1);
  
  $where["operations.plageop_id"] = "= '$plage->_id'";
  
  $listOp = new COperation;
  $listOp = $listOp->loadList($where, $order, null, null, $ljoin);

  foreach($listOp as $keyOp => &$operation) {
    $operation->loadRefsFwd(1);
    $sejour =& $operation->_ref_sejour;
    $sejour->loadRefsFwd(1);   
    // On utilise la first_affectation pour contenir l'affectation courante du patient
    $sejour->_ref_first_affectation = $sejour->getCurrAffectation(mbDate($operation->_datetime));
    $affectation =& $sejour->_ref_first_affectation;
    if ($affectation->_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
  }
  if ((sizeof($listOp) == 0) && !$filter->_plage) {
    unset($plagesop[$plage->_id]);
  }
  $plage->_ref_operations = $listOp;
  
  // Chargement des affectation de la plage
  $plage->loadAffectationsPersonnel();
  
  // Initialisation des tableaux de stockage des affectation pour les op et les panseuses
  $affectations_plage[$plage->_id]["iade"]        = array();
	$affectations_plage[$plage->_id]["op"]          = array();
  $affectations_plage[$plage->_id]["op_panseuse"] = array();
  
  if (null !== $plage->_ref_affectations_personnel) {
  	$affectations_plage[$plage->_id]["iade"]        = $plage->_ref_affectations_personnel["iade"];
    $affectations_plage[$plage->_id]["op"]          = $plage->_ref_affectations_personnel["op"];
    $affectations_plage[$plage->_id]["op_panseuse"] = $plage->_ref_affectations_personnel["op_panseuse"];
  }
  
  $listDates[$plage->date][$plage->_id] = $plage;
}

foreach($operations as &$curr_op) {
  $curr_op->loadRefsFwd(1);
  $sejour =& $curr_op->_ref_sejour;
  $sejour->loadRefsFwd(1);   
  // On utilise la first_affectation pour contenir l'affectation courante du patient
  $sejour->_ref_first_affectation = $sejour->getCurrAffectation(mbDate($curr_op->_datetime));
  $affectation =& $sejour->_ref_first_affectation;
  if ($affectation->_id) {
    $affectation->loadRefsFwd();
    $affectation->_ref_lit->loadCompleteView();
  }
  $listDates[$curr_op->date]["hors_plage"][] = $curr_op;
}

ksort($listDates);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("filter"            , $filter);
$smarty->assign("_coordonnees"      , $_coordonnees);
$smarty->assign("listDates"         , $listDates);

$smarty->display("view_planning.tpl");

?>