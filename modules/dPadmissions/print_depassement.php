<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

$id = mbGetValueFromGetOrSession("id");

$admission = new COperation();
$admission->load($id);
$admission->loadRefs();
$admission->_ref_sejour->loadRefsFwd();
$admission->_ref_plageop->loadRefs();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign('admission', $admission);

$smarty->display('print_depassement.tpl');

?>