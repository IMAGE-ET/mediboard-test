<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$item_category_id = CValue::getOrSession('item_category_id');
$target_class     = CValue::get('target_class');

$item_category = new CDailyCheckItemCategory();
if ($item_category->load($item_category_id)) {
  $item_category->loadRefsNotes();
}
else {
  $item_category->target_class = $target_class;
}

foreach (CDailyCheckList::$_HAS_classes as $_class) {
  unset($item_category->_specs["target_class"]->_locales[$_class]);
}

list($targets, $item_categories_by_class) = CDailyCheckItemCategory::getCategoriesTree();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("item_category", $item_category);
$smarty->assign("item_categories_by_class", $item_categories_by_class);
$smarty->assign("targets", $targets);
$smarty->display("vw_daily_check_item_category.tpl");
