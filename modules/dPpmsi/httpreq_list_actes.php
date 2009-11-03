<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$operation_id = CValue::getOrSession("operation_id");
$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefsActesCCAM();
foreach ($operation->_ref_actes_ccam as &$acte) {
  $acte->loadRefsFwd();
  $acte->guessAssociation();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("curr_op", $operation);

$smarty->display("inc_confirm_actes_ccam.tpl");

?>