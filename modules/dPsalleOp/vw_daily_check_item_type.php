<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$item_type_id = CValue::getOrSession('item_type_id');

$group_id = CGroups::loadCurrent()->_id;

$item_type = new CDailyCheckItemType();
if (!$item_type->load($item_type_id)) {
  $item_type->index = 1;
}
else {
  $item_type->loadRefsNotes();
}

$item_category = new CDailyCheckItemCategory();

foreach (CDailyCheckList::$_HAS_classes as $_class) {
  unset($item_category->_specs["target_class"]->_locales[$_class]);
}

list($targets, $item_categories_by_class) = CDailyCheckItemCategory::getCategoriesTree();

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
$smarty->assign("item_type", $item_type);
$smarty->assign("item_category", $item_category);
$smarty->assign("item_categories_by_class", $item_categories_by_class);
$smarty->assign("targets", $targets);
$smarty->display("vw_daily_check_item_type.tpl");
