<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;
$ds = CSQLDataSource::get("std");

$can->needsRead();

$now       = mbDate();
$filter = new COperation;
$filter->_date_min     = mbGetValueFromGet("_date_min", $now);
$filter->_date_max     = mbGetValueFromGet("_date_max", $now);
$filter->_prat_id      = mbGetValueFromGet("_prat_id");
$filter->_bloc_id      = mbGetValueFromGet("_bloc_id");
$filter->salle_id      = mbGetValueFromGet("salle_id");
$filter->_plage        = mbGetValueFromGet("_plage");
$filter->_intervention = mbGetValueFromGet("_intervention");
$filter->_specialite   = mbGetValueFromGet("_specialite");
$filter->_codes_ccam   = mbGetValueFromGet("_codes_ccam");
$filter->_ccam_libelle = mbGetValueFromGet("_ccam_libelle", 1);

$_coordonnees  = mbGetValueFromGet("_coordonnees");

$filterSejour = new CSejour;
$filterSejour->type = mbGetValueFromGet("type");

//On sort les plages opératoires
//  Chir - Salle - Horaires

$plagesop = new CPlageOp;

$affectations_plage = array();

$where = array();
$where["date"] =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);

$order = "date, salle_id, debut";

$chir_id = mbGetValueFromGet("chir");
$user = new CMediusers();
$user->load($AppUI->user_id);

// Filtre par specialite
if ($filter->_specialite or $filter->_prat_id) {
  $chir = new CMediusers;
  // Chargement de la liste des chirs qui ont la specialite selectionnee
  $chirs = $chir->loadList(array ("function_id" => "= '$filter->_specialite '"));
  $where["chir_id"] = CSQLDataSource::prepareIn(array_keys($chirs), $filter->_prat_id);
}

// En fonction de la salle
$salle = new CSalle();
$whereSalle = array();
$whereSalle["bloc_id"] = CSQLDataSource::prepareIn(array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ)), $filter->_bloc_id);
if($filter->salle_id) {
  $whereSalle["salle_id"] = "= $filter->salle_id";
}
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));

$plagesop = $plagesop->loadList($where, $order);

// Operations de chaque plage
foreach($plagesop as &$plage) {
  $plage->loadRefsFwd(1);
  
  $where = array();
  $where["plageop_id"] = "= '$plage->_id'";
  switch ($filter->_intervention) {
    case "1" : $where["rank"] = "!= '0'"; break;
    case "2" : $where["rank"] = "= '0'"; break;
  }
  
  if ($filter->_codes_ccam) {
    $where["codes_ccam"] = "LIKE '%$filter->_codes_ccam%'";
  }
  
  $order = "operations.rank";

  $listOp = new COperation;
  $listOp = $listOp->loadList($where, $order);

  foreach($listOp as $keyOp => &$operation) {
    $operation->loadRefsFwd(1);
    $sejour =& $operation->_ref_sejour;
    if($filterSejour->type && $filterSejour->type != $sejour->type) {
      unset($listOp[$keyOp]);
    } else {
     $sejour->loadRefsFwd(1);   
     // On utilise la first_affectation pour contenir l'affectation courante du patient
     $sejour->_ref_first_affectation = $sejour->getCurrAffectation($operation->_datetime);
     $affectation =& $sejour->_ref_first_affectation;
     if ($affectation->_id) {
       $affectation->loadRefsFwd();
       $affectation->_ref_lit->loadCompleteView();
     }
    }
  }
  if ((sizeof($listOp) == 0) && !$filter->_plage) {
    unset($plagesop[$plage->_id]);
  }
  $plage->_ref_operations = $listOp;
  
  // Chargement des affectation de la plage
  $plage->loadAffectationsPersonnel();
  
  // Initialisation des tableaux de stockage des affectation pour les op et les panseuses
  $affectations_plage[$plage->_id]["op"] = array();
  $affectations_plage[$plage->_id]["op_panseuse"] = array();
  
  if (null !== $plage->_ref_affectations_personnel) {
    $affectations_plage[$plage->_id]["op"] = $plage->_ref_affectations_personnel["op"];
    $affectations_plage[$plage->_id]["op_panseuse"] = $plage->_ref_affectations_personnel["op_panseuse"];
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("filter"            , $filter);
$smarty->assign("_coordonnees"      , $_coordonnees);
$smarty->assign("plagesop"          , $plagesop);

$smarty->display("view_planning.tpl");

?>