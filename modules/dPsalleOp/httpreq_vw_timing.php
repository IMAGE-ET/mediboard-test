<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/

CCanDo::checkRead();

$operation_id = CValue::getOrSession("operation_id");
$date  = CValue::getOrSession("date", mbDate());
$modif_operation = CCanDo::edit() || $date >= mbDate();

$operation = new COperation();
if($operation_id){
  $operation->load($operation_id);
  $operation->loadRefs();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp",           $operation);
$smarty->assign("date",            $date);
$smarty->assign("modif_operation", $modif_operation);

$smarty->display("inc_vw_timing.tpl");

?>