<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$operation = new COperation;
$sejour    = new CSejour;

if ($sejour_id = CValue::get("sejour_id")) {
  $sejour->load($sejour_id);
  $sejour->loadNDA();
  $sejour->loadRefsFwd();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefs();
}

if ($operation_id = CValue::get("operation_id")) {
	$operation->load($operation_id);
	$operation->loadRefsFwd();
	$operation->_ref_sejour->loadRefsFwd();
	$operation->_ref_sejour->loadNDA();
	$patient =& $operation->_ref_sejour->_ref_patient;
  $patient->loadRefs();
}

$today = mbDate();

$group = CGroups::loadCurrent();
$group->loadConfigValues();
$simple_DHE = $group->_configs['dPplanningOp_COperation_DHE_mode_simple'];

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("operation" , $operation);
$smarty->assign("sejour"    , $sejour);
$smarty->assign("today"     , $today    );
$smarty->assign("simple_DHE", $simple_DHE);

$smarty->display("view_planning.tpl");

?>