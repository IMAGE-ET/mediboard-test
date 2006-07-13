<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPbloc', 'plagesop') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if(!($id = mbGetValueFromGetOrSession('id'))) {
  $AppUI->msg = "Vous devez choisir une plage opératoire";
  $AppUI->redirect( "m=dPbloc&tab=1");
}

$anesth = dPgetSysVal("AnesthType");

// Infos sur la plage opératoire
$plage = new CPlageOp;
$plage->load($id);
$plage->loadRefsFwd();

// Liste de droite
$list1 = new COperation;
$where = array();
$where["id"] = "= '$id'";
$where["rank"] = "= '0'";
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.id";
$order = "operations.temp_operation";
$list1 = $list1->loadList($where, $order, null, null, $ljoin);
foreach($list1 as $key => $value) {
  $list1[$key]->loadRefsFwd();
  $list1[$key]->_ref_sejour->loadRefsFwd();
}

// Liste de gauche
$list2 = new COperation;
$where["rank"] = "!= '0'";
$order = "operations.rank";
$list2 = $list2->loadList($where, $order, null, null, $ljoin);
foreach($list2 as $key => $value) {
  $list2[$key]->loadRefsFwd();
  $list2[$key]->_ref_sejour->loadRefsFwd();
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('plage', $plage);
$smarty->assign('anesth', $anesth);
$smarty->assign('list1', $list1);
$smarty->assign('list2', $list2);
$smarty->assign('max', sizeof($list2));

$smarty->display('vw_edit_interventions.tpl');

?>