<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$item_category_id = CValue::getOrSession('item_category_id');
$list_type_id     = CValue::get('list_type_id');

$list_type = new CDailyCheckListType();
$list_type->load($list_type_id);

$item_category = new CDailyCheckItemCategory();
if ($item_category->load($item_category_id)) {
  $item_category->loadRefsNotes();
  $item_category->loadRefItemTypes();
}
else {
  $item_category->list_type_id = $list_type_id;
  $item_category->target_class = $list_type->object_class;
}

foreach (CDailyCheckList::$_HAS_classes as $_class) {
  unset($item_category->_specs["target_class"]->_locales[$_class]);
}

list($targets, $item_categories_by_class) = CDailyCheckItemCategory::getCategoriesTree();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("item_category", $item_category);
$smarty->assign("targets", $targets);
$smarty->display("vw_daily_check_item_category.tpl");
