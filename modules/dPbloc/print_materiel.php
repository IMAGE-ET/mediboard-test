<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

$deb = mbGetValueFromGetOrSession("deb", mbDate());
$fin = mbGetValueFromGetOrSession("fin", mbDate());

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where[] = "plagesop.date BETWEEN '$deb' AND '$fin'";
$order = array();
$order[] = "plagesop.date";
$order[] = "rank";

$where1 = $where;
$where1["commande_mat"] = "!= 'o'";
$op1 = new COperation();
$op1 = $op1->loadList($where1, $order, null, null, $ljoin);
foreach($op1 as $key => $value) {
  $op1[$key]->loadRefsFwd();
  $op1[$key]->_ref_sejour->loadRefsFwd();
}

$where2 = $where;
$where2["commande_mat"] = "!= 'n'";
$op2 = new COperation();
$op2 = $op2->loadList($where2, $order, null, null, $ljoin);
foreach($op2 as $key => $value) {
  $op2[$key]->loadRefsFwd();
  $op2[$key]->_ref_sejour->loadRefsFwd();
}

// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("op1", $op1);
$smarty->assign("op2", $op2);

$smarty->display("print_materiel.tpl");

?>