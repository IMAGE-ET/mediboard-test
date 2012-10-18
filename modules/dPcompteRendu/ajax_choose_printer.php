<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

$mode_etiquette = CValue::get("mode_etiquette", 0);
$object_class   = CValue::get("object_class");
$object_id      = CValue::get("object_id");
$modele_etiquette_id = CValue::get("modele_etiquette_id");

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

$smarty->assign("mode_etiquette", $mode_etiquette);
$smarty->assign("printers", $printers);
$smarty->assign("object_class", $object_class);
$smarty->assign("object_id"   , $object_id);
$smarty->assign("modele_etiquette_id", $modele_etiquette_id);

$smarty->display("inc_choose_printer.tpl");
?>