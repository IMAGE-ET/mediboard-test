<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");

$now       = mbDate();
$filter = new COperation;
$filter->salle_id      = CValue::get("salle_id");
$filter->_date_min     = CValue::get("_date_min", $now);
$filter->_date_max     = CValue::get("_date_max", $now);
$filter->_prat_id      = CValue::get("_prat_id");
$filter->_plage        = CValue::get("_plage");
$filter->_ranking      = CValue::get("_ranking");
$filter->_cotation     = CValue::get("_cotation");
$filter->_specialite   = CValue::get("_specialite");
$filter->_codes_ccam   = CValue::get("_codes_ccam");
$filter->_ccam_libelle = CValue::get("_ccam_libelle", 1);

$filterSejour = new CSejour;
$filterSejour->type = CValue::get("type");

//On sort les plages opératoires
//  Chir - Salle - Horaires

$plagesop = new CPlageOp;

$where = array();
$where["date"] =  $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);

$order = "date, salle_id, debut";

$chir_id = CValue::get("chir");
$user = CMediusers::get();

// En fonction du praticien
if($filter->_prat_id) {
  $where["chir_id"] = $ds->prepare("= %", $filter->_prat_id);
}

// En fonction de la salle
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

$salle = new CSalle;
$whereSalle = array('bloc_id' => CSQLDataSource::prepareIn(array_keys($listBlocs)));
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles), $filter->salle_id);

$plagesop = $plagesop->loadList($where, $order);
$plagesop["urgences"] = new CPlageOp();

// Operations de chaque plage
$listUrgencesTraitees = array();
foreach ($plagesop as &$plage) {
  $where = array();
  $tempOp = new COperation;
  
  // Cas des plages normales
  if ($plage->_id) {
	  
	  $plage->loadRefsFwd();
	
	  // Opérations normale
	  $joins = array();
	  $where["plageop_id"] = "= '$plage->_id'";
	  $where["annulee"] = "= '0'";
	  
	  // Intervention ordonnancé
	  switch ($filter->_ranking) {
	    case "ok" : $where["rank"] = "!= '0'"; break;
	    case "ko" : $where["rank"] = "= '0'"; break;
	  }
	  
    // Interventions avec ou sans actes
    if ($filter->_cotation) {
      $ljoin["acte_ccam"] = "operations.operation_id = acte_ccam.object_id AND acte_ccam.object_class = 'COperation'";
      switch ($filter->_cotation) {
        case "ok" : $where["acte_id"] = "IS NOT NULL"; break;        
        case "ko" : $where["acte_id"] = "IS NULL"; break;
      }
    }
	      
 	  if ($filter->_codes_ccam) {
	    $where["codes_ccam"] = "LIKE '%$filter->_codes_ccam%'";
	  }
	  
	  $order = "operations.rank";
	  
	  $listOperations = $tempOp->loadList($where, $order);
	  
	  // Urgences
	  $where["plageop_id"]   = "IS NULL";
	  $where["salle_id"]     = "= '$plage->salle_id'";
	  $where["chir_id"]      = "= '$plage->chir_id'";
	  $where["date"]         = "= '$plage->date'";
	  $where["operation_id"] = $plage->_spec->ds->prepareNotIn($listUrgencesTraitees);
	  $listUrgences = $tempOp->loadList($where);
	  $listUrgencesTraitees = array_merge($listUrgencesTraitees, array_keys($listUrgences));
	  
	  // On compile les interventions
	  $plage->_ref_operations = array_merge($listOperations, $listUrgences);
  }
  else {
    $ljoin = array();

    // Interventions avec ou sans actes
    if ($filter->_cotation) {
      $ljoin["acte_ccam"] = "operations.operation_id = acte_ccam.object_id AND acte_ccam.object_class = 'COperation'";
      switch ($filter->_cotation) {
        case "ok" : $where["acte_id"] = "IS NOT NULL"; break;        
        case "ko" : $where["acte_id"] = "IS NULL"; break;
      }
    }
    
    // Cas des urgences restantes
	  $where["plageop_id"]   = "IS NULL";
	  $where["date"]         = $ds->prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);
	  $where["operation_id"] = $ds->prepareNotIn($listUrgencesTraitees);
	  $order = "date, chir_id";
    $plage->_ref_operations = $tempOp->loadList($where, $order, null, null, $ljoin);
	  if (!count($plage->_ref_operations)) {
	    unset($plagesop["urgences"]);
	    continue;
	  }
  }
  
  foreach($plage->_ref_operations as $keyOp => &$operation) {
    $operation->loadRefsFwd();
    $operation->loadRefsActesCCAM();
    foreach($operation->_ref_actes_ccam as &$curr_acte) {
      $curr_acte->loadRefsFwd();
    }
    $sejour =& $operation->_ref_sejour;
    if ($filterSejour->type && $filterSejour->type != $sejour->type) {
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