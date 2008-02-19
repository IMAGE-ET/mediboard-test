<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsaleOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$salle = mbGetValueFromGetOrSession("salle");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$operation_id = mbGetValueFromGetOrSession("operation_id");

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Selection des salles
$listSalles = new CSalle;
$where = array("group_id"=>"= '$g'");
$listSalles = $listSalles->loadList($where);

$plages   = array();
$urgences = array();

// Selection des plages opératoires de la journée
if($salle) {
	$plages = new CPlageOp;
	$where = array();
	$where["date"] = "= '$date'";
	$where["salle_id"] = "= '$salle'";
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
	      unset($curr_op);
	    }
	  }
	}
	
	$urgences = new COperation;
	$where = array();
	$where["date"]     = "= '$date'";
	$where["salle_id"] = "= '$salle'";
	$order = "chir_id";
	$urgences = $urgences->loadList($where);
	foreach($urgences as &$curr_op) {
	  $curr_op->loadRefChir();
	  $curr_op->loadRefSejour();
	  $curr_op->_ref_sejour->loadRefPatient();
	  $curr_op->loadExtCodesCCAM();
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false       );
$smarty->assign("salle"         , $salle       );
$smarty->assign("listSalles"    , $listSalles  );
$smarty->assign("listAnesths"   , $listAnesths );
$smarty->assign("plages"        , $plages      );
$smarty->assign("urgences"      , $urgences    );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_plages.tpl");
?>