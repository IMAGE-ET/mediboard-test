<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$operation_id = mbGetValueFromGetOrSession("protocole_id", 0);

$op = new COperation;
$chir = new CMediusers;
if(!$operation_id) {
  // L'utilisateur est-il praticien?
  $mediuser = new CMediusers;
  $mediuser->load($AppUI->user_id);
  if ($mediuser->isPraticien()) {
    $chir = $mediuser;
  }
}  else {
  $op->load($operation_id);
  $op->loadRefs();
}

// Heures & minutes
$start = 7;
$stop = 20;
$step = 15;

for ($i = $start; $i < $stop; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $step) {
    $mins[] = $i;
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('protocole', true);
$smarty->assign('hospitalisation', false);

$smarty->assign('op', $op);
$smarty->assign('chir' , $op->chir_id    ? $op->_ref_chir    : $chir);
$smarty->assign('pat'  , $op->pat_id     ? $op->_ref_pat     : null );
$smarty->assign('plage', $op->plageop_id ? $op->_ref_plageop : new CPlageop );

$smarty->assign('hours', $hours);
$smarty->assign('mins', $mins);

$smarty->display('vw_addedit_planning.tpl');

?>