<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

$date = mbGetValueFromGetOrSession("date", mbDate());

$listAffectations = new CAffectation;
$where = array();
$where[] = "entree < '$date 23:59:59' AND sortie > '$date 00:00:00'";
$order = "entree, sortie";
$listAffectations = $listAffectations->loadList($where, $order);

foreach($listAffectations as $key => $affectation) {
  $listAffectations[$key]->loadRefs();
  $listAffectations[$key]->_ref_lit->loadCompleteView();
  $listAffectations[$key]->_ref_operation->loadRefs();
  $listAffectations[$key]->_ref_operation->loadRefGHM();
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign("date", $date);
$smarty->assign("listAffectations", $listAffectations);

$smarty->display('vw_list_hospi.tpl');

?>
