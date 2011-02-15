<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */



$printer_id = CValue::getOrSession("printer_id", 0);

$smarty = new CSmartyDP();
$smarty->assign("printer_id", $printer_id);
$smarty->display("vw_printers.tpl");

?>