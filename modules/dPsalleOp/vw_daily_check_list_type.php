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
  $list_type->group_id = CGroups::loadCurrent()->_id;
  $list_type->object_class = $target_class;
}

$list_type->makeLinksArray();

foreach (CDailyCheckList::$_HAS_classes as $_class) {
  unset($list_type->_specs["object_class"]->_locales[$_class]);
}

list($targets, $by_class) = CDailyCheckListType::getListTypesTree();

foreach ($by_class as $_class => $_list_types) {
  /** @var CDailyCheckListType[] $_list_types */
  foreach ($_list_types as $_list_type) {
    $_type_links = $_list_type->loadRefTypeLinks();
    foreach ($_type_links as $_type_link) {
      $_object = $_type_link->loadRefObject();
    }
    $_list_type->countBackRefs("daily_check_list_categories");
  }
}

foreach ($targets as $_targets) {
  foreach ($_targets as $_target) {
    $_target->loadRefsFwd();
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_type", $list_type);
$smarty->assign("by_class",  $by_class);
$smarty->assign("targets",   $targets);
$smarty->display("vw_daily_check_list_type.tpl");
