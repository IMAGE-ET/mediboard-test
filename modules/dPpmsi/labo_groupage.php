<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

$operation_id  = mbGetValueFromGetOrSession("operation_id", null);

if(!$operation_id) {
  $AppUI->setMsg("Vous devez selectionner une intervention", UI_MSG_ERROR);
  $AppUI->redirect("m=dPpmsi&tab=vw_dossier");
}

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefGHM();
//mbTrace($operation->_ref_GHM);

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign("operation", $operation);
$smarty->assign("GHM", $operation->_ref_GHM);

$smarty->display('labo_groupage.tpl');

?>