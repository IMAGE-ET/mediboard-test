<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$list_type_id = CValue::getOrSession('list_type_id');
$target_class = CValue::get('target_class');

$list_type = new CDailyCheckListType();
if ($list_type->load($list_type_id)) {
  $list_type->loadRefsNotes();
  $list_type->loadRefsCategories();
}
else {
  $list_type->object_class = $target_class;
}

foreach (CDailyCheckList::$_HAS_classes as $_class) {
  unset($list_type->_specs["object_class"]->_locales[$_class]);
}

list($targets, $by_class) = CDailyCheckListType::getListTypesTree();

foreach ($by_class as $_by_object) {
  foreach ($_by_object as $_list) {
    foreach ($_list as $_list_type) {
      $_list_type->countBackRefs("daily_check_list_categories");
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_type",  $list_type);
$smarty->assign("by_class",   $by_class);
$smarty->assign("targets",    $targets);
$smarty->display("vw_daily_check_list_type.tpl");
