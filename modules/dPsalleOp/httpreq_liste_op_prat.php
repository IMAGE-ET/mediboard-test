<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$operation_id = mbGetValueFromGetOrSession("operation_id");

// Chargement des chirurgiens
$listPermPrats = new CMediusers;
$listPermPrats = $listPermPrats->loadPraticiens(PERM_READ);
$listPrats = array();
$plagesJour = new CPlageOp();
$where = array();
$where["date"] = "= '$date'";
$groupby = "chir_id";
$plagesJour = $plagesJour->loadList($where, null, null, $groupby);
foreach($plagesJour as $curr_plage) {
  if(key_exists($curr_plage->chir_id, $listPermPrats)) {
    $listPrats[$curr_plage->chir_id] = $listPermPrats[$curr_plage->chir_id];
  }
}

$urgencesJour = new COperation();
$where = array();
$where["date"]     = "= '$date'";
$where["chir_id"] = "= '$praticien_id'";
$groupby = "chir_id";
$urgencesJour = $urgencesJour->loadList($where, null, null, $groupby);
foreach($urgencesJour as $curr_op) {
  if(key_exists($curr_op->chir_id, $listPermPrats)) {
    $listPrats[$curr_op->chir_id] = $listPermPrats[$curr_op->chir_id];
  }
}

$plages   = array();
$urgences = array();

// Selection des plages opératoires de la journée
if($praticien_id) {
	$plages = new CPlageOp;
	$where = array();
	$where["date"] = "= '$date'";
	$where["chir_id"] = "= '$praticien_id'";
	$order = "debut";
	$plages = $plages->loadList($where, $order);
	foreach($plages as &$curr_plage) {
	  $curr_plage->loadRefs(0);
	  $curr_plage->_unordered_operations = array();
	  foreach($curr_plage->_ref_operations as $key => &$curr_op) {
	    $curr_op->loadRefSejour();
	    $curr_op->_ref_sejour->loadRefPatient();
	    $curr_op->loadExtCodesCCAM();
	    if($curr_op->rank == 0) {
	      $curr_plage->_unordered_operations[$key] = $curr_op;
	      unset($curr_plage->_ref_operations[$key]);
	    }
	  }
	}
	
	$urgences = new COperation;
	$where = array();
	$where["date"]     = "= '$date'";
	$where["chir_id"] = "= '$praticien_id'";
	$order = "time_operation";
	$urgences = $urgences->loadList($where, $order);
	foreach($urgences as &$curr_op) {
	  $curr_op->loadRefChir();
	  $curr_op->loadRefSejour();
	  $curr_op->_ref_sejour->loadRefPatient();
	  $curr_op->loadExtCodesCCAM();
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("praticien_id"  , $praticien_id);
$smarty->assign("listPrats"     , $listPrats   );
$smarty->assign("plages"        , $plages      );
$smarty->assign("urgences"      , $urgences    );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_op_prat.tpl");
?>