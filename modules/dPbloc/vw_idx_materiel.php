<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m;

$can->needsRead();

$typeAff = mbGetValueFromGetOrSession("typeAff");

$deb = mbDate();
$fin = mbDate("+ 0 day");

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where["commande_mat"] = $typeAff ? "= '1'" : "!= '1'";
$where["annulee"]      = $typeAff ? "= '1'" : "!= '1'";
$order = array();
$order[] = "plagesop.date";
$order[] = "rank";

$op = new COperation;
$op = $op->loadList($where, $order, null, null, $ljoin);
foreach($op as $key => $value) {
  $op[$key]->loadRefsFwd();
  $op[$key]->_ref_sejour->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("typeAff", $typeAff);
$smarty->assign("deb"    , $deb);
$smarty->assign("fin"    , $fin);
$smarty->assign("op"     , $op);

$smarty->display("vw_idx_materiel.tpl");

?>