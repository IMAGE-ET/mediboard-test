<?php /* $Id: vw_idx_materiel.php,v 1.14 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.14 $
* @author Romain Ollivier
*/
 
global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

$typeAff = mbGetValueFromGetOrSession("typeAff");

$deb = mbDate();
$fin = mbDate("+ 0 day");

// Récupération des opérations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.id";
$where = array();
$where["materiel"] = "!= ''";
$where["plageop_id"] = "IS NOT NULL";
$where["commande_mat"] = $typeAff ? "= 'o'" : "!= 'o'";
$where["annulee"]      = $typeAff ? "= '1'" : "!= '1'";
$order = array();
$order[] = "plagesop.date";
$order[] = "rank";

$op = new COperation;
$op = $op->loadList($where, $order, null, null, $ljoin);
foreach($op as $key => $value) {
  $op[$key]->loadRefsFwd();
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('typeAff', $typeAff);
$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('op', $op);

$smarty->display('vw_idx_materiel.tpl');

?>