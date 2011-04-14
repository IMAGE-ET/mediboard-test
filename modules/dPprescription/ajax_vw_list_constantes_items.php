<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$category_prescription_id = CValue::getOrSession("category_prescription_id");
$constante_item_id = CValue::get("constante_item_id");

$category_prescription = new CCategoryPrescription;
$category_prescription->load($category_prescription_id);

$constantes_items = $category_prescription->loadBackRefs("constantes_items");

$smarty = new CSmartyDP;

$smarty->assign("category_prescription", $category_prescription);
$smarty->assign("constante_item_id", $constante_item_id);
$smarty->assign("constantes_items", $constantes_items);
$smarty->display("inc_list_constantes_items.tpl");
?>