<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$operation = new COperation;
$sejour    = new CSejour;

if ($sejour_id = CValue::get("sejour_id")) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefs();
}

if ($operation_id = CValue::get("operation_id")) {
	$operation->load($operation_id);
	$operation->loadRefsFwd();
	$operation->_ref_sejour->loadRefsFwd();
	$patient =& $operation->_ref_sejour->_ref_patient;
  $patient->loadRefs();
}

$today = mbDate();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("operation", $operation);
$smarty->assign("sejour", $sejour);
$smarty->assign("today"    , $today    );

$smarty->display("view_planning.tpl");

?>