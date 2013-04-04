<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

$now       = mbDate();
$filter = new COperation;
$filter->_date_min       = CValue::get("_date_min", $now);
$filter->_date_max       = CValue::get("_date_max", $now);
$filter->_prat_id        = CValue::get("_prat_id");
$filter->_bloc_id        = CValue::get("_bloc_id");
$filter->salle_id        = CValue::get("salle_id");
$filter->_plage          = CValue::get("_plage", CAppUI::conf("dPbloc CPlageOp plage_vide"));
$filter->_ranking        = CValue::get("_ranking");
$filter->_specialite     = CValue::get("_specialite");
$filter->_codes_ccam     = CValue::get("_codes_ccam");
$filter->exam_extempo    = CValue::get("exam_extempo");
$filter->_ccam_libelle   = CValue::get("_ccam_libelle", CAppUI::conf("dPbloc CPlageOp libelle_ccam"));
$filter->_planning_perso = CValue::get("planning_perso");
$_coordonnees            = CValue::get("_coordonnees");
$_print_numdoss          = CValue::get("_print_numdoss");
$_print_ipp              = CValue::get("_print_ipp");
$_print_annulees         = CValue::get("_print_annulees");

if (is_array($filter->_bloc_id)) {
  CMbArray::removeValue("0", $filter->_bloc_id);
}

$filterSejour = new CSejour;
$filterSejour->type = CValue::get("type");

$group = CGroups::loadCurrent();

// On sort les plages opératoires et les interventions hors plage
//  date - salle - horaires

$plagesop   = new CPlageOp();
$operation = new COperation();

$numOp = 0;

$affectations_plage = array();

$wherePlagesop   = array();
$whereOperations = array();

$wherePlagesop["plagesop.date"]     =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);
$whereOperations["operations.date"] =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);

$user = CMediusers::get();

$praticien = new CMediusers();
$praticien->load($filter->_prat_id);

// dans le cas d'un anesthesiste, vider le prat_id si l'anesthesiste veut voir tous
// les plannings sinon laisser son prat_id pour afficher son planning perso
if ($praticien->isFromType(array("Anesthésiste"))&& !$filter->_planning_perso) {
  $filter->_prat_id = null;
}

// Filtre sur les praticiens ou les spécialités
$function = new CFunctions();
$functions = array();
$praticiens = array();
// Aucun filtre de séléctionné : tous les éléments auxquels on a le droit
if (!$filter->_specialite && !$filter->_prat_id) {
  if (!$user->isFromType(array("Anesthésiste")) && !$praticien->isFromType(array("Anesthésiste"))) {
    $functions  = $function->loadListWithPerms(PERM_READ);
    $praticiens = $user->loadPraticiens();
  }
  else {
    $functions = $function->loadList();
    $praticiens = $praticien->loadList();
  }
// Filtre sur la specialité : la spec et ses chirs primaires et secondaires
}
elseif ($filter->_specialite) {
  $function->load($filter->_specialite);
  $function->loadBackRefs("users");
  $function->loadBackRefs("secondary_functions");
  $functions[$function->_id] = $function;
  $praticiens = $function->_back["users"];
  foreach ($function->_back["secondary_functions"] as $sec_func) {
    if (!isset($praticiens[$sec_func->user_id])) {
      $sec_func->loadRefUser();
      $praticiens[$sec_func->user_id] = $sec_func->_ref_user;
    }
  }
// Filtre sur le chir : le chir et ses specs primaires et secondaires
}
elseif ($filter->_prat_id) {
  $praticien->loadRefFunction();
  $praticien->loadBackRefs("secondary_functions");
  $praticiens[$praticien->_id] = $praticien;
  $functions[$praticien->function_id] = $praticien->_ref_function;
  foreach ($praticien->_back["secondary_functions"] as $sec_func) {
    if (!isset($functions[$sec_func->function_id])) {
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

$whereSalle["sallesbloc.bloc_id"] =
  CSQLDataSource::prepareIn(count($filter->_bloc_id) ?
    $filter->_bloc_id :
    array_keys($group->loadBlocs(PERM_READ)));

if ($filter->salle_id) {
  $whereSalle["sallesbloc.salle_id"] = "= $filter->salle_id";
}
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);
if ($filter->salle_id || $filter->_bloc_id) {
  $whereOperations["operations.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
}

$whereOperations["sejour.group_id"] = "= '".$group->_id."'";

$wherePlagesop["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));

$orderPlagesop = "date, salle_id, debut";

$plagesop   = $plagesop->loadList($wherePlagesop, $orderPlagesop);

$ljoin = array();
$ljoin["sejour"] = "operations.sejour_id = sejour.sejour_id";
$where = array();
$where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($praticiens));
if (!$_print_annulees) {
  $where["operations.annulee"] = "= '0'";
  $whereOperations["operations.annulee"] = "= '0'";
}

switch ($filter->_ranking) {
  case "ok" :
    $where["operations.rank"] = "!= '0'";
    break;
  case "ko" :
    $where["operations.rank"] = "= '0'";
}

if ($filter->_codes_ccam) {
  $where["operations.codes_ccam"]           = "LIKE '%$filter->_codes_ccam%'";
  $whereOperations["operations.codes_ccam"] = "LIKE '%$filter->_codes_ccam%'";
}
if ($filter->exam_extempo) {
  $where["operations.exam_extempo"]           = "= '1'";
  $whereOperations["operations.exam_extempo"] = "= '1'";
}
if ($filterSejour->type) {
  $where["sejour.type"]           = "= '$filterSejour->type'";
  $whereOperations["sejour.type"] = "= '$filterSejour->type'";
}

$orderOperations = "date, salle_id, chir_id";

$operations = $operation->loadList($whereOperations, $orderOperations, null, null, $ljoin);
CMbObject::massLoadFwdRef($operations, "plageop_id");
CMbObject::massLoadFwdRef($operations, "sejour_id");
CMbObject::massLoadFwdRef($operations, "chir_id");

$order = "operations.rank, operations.horaire_voulu, sejour.entree_prevue";

$listDates = array();

// Operations de chaque plage
foreach ($plagesop as &$plage) {
  $plage->loadRefsFwd(1);

  $where["operations.plageop_id"] = "= '$plage->_id'";

  $listOp = new COperation();
  $listOp = $listOp->loadList($where, $order, null, null, $ljoin);

  $chirs   = CMbObject::massLoadFwdRef($listOp, "chir_id");
  $sejours = CMbObject::massLoadFwdRef($listOp, "sejour_id");
  CMbObject::massLoadFwdRef($sejours, "patient_id");

  foreach ($listOp as $operation) {
    $operation->loadRefPlageOp(1);
    $operation->loadRefsConsultAnesth();
    $operation->loadRefPraticien(1);
    $operation->loadExtCodesCCAM();
    $operation->updateHeureUS();
    $operation->updateSalle();
    $operation->loadAffectationsPersonnel();
    $sejour = $operation->loadRefSejour(1);
    $sejour->loadRefsFwd(1);
    if ($_print_ipp) {
      $sejour->_ref_patient->loadIPP();
    }
    if ($_print_numdoss) {
      $sejour->loadNDA();
    }
    
    // Chargement de l'affectation
    $affectation = $operation->getAffectation();
    
    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }
    $sejour->_ref_first_affectation = $affectation;
  }
  if ((count($listOp) == 0) && !$filter->_plage) {
    unset($plagesop[$plage->_id]);
    continue;
  }
  $plage->_ref_operations = $listOp;
  $numOp += count($listOp);

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

foreach ($operations as $operation) {
  $operation->loadRefPlageOp(1);
  $operation->loadRefsConsultAnesth();
  $operation->loadRefPraticien(1);
  $operation->loadExtCodesCCAM();
  $operation->updateHeureUS();
  $operation->loadAffectationsPersonnel();
  $sejour = $operation->loadRefSejour(1);
  $sejour->loadRefsFwd(1);
  if ($_print_numdoss) {
    $sejour->loadNDA();
  }
  if ($_print_ipp) {
    $sejour->_ref_patient->loadIPP();
  }

  // Chargement de l'affectation
  $affectation = $operation->getAffectation();
  
  if ($affectation->_id) {
    $affectation->loadRefLit()->loadCompleteView();
  }
  $sejour->_ref_first_affectation = $affectation;

  $listDates[$operation->date]["hors_plage"][] = $operation;
}

$numOp += count($operations);

ksort($listDates);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("filter"            , $filter);
$smarty->assign("_coordonnees"      , $_coordonnees);
$smarty->assign("_print_numdoss"    , $_print_numdoss);
$smarty->assign("_print_ipp"        , $_print_ipp);
$smarty->assign("listDates"         , $listDates);
$smarty->assign("operations"        , $operations);
$smarty->assign("numOp"             , $numOp);

$smarty->display("view_planning.tpl");
