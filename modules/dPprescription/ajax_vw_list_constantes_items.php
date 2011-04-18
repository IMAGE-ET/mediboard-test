<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$element_prescription_id = CValue::getOrSession("element_prescription_id");
$constante_item_id = CValue::get("constante_item_id");

$element_prescription = new CElementPrescription;
$element_prescription->load($element_prescription_id);

$constantes_items = $element_prescription->loadBackRefs("constantes_items");

$smarty = new CSmartyDP;

$smarty->assign("element_prescription", $element_prescription);
$smarty->assign("constante_item_id", $constante_item_id);
$smarty->assign("constantes_items", $constantes_items);
$smarty->display("inc_list_constantes_items.tpl");
?>