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

$constante_item = new CConstanteItem;
if ($constante_item_id) {
  $constante_item->load($constante_item_id);
}

$constante_item->category_prescription_id = $category_prescription_id;
$constante_item->loadRefCategoryPrescription();
$smarty = new CSmartyDP;
$smarty->assign("constante_item", $constante_item);
$smarty->assign("category_prescription_id", $category_prescription_id);
$smarty->display("inc_form_constante_item.tpl");

?>