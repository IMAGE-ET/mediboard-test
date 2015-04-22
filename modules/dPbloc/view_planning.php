<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

$now    = CMbDT::date();
$filter = new COperation();
$filter->_datetime_min   = CValue::get("_datetime_min", "$now 00:00:00");
$filter->_datetime_max   = CValue::get("_datetime_max", "$now 23:59:59");
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
$filter->_nb_days        = CValue::get("_nb_days", 0);
$filter->_by_prat        = CValue::get("_by_prat");

$_coordonnees            = CValue::get("_coordonnees");
$_print_numdoss          = CValue::get("_print_numdoss");
$_print_ipp              = CValue::get("_print_ipp");
$_print_annulees         = CValue::get("_print_annulees");
$_materiel               = CValue::get("_materiel", CAppUI::conf("dPbloc CPlageOp view_materiel"));
$_missing_materiel       = CValue::get("_missing_materiel", CAppUI::conf("dPbloc CPlageOp view_missing_materiel"));
$_extra                  = CValue::get("_extra", CAppUI::conf("dPbloc CPlageOp view_extra"));
$_duree                  = CValue::get("_duree", CAppUI::conf("dPbloc CPlageOp view_duree"));
$_convalescence          = CValue::get('_convalescenve', CAppUI::conf('dPbloc CPlageOp view_convalescence'));
$_hors_plage             = CValue::get("_hors_plage");
$_show_comment_sejour    = CValue::get("_show_comment_sejour");
$_compact                = CValue::get('_compact', 0);
$_show_identity          = CValue::get('_show_identity', 1);

if ($filter->_nb_days) {
  $filter->_datetime_max = CMbDT::date("+$filter->_nb_days days", CMbDT::date($filter->_datetime_min)) . " 21:00:00";
}

$no_consult_anesth       = CValue::get("no_consult_anesth");

if (is_array($filter->_bloc_id)) {
  CMbArray::removeValue("0", $filter->_bloc_id);
}

$filterSejour = new CSejour();
$filterSejour->type = CValue::get("type");

$group = CGroups::loadCurrent();

// On sort les plages opératoires et les interventions hors plage
//  date - salle - horaires

$numOp = 0;

$affectations_plage = array();

$wherePlagesop   = array();
$whereOperations = array();

$wherePlagesop["plagesop.date"]     =  $ds->prepare("BETWEEN %1 AND %2", CMbDT::date($filter->_datetime_min), CMbDT::date($filter->_datetime_max));
$whereOperations["operations.date"] =  $ds->prepare("BETWEEN %1 AND %2", CMbDT::date($filter->_datetime_min), CMbDT::date($filter->_datetime_max));
$whereOperations["operations.plageop_id"] = "IS NULL";

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
}
elseif ($filter->_specialite) {
  // Filtre sur la specialité : la spec et ses chirs primaires et secondaires
  $function->load($filter->_specialite);
  $function->loadBackRefs("users");
  $function->loadBackRefs("secondary_functions");
  $functions[$function->_id] = $function;
  $praticiens = $function->_back["users"];
  /** @var CSecondaryFunction $sec_func */
  foreach ($function->_back["secondary_functions"] as $sec_func) {
    if (!isset($praticiens[$sec_func->user_id])) {
      $sec_func->loadRefUser();
      $praticiens[$sec_func->user_id] = $sec_func->_ref_user;
    }
  }
}
elseif ($filter->_prat_id) {
  // Filtre sur le chir : le chir et ses specs primaires et secondaires
  $praticien->loadRefFunction();
  $praticien->loadBackRefs("secondary_functions");
  $praticiens[$praticien->_id] = $praticien;
  $functions[$praticien->function_id] = $praticien->_ref_function;
  /** @var CSecondaryFunction $sec_func */
  foreach ($praticien->_back["secondary_functions"] as $sec_func) {
    if (!isset($functions[$sec_func->function_id])) {
      $sec_func->loadRefFunction();
      $functions[$sec_func->function_id] = $sec_func->_ref_function;
    }
  }
}

// Liste des praticiens et fonctions à charger
$wherePlagesop[] = "plagesop.chir_id ".
  CSQLDataSource::prepareIn(array_keys($praticiens)).
  " OR plagesop.spec_id ".
  CSQLDataSource::prepareIn(array_keys($functions));
