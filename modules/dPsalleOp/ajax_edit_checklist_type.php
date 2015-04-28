<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
CCanDo::checkAdmin();

global $g;
$check_list_type_id  = CValue::getOrSession('check_list_type_id');
$check_list_group_id = CValue::get('check_list_group_id');

$list_type = new CDailyCheckListType();
$list_type->load($check_list_type_id);
$list_type->loadRefsNotes();
$list_type->loadRefsCategories();

$list_type->makeLinksArray();

list($targets, $by_type) = CDailyCheckListType::getListTypesTree();

if ($list_type->type != "intervention" || !$list_type->check_list_group_id) {
  unset($list_type->_specs["type_validateur"]->_list[10]);
}

foreach ($targets as $_targets) {
  foreach ($_targets as $_target) {
    $_target->loadRefsFwd();
  }
}

if ($check_list_group_id) {
  $list_type->type = 'intervention';
  $list_type->check_list_group_id = $check_list_group_id;
  $list_type->group_id = $g;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_type", $list_type);
$smarty->assign("targets"  , $targets);
$smarty->assign("callback" , CValue::get('callback'));
$smarty->assign("modal"    , CValue::get('modal', 0));
$smarty->display("inc_edit_check_list_type.tpl");