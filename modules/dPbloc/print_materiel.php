<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$now       = mbDate();

$filter = new CMateriel;
$filter->_date_min = mbGetValueFromGet("_date_min"    , "$now");
$filter->_date_max = mbGetValueFromGet("_date_max"    , "$now");

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();

$salle = new CSalle;
$whereSalle = array();
$whereSalle["group_id"] = "= '$g'";
$where["plagesop.salle_id"] = $db->prepareIn(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where[] = "plagesop.date BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

$order = array();
$order[] = "plagesop.date";
$order[] = "rank";

$where1 = $where;
$where1["commande_mat"] = "!= '1'";
$op1 = new COperation();
$op1 = $op1->loadList($where1, $order, null, null, $ljoin);
foreach($op1 as $key => $value) {
  $op1[$key]->loadRefsFwd();
  $op1[$key]->_ref_sejour->loadRefsFwd();
}

$where2 = $where;
$where2["commande_mat"] = "!= '0'";
$op2 = new COperation();
$op2 = $op2->loadList($where2, $order, null, null, $ljoin);
foreach($op2 as $key => $value) {
  $op2[$key]->loadRefsFwd();
  $op2[$key]->_ref_sejour->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("op1", $op1);
$smarty->assign("op2", $op2);

$smarty->display("print_materiel.tpl");

?>