$whereOperations["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($praticiens));

// En fonction de la salle
$salle = new CSalle();
$whereSalle = array();

$whereSalle["sallesbloc.bloc_id"] = CSQLDataSource::prepareIn(
  count($filter->_bloc_id) ?
  $filter->_bloc_id :
  array_keys($group->loadBlocs(PERM_READ))
);

if ($filter->salle_id) {
  $whereSalle["sallesbloc.salle_id"] = "= '$filter->salle_id'";
}
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);
if ($filter->salle_id || $filter->_bloc_id) {
  $whereOperations["operations.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
}

$whereOperations["sejour.group_id"] = "= '".$group->_id."'";

$wherePlagesop["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));

$orderPlagesop = "date, salle_id, debut";

$plageop   = new CPlageOp();
/** @var CPlageOp[] $plagesop */
$plagesop = $plageop->loadList($wherePlagesop, $orderPlagesop);

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

$orderOperations = "date, salle_id, time_operation, chir_id";

$operation = new COperation();
/** @var COperation[] $operations */
$operations = $operation->loadList($whereOperations, $orderOperations, null, null, $ljoin);
CStoredObject::massLoadFwdRef($operations, "plageop_id");
CStoredObject::massLoadFwdRef($operations, "sejour_id");
CStoredObject::massLoadFwdRef($operations, "chir_id");

$order = "operations.rank, operations.horaire_voulu, sejour.entree_prevue";

$listDates = array();
$listDatesByPrat = array();
$listPrats = array();

$prestation_id = CAppUI::pref("prestation_id_hospi");

if (CAppUI::conf("dPhospi systeme_prestations") == "standard" || $prestation_id == "all") {
  $prestation_id = "";
}

$prestation = new CPrestationJournaliere();
$prestation->load($prestation_id);

$format_print = CAppUI::conf("dPbloc printing format_print", $group);

$ordre_passage_temp = array();
$ordre_passage = array();

// Operations de chaque plage
foreach ($plagesop as $plage) {
  $plage->loadRefsFwd(1);

  $where["operations.plageop_id"] = "= '$plage->_id'";

  $op = new COperation();
  /** @var COperation[] $listOp */
  $listOp = $op->loadList($where, $order, null, null, $ljoin);

  $chirs   = CStoredObject::massLoadFwdRef($listOp, "chir_id");
  $sejours = CStoredObject::massLoadFwdRef($listOp, "sejour_id");
  CStoredObject::massLoadFwdRef($sejours, "patient_id");

  foreach ($listOp as $key=>$operation) {
    $operation->loadRefPlageOp();
    if ($operation->_datetime_best < $filter->_datetime_min ||
      $operation->_datetime_best > $filter->_datetime_max) {
      unset($listOp[$key]);
      continue;
    }
    $operation->loadRefsConsultAnesth();
    if ($no_consult_anesth && $operation->_ref_consult_anesth->_id) {
      unset($listOp[$operation->_id]);
    }
    $operation->loadRefPraticien();
    $operation->loadExtCodesCCAM();
    $operation->updateHeureUS();
    $operation->updateSalle();
    $operation->loadAffectationsPersonnel();
    $sejour = $operation->loadRefSejour();
    $sejour->loadRefsFwd();
    if ($_print_ipp) {
      $sejour->_ref_patient->loadIPP();
    }
    if ($_print_numdoss) {
      $sejour->loadNDA();
    }

    if ($prestation_id) {
      $sejour->loadLiaisonsForPrestation($prestation_id);
    }

    if ($format_print == "advanced") {
      $operation->_liaisons_prestation = implode("|", $sejour->loadAllLiaisonsForDay(CMbDT::date($operation->_datetime_best)));
      $ordre_passage_temp[$operation->chir_id][CMbDT::date($operation->_datetime)][$operation->_id] = $operation;
    }

    // Chargement de l'affectation
    $affectation = $operation->getAffectation();

    if ($affectation->_id) {
      $affectation->loadRefLit()->loadCompleteView();
    }
    $sejour->_ref_first_affectation = $affectation;

    // Chargement des ressources si gestion du materiel en mode expert
    if ($_materiel && CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert") {
      $operation->loadRefsBesoins();
      foreach ($operation->_ref_besoins as $_besoin) {
        $_besoin->_available = $_besoin->isAvailable();
      }
    }
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
  $affectations_plage[$plage->_id]["sagefemme"]   = array();
  $affectations_plage[$plage->_id]["manipulateur"] = array();

  if (null !== $plage->_ref_affectations_personnel) {
    $affectations_plage[$plage->_id]["iade"]        = $plage->_ref_affectations_personnel["iade"];
    $affectations_plage[$plage->_id]["op"]          = $plage->_ref_affectations_personnel["op"];
    $affectations_plage[$plage->_id]["op_panseuse"] = $plage->_ref_affectations_personnel["op_panseuse"];
    $affectations_plage[$plage->_id]["sagefemme"]   = $plage->_ref_affectations_personnel["sagefemme"];
    $affectations_plage[$plage->_id]["manipulateur"]= $plage->_ref_affectations_personnel["manipulateur"];
  }

  $listDates[$plage->date][$plage->_id] = $plage;

  if ($filter->_by_prat) {
    foreach ($plage->_ref_operations as $_op) {
      $listPrats[$_op->chir_id] = $_op->_ref_chir;
      $listDatesByPrat[$plage->date][$_op->chir_id][$_op->_id] = $_op;
    }
  }
}

foreach ($operations as $key => $operation) {
  $operation->loadRefPlageOp();

  if ($operation->_datetime_best < $filter->_datetime_min ||
    $operation->_datetime_best > $filter->_datetime_max) {
    unset($operations[$key]);
    continue;
  }
  $operation->loadRefsConsultAnesth();
  if ($no_consult_anesth && $operation->_ref_consult_anesth->_id) {
    unset($operations[$operation->_id]);
    continue;
  }
  $operation->loadRefPraticien();
  $operation->loadExtCodesCCAM();
  $operation->updateHeureUS();
  $operation->loadAffectationsPersonnel();
  $sejour = $operation->loadRefSejour();
  $sejour->loadRefsFwd();
  if ($_print_numdoss) {
    $sejour->loadNDA();
  }
  if ($_print_ipp) {
    $sejour->_ref_patient->loadIPP();
  }

  if ($prestation_id) {
    $sejour->loadLiaisonsForPrestation($prestation_id);
  }

  if ($format_print == "advanced") {
    $operation->_liaisons_prestation = implode("|", $sejour->loadAllLiaisonsForDay(CMbDT::date($operation->_datetime_best)));
    $ordre_passage_temp[$operation->chir_id][CMbDT::date($operation->_datetime)][$operation->_id] = $operation;
  }

  // Chargement de l'affectation
  $affectation = $operation->getAffectation();

  if ($affectation->_id) {
    $affectation->loadRefLit()->loadCompleteView();
  }
  $sejour->_ref_first_affectation = $affectation;

  // Chargement des ressources si gestion du materiel en mode expert
  if ($_materiel && CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert") {
    $operation->loadRefsBesoins();
    foreach ($operation->_ref_besoins as $_besoin) {
      $_besoin->loadRefTypeRessource();
    }
  }

  $listDates[$operation->date]["hors_plage"][] = $operation;

  if ($filter->_by_prat) {
    $listPrats[$operation->chir_id] = $operation->_ref_chir;
    $listDatesByPrat[$operation->date][$operation->chir_id][$operation->_id] = $operation;
  }
}

$numOp += count($operations);

ksort($listDates);

if ($format_print == "advanced") {
  foreach ($ordre_passage_temp as $chir_id => $_ops_by_prat) {
    foreach ($_ops_by_prat as $_ops_by_date) {
      array_multisort(CMbArray::pluck($_ops_by_date, "_datetime"), SORT_ASC, $_ops_by_date);
      $i = 1;
      foreach ($_ops_by_date as $_op) {
        $ordre_passage[$_op->_id] = $i;
        $i++;
      }
    }
  }
}

if ($filter->_by_prat) {
  foreach ($listPrats as $_prat) {
    $_prat->loadRefFunction();
  }

  ksort($listDatesByPrat);
  foreach ($listDatesByPrat as &$_listDatesByPrat) {
    foreach ($_listDatesByPrat as &$listOps) {
      array_multisort(CMbArray::pluck($listOps, "time_operation"), SORT_ASC, $listOps);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectations_plage"  , $affectations_plage);
$smarty->assign("filter"              , $filter);
$smarty->assign("prestation"          , $prestation);
$smarty->assign("_coordonnees"        , $_coordonnees);
$smarty->assign("_print_numdoss"      , $_print_numdoss);
$smarty->assign("_print_ipp"          , $_print_ipp);
$smarty->assign("listDates"           , $listDates);
$smarty->assign("operations"          , $operations);
$smarty->assign("numOp"               , $numOp);
$smarty->assign("_materiel"           , $_materiel);
$smarty->assign("_missing_materiel"   , $_missing_materiel);
$smarty->assign("_extra"              , $_extra);
$smarty->assign("_duree"              , $_duree);
$smarty->assign('_convalescence'      , $_convalescence);
$smarty->assign("_hors_plage"         , $_hors_plage);
$smarty->assign("_show_comment_sejour", $_show_comment_sejour);
$smarty->assign('_compact'            , $_compact);
$smarty->assign("_show_identity"      , $_show_identity);
$smarty->assign("ordre_passage"       , $ordre_passage);
$smarty->assign("_by_prat"            , $filter->_by_prat);

if ($filter->_by_prat) {
  $smarty->assign("listDatesByPrat"   , $listDatesByPrat);
  $smarty->assign("listPrats"         , $listPrats);

  $smarty->display("view_planning_by_prat.tpl");
}
else {
  switch ($format_print) {
    default:
    case "standard":
      $smarty->display("view_planning.tpl");
      break;
    case "advanced":
      $smarty->display("view_planning_advanced.tpl");
  }
}