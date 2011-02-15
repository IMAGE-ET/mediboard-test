<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$printer = new CPrinter();
$printers = $printer->loadList();

foreach($printers as $_printer) {
  $_printer->loadTargetObject();
}

$printer_id = CValue::getOrSession("printer_id", 0);
$smarty = new CSmartyDP();

$smarty->assign("printers"  , $printers);
$smarty->assign("printer_id", $printer_id);
$smarty->display("inc_list_printers.tpl");

?>