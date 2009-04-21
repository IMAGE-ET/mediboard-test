<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$can->needsRead();
$ds = CSQLDataSource::get("std");

$date = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

// Selection des plages opératoires de la journée
$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$plages = $plages->loadList($where);

// Listes des opérations
$listEntree = new COperation;
$where = array();
$where[] = "`plageop_id` ".$ds->prepareIn(array_keys($plages))." OR (`plageop_id` IS NULL AND `date` = '$date')";
$where["entree_bloc"] = "IS NULL";
$order = "time_operation";
$listEntree = $listEntree->loadList($where,$order);
foreach($listEntree as $key => $value) {
	$oper =& $listEntree[$key];
  $oper->loadRefsFwd();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listEntree" , $listEntree);
$smarty->assign("date"       , $date);
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("vw_brancardage.tpl");
?>