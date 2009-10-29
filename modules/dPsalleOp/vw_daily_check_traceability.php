<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');
$object_guid = mbGetValueFromGetOrSession('object_guid');
$check_list_id = mbGetValueFromGetOrSession('check_list_id');

$check_list = new CDailyCheckList;
$check_list->load($check_list_id);
$check_list->loadBackRefs('items');

if ($check_list->_back['items']) {
	foreach($check_list->_back['items'] as $id => $item) {
		$item->loadRefsFwd();
	}
}

@list($object_class, $object_id) = explode('-', $object_guid);

$where = array();
if ($object_class) {
	$where['object_class'] = "= '$object_class'";
  if ($object_id)
    $where['object_id'] = "= '$object_id'";
}
if ($date_min) {
  $where[] = "date >= '$date_min'";
}
if ($date_max) {
	$where[] = "date <= '$date_max'";
}
$list_check_lists = $check_list->loadList($where, 'date DESC, object_class' , 30);

$check_list_filter = new CDailyCheckList;
$check_list_filter->object_class = $object_class;
$check_list_filter->object_id = $object_id;
$check_list_filter->_date_min = $date_min;
$check_list_filter->_date_max = $date_max;
$check_list_filter->loadRefsFwd();


$list_rooms = array(
  "CSalle" => array(),
  "CBlocOperatoire" => array()
);

foreach($list_rooms as $class => &$list) {
  $room = new $class;
  $list = $room->loadList();
  $empty = new $class;
  $empty->updateFormFields();
  array_unshift($list, $empty);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_check_lists", $list_check_lists);
$smarty->assign("list_rooms", $list_rooms);
$smarty->assign("check_list", $check_list);
$smarty->assign("check_list_filter", $check_list_filter);
$smarty->display("vw_daily_check_traceability.tpl");

?>