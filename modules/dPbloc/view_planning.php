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
$filter->_date_min = mbGetValueFromGet("_date_min", $now);
$filter->_date_max = mbGetValueFromGet("_date_max", $now);
$filter->_prat_id = mbGetValueFromGet("chir");
$filter->salle_id = mbGetValueFromGet("salle");
$filter->_plage = mbGetValueFromGet("vide");
$filter->_intervention = mbGetValueFromGet("type");
$filter->_specialite = mbGetValueFromGet("spe");
$filter->_codes_ccam = mbGetValueFromGet("code_ccam");

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

// On ne teste pas les droits pour l'instant...

//if($user->isFromType(array("Anesthésiste"))) {
  if($chir_id) {
    $where["chir_id"] = $ds->prepare("= %", $filter->_prat_id);
  }
//} else {
//  $listPrat = new CMediusers;
//  $listPrat = $listPrat->loadPraticiens(PERM_READ, $spe);
//  $where["chir_id"] = $ds->prepareIn(array_keys($listPrat), $chir_id);
//}

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

  if ((sizeof($listOp) == 0) && ($filter->_plage == "false"))
    unset($plagesop[$plage->_id]);
  else {
    foreach($listOp as $operation) {
      $operation->loadRefsFwd();
      $operation->_ref_sejour->loadRefsFwd();
            
      // On utilise la first_affectation pour contenir l'affectation courante du patient
      $sejour =& $operation->_ref_sejour;
      $sejour->_ref_first_affectation = $sejour->getCurrAffectation($operation->_datetime);
      $affectation =& $sejour->_ref_first_affectation;
      if ($affectation->_id) {
        $affectation->loadRefsFwd();
        $affectation->_ref_lit->loadCompleteView();
      }
    }
    $plage->_ref_operations = $listOp;
    $plage->loadPersonnel();
    if (null !== $$plage->_ref_personnel) {
      foreach ($plage->_ref_personnel as $_personnel) {
        $_personnel->loadUser();
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"  , $filter);
$smarty->assign("plagesop", $plagesop);

$smarty->display("view_planning.tpl");

?>