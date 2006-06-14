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

require_once( $AppUI->getModuleClass('dPplanningOp', 'sejour') );

$sejour_id  = mbGetValueFromGetOrSession("sejour_id", null);

if(!$sejour_id) {
  $AppUI->setMsg("Vous devez selectionner un séjour", UI_MSG_ERROR);
  $AppUI->redirect("m=dPpmsi&tab=vw_dossier");
}

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefsFwd();
$sejour->loadRefsOperations();
foreach($sejour->_ref_operations as $keyOp => $value) {
  $sejour->_ref_operations[$keyOp]->loadRefsFwd();
}
$sejour->loadRefGHM();

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $sejour->_ref_patient);
$smarty->assign("GHM", $sejour->_ref_GHM);

$smarty->display('labo_groupage.tpl');

?>