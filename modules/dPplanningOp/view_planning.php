<?php /* $Id: view_planning.php,v 1.19 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 1.19 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPccam', 'acte') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Récupération des variables passées en GET
$operation_id = dPgetParam($_GET, "operation_id", null);
$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefsFwd();
$patient =& $operation->_ref_pat;
$patient->loadRefs();

$today = mbDate();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;
$smarty->assign('operation', $operation);
$smarty->assign('today', $today);
$smarty->display('view_planning.tpl');

?>