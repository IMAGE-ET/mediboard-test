<?php /* $Id: view_planning.php 2798 2007-10-11 15:55:13Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: $
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
$filter->salle_id      = mbGetValueFromGet("salle_id");
$filter->_plage        = mbGetValueFromGet("_plage");
$filter->_intervention = mbGetValueFromGet("_intervention");
$filter->_specialite   = mbGetValueFromGet("_specialite");
$filter->_codes_ccam   = mbGetValueFromGet("_codes_ccam");
$filter->_ccam_libelle = mbGetValueFromGet("_ccam_libelle", 1);

$filterSejour = new CSejour;
$filterSejour->type = mbGetValueFromGet("type");

//On sort les plages opératoires
//  Chir - Salle - Horaires

$plagesop = new CPlageOp;

$where = array();
$where["date"] =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);

$order = array();
$order[] = "date";
$order[] = "salle_id";
$order[] = "debut";

$chir_id = mbGetValueFromGet("chir");
$user = new CMediusers();
$user->load($AppUI->user_id);

// En fonction du praticien
if($filter->_prat_id) {
  $where["chir_id"] = $ds->prepare("= %", $filter->_prat_id);
}

// En fonction de la salle
$salle = new CSalle;
$whereSalle = array();
$whereSalle["group_id"] = "= '$g'";
$where["salle_id"] = $ds->prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)), $filter->salle_id);

$plagesop = $plagesop->loadList($where, $order);

// Operations de chaque plage
foreach($plagesop as &$plage) {
  $plage->loadRefsFwd();
  
  $where = array();
  $where["plageop_id"] = "= '$plage->_id'";
  $where["annulee"] = "= '0'";
  switch ($filter->_intervention) {
    case "1" : $where["rank"] = "!= '0'"; break;
    case "2" : $where["rank"] = "= '0'"; break;
  }
  
  if ($filter->_codes_ccam) {
    $where["codes_ccam"] = "LIKE '%$filter->_codes_ccam%'";
  }
  
  $order = "operations.rank";

  $tempOp = new COperation;
  $plage->_ref_operations = $tempOp->loadList($where, $order);

  
  foreach($plage->_ref_operations as $keyOp => &$operation) {
    $operation->loadRefsFwd();
    $operation->loadRefsActesCCAM();
    foreach($operation->_ref_actes_ccam as &$curr_acte) {
      $curr_acte->loadRefsFwd();
      $curr_acte->guessAssociation();
    }
    $sejour =& $operation->_ref_sejour;
    if($filterSejour->type && $filterSejour->type != $sejour->type) {
      unset($plage->_ref_operations[$keyOp]);
    } else {
     $sejour->loadRefsFwd();   
     // On utilise la first_affectation pour contenir l'affectation courante du patient
     $sejour->_ref_first_affectation = $sejour->getCurrAffectation($operation->_datetime);
     $affectation =& $sejour->_ref_first_affectation;
     if ($affectation->_id) {
       $affectation->loadRefsFwd();
       $affectation->_ref_lit->loadCompleteView();
     }
    }
  }
  if ((sizeof($plage->_ref_operations) == 0) && !$filter->_plage) {
    unset($plagesop[$plage->_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"  , $filter);
$smarty->assign("plagesop", $plagesop);

$smarty->display("print_planning.tpl");

?>