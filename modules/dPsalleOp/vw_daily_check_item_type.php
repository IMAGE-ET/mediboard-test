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
$item_type_id     = CValue::get('item_type_id');
$item_category_id = CValue::get('item_category_id');

$group_id = CGroups::loadCurrent()->_id;

$item_type = new CDailyCheckItemType();
if ($item_type->load($item_type_id)) {
  $item_type->loadRefsNotes();
}
else {
  $item_type->index       = 1;
  $item_type->category_id = $item_category_id;
  $item_type->active      = "1";
}
$item_type->loadRefCategory()->loadRefListType();

$item_category = new CDailyCheckItemCategory();

foreach (CDailyCheckList::$_HAS_classes as $_class) {
  unset($item_category->_specs["target_class"]->_locales[$_class]);
}

$category = new CDailyCheckItemCategory();
$category->load($item_category_id);

$op = $category->target_class == "COperation" ? 1 : 0;
list($targets, $item_categories_by_class) = CDailyCheckItemCategory::getCategoriesTree($op);

$target_class_list = array_keys($item_category->_specs["target_class"]->_locales);

foreach ($item_categories_by_class as $_class => $item_categories_by_target) {
  foreach ($item_categories_by_target as $_id => $_categories) {
    /** @var CDailyCheckItemCategory $_cat */
    foreach ($_categories as $_cat) {
      $_cat->loadBackRefs('item_types', array("`index`", "`title`"));

      /** @var CDailyCheckItemType $_type */
      foreach ($_cat->_back['item_types'] as $_id_type => $_type) {
        if ($_type->group_id != $group_id) {
          unset($_cat->_back['item_types'][$_id_type]);
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("item_type",                $item_type);
$smarty->assign("item_category",            $item_category);
$smarty->assign("item_categories_by_class", $item_categories_by_class);
$smarty->assign("targets",                  $targets);
$smarty->display("vw_daily_check_item_type.tpl");
