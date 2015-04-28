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
$list_type_id = CValue::getOrSession('list_type_id');
$type         = CValue::get('type', "ouverture_salle");

$list_type = new CDailyCheckListType();
if ($list_type->load($list_type_id)) {
  if ($list_type->type == "intervention" || $list_type->check_list_group_id) {
    $list_type = new CDailyCheckListType();
  }
  else {
    $list_type->loadRefsNotes();
    $list_type->loadRefsCategories();
  }
}
if (!$list_type->_id) {
  $list_type->group_id = CGroups::loadCurrent()->_id;
  $list_type->type = $type;
}

$list_type->makeLinksArray();

unset($list_type->_specs["type_validateur"]->_locales["chir_interv"]);

list($targets, $by_type) = CDailyCheckListType::getListTypesTree();

foreach ($by_type as $type => $_list_types) {
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

$smarty->assign("list_type" , $list_type);
$smarty->assign("by_type"   , $by_type);
$smarty->assign("targets"   , $targets);

$smarty->display("vw_daily_check_list_type.tpl");