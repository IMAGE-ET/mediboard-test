<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$category_prescription_id = CValue::getOrSession("category_prescription_id");

$category = new CCategoryPrescription();
$category->load($category_prescription_id);

$group = new CGroups();
$groups = $group->loadList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("category", $category);
$smarty->assign("groups", $groups);
$smarty->display("inc_form_category.tpl");

?>