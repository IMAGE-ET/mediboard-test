<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

$id = mbGetValueFromGetOrSession("id");

$admission = new COperation();
$admission->load($id);
$admission->loadRefs();
$admission->_ref_plageop->loadRefs();
//$admission->_ref_pat->loadRefs();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('admission', $admission);

$smarty->display('print_depassement.tpl');

?>