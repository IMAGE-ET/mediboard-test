<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$operation_id = mbGetValueFromGetOrSession("operation_id");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;


$operation = new COperation();
if($operation_id){
  $operation->load($operation_id);
  $operation->loadRefs();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp", $operation);
$smarty->assign("date"          , $date                    );
$smarty->assign("modif_operation", $modif_operation        );

$smarty->display("inc_vw_timing.tpl");

?>