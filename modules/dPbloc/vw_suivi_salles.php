<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc 
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsEdit();

$date_suivi  = mbGetValueFromGetOrSession("date_suivi", mbDate());


// Chargement des salles
$salle = new CSalle;
$where = array("group_id"=>"= '$g'");
$order = "'nom'";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);

// Chargement des Anesthésistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$listInfosSalles = array();
foreach($listSalles as $keySalle=>$currSalle){
  $listInfosSalles[$keySalle] = array();
  $salle =& $listInfosSalles[$keySalle];
  
  $plages = new CPlageOp;
  $where = array();
  $where["date"] = "= '$date_suivi'";
  $where["salle_id"] = "= '$keySalle'";
  $order = "debut";
	$plages = $plages->loadList($where, $order);
	foreach($plages as &$curr_plage) {
	  $curr_plage->loadRefs(0);
	  $curr_plage->_unordered_operations = array();
	  foreach($curr_plage->_ref_operations as $key => &$curr_op) {
	    if($curr_op->salle_id != $curr_plage->salle_id) {
	      unset($curr_plage->_ref_operations[$key]);
	      continue;
	    }
	    $curr_op->loadRefSejour();
	    $curr_op->_ref_sejour->loadRefPatient();
	    $curr_op->loadExtCodesCCAM();
	    if($curr_op->rank == 0) {
	      $curr_plage->_unordered_operations[$key] = $curr_op;
	      unset($curr_plage->_ref_operations[$key]);
	    }
	  }
	}
  $salle["plages"] = $plages;
	
	// Interventions déplacés
	$deplacees = new COperation;
	$ljoin = array();
	$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
	$where = array();
	$where["operations.plageop_id"] = "IS NOT NULL";
	$where["plagesop.salle_id"]     = "!= operations.salle_id";
	$where["plagesop.date"]         = "= '$date_suivi'";
	$where["operations.salle_id"]   = "= '$keySalle'";
	$order = "operations.time_operation";
	$deplacees = $deplacees->loadList($where, $order, null, null, $ljoin);
	foreach($deplacees as &$curr_op) {
	  $curr_op->loadRefChir();
	  $curr_op->loadRefSejour();
	  $curr_op->_ref_sejour->loadRefPatient();
	  $curr_op->loadExtCodesCCAM();
	}
	$salle["deplacees"] = $deplacees;
  
	// Urgences
  $urgences = new COperation;
  $where = array();
  $where["date"]     = "= '$date_suivi'";
  $where["salle_id"] = "= '$keySalle'";
  $order = "chir_id";
  $urgences = $urgences->loadList($where);
  foreach($urgences as $keyOp => $curr_op) {
    $urgences[$keyOp]->loadRefChir();
    $urgences[$keyOp]->loadRefSejour();
    $urgences[$keyOp]->_ref_sejour->loadRefPatient();
    $urgences[$keyOp]->loadExtCodesCCAM();
  }
  $salle["urgences"] = $urgences;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listInfosSalles", $listInfosSalles);
$smarty->assign("listSalles"     , $listSalles);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);

$smarty->display("vw_suivi_salles.tpl");
?>