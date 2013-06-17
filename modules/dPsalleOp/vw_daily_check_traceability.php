<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_min      = CValue::getOrSession('_date_min');
$date_max      = CValue::getOrSession('_date_max');
$object_guid   = CValue::getOrSession('object_guid');
$check_list_id = CValue::getOrSession('check_list_id');
$start         = (int) CValue::get('start');

$check_list = new CDailyCheckList;
$check_list->load($check_list_id);

$items = $check_list->loadBackRefs('items');
if ($check_list->_ref_object) {
  $check_list->_ref_object->loadRefsFwd();
}

if ($items) {
  foreach ($items as $id => $item) {
    $item->loadRefsFwd();
  }
}

@list($object_class, $object_id) = explode('-', $object_guid);

$where = array(
  "validator_id" => "IS NOT NULL"
);
if ($object_class) {
  $where['object_class'] = "= '$object_class'";
  if ($object_id) {
    $where['object_id'] = "= '$object_id'";
  }
}
if ($date_min) {
  $where[] = "date >= '$date_min'";
}
if ($date_max) {
  $where[] = "date <= '$date_max'";
}
$list_check_lists = $check_list->loadList($where, 'date DESC, object_class, object_id, type' , "$start,40");
$count_check_lists = $check_list->countList($where);

foreach ($list_check_lists as $_check_list) {
  if ($_check_list->_ref_object) {
    $_check_list->_ref_object->loadRefsFwd();
  }
}

$check_list_filter = new CDailyCheckList();
$check_list_filter->object_class = $object_class;
$check_list_filter->object_id = $object_id;
$check_list_filter->_date_min = $date_min;
$check_list_filter->_date_max = $date_max;
$check_list_filter->loadRefsFwd();

$list_rooms = CDailyCheckList::getRooms();

$empty = new COperation();
$empty->updateFormFields();
$list_rooms["COperation"] = array($empty);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_check_lists", $list_check_lists);
$smarty->assign("count_check_lists", $count_check_lists);
$smarty->assign("list_rooms", $list_rooms);
$smarty->assign("check_list", $check_list);
$smarty->assign("object_guid", $object_guid);
$smarty->assign("check_list_filter", $check_list_filter);
$smarty->assign("start", $start);
$smarty->display("vw_daily_check_traceability.tpl");
