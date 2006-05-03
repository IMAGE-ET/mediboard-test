<?php /* $Id: vw_edit_interventions.php,v 1.20 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.20 $
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
  $list1[$key]->loadRefs();
}

// Liste de gauche
$list2 = new COperation;
$where["rank"] = "!= '0'";
$order = "operations.rank";
$list2 = $list2->loadList($where, $order, null, null, $ljoin);
foreach($list2 as $key => $value) {
  $list2[$key]->loadRefs();
  $j = 0;
  for($i = substr($list2[$key]->_ref_plageop->debut, 0, 2) ; $i < substr($list2[$key]->_ref_plageop->fin, 0, 2) ; $i++) {
    if(strlen($i) == 1)
    $i = "0".$i;
	$list2[$key]->_listhour[$j] = $i;
	$j++;
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('plage', $plage);
$smarty->assign('anesth', $anesth);
$smarty->assign('list1', $list1);
$smarty->assign('list2', $list2);
$smarty->assign('max', sizeof($list2));

$smarty->display('vw_edit_interventions.tpl');

?>