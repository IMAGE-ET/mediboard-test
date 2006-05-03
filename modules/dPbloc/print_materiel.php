<?php /* $Id: print_materiel.php,v 1.7 2006/04/21 16:56:37 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.7 $
* @author Romain Ollivier
*/
 
global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

$deb = mbGetValueFromGetOrSession("deb", mbDate());
$fin = mbGetValueFromGetOrSession("fin", mbDate());

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.id";
$where = array();
$where["materiel"] = "!= ''";
$where["plageop_id"] = "IS NOT NULL";
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
}

$where2 = $where;
$where2["commande_mat"] = "!= 'n'";
$op2 = new COperation();
$op2 = $op2->loadList($where2, $order, null, null, $ljoin);
foreach($op2 as $key => $value) {
  $op2[$key]->loadRefsFwd();
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('op1', $op1);
$smarty->assign('op2', $op2);

$smarty->display('print_materiel.tpl');

?>