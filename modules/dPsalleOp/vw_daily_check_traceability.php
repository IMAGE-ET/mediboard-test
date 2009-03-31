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
$room_id = mbGetValueFromGetOrSession('room_id');
$check_list_id = mbGetValueFromGetOrSession('check_list_id');

$check_list = new CDailyCheckList;
$check_list->load($check_list_id);
$check_list->loadBackRefs('items');
if ($check_list->_back['items']) {
	foreach($check_list->_back['items'] as &$item) {
		$item->loadRefsFwd();
	}
}

$where = array();
if ($room_id) {
	$where['room_id'] = "= '$room_id'";
}
if ($date_min) {
  $where[] = "date >= '$date_min'";
}
if ($date_max) {
	$where[] = "date <= '$date_max'";
}
$list_check_lists = $check_list->loadList($where, 'date', 30);

$check_list_filter = new CDailyCheckList;
$check_list_filter->room_id = $room_id;
$check_list_filter->_date_min = $date_min;
$check_list_filter->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_check_lists", $list_check_lists);
$smarty->assign("check_list", $check_list);
$smarty->assign("check_list_filter", $check_list_filter);
$smarty->display("vw_daily_check_traceability.tpl");

?>