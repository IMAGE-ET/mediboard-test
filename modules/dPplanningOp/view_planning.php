<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$operation_id = mbGetValueFromGet("operation_id");
$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefsFwd();
$operation->_ref_sejour->loadRefsFwd();
$patient =& $operation->_ref_sejour->_ref_patient;
$patient->loadRefs();

$today = mbDate();

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("operation", $operation);
$smarty->assign("today"    , $today    );

$smarty->display("view_planning.tpl");

?>