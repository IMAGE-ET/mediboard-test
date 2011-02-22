<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

$current_user = CAppUI::$user;
$function_id  = $current_user->function_id;

$printer = new CPrinter;
$where = array();
$where["function_id"] = "= '$function_id'";
$printers = $printer->loadlist($where);

foreach($printers as $_printer) {
  $_printer->loadTargetObject();
}

$smarty = new CSmartyDP();

$smarty->assign("printers", $printers);
$smarty->display("inc_choose_printer.tpl");
?>