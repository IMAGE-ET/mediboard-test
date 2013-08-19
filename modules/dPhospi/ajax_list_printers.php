<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$printer = new CPrinter();
$order_by = "object_id, text";
$ljoin = array();
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = printer.function_id";
$printers = $printer->loadList(null, $order_by, null, null, $ljoin);

foreach ($printers as $_printer) {
  $_printer->loadTargetObject();
  $_printer->loadRefFunction();
}

$printer_id = CValue::getOrSession("printer_id", 0);
$smarty = new CSmartyDP();

$smarty->assign("printers"  , $printers);
$smarty->assign("printer_id", $printer_id);
$smarty->display("inc_list_printers.tpl");

