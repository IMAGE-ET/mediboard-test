<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$service_id = CValue::getOrSession('service_id');
$patient_id = CValue::getOrSession('patient_id');

// Services list
$service = new CService();
$list_services = $service->loadListWithPerms(PERM_READ);

$num_days_date_min = CAppUI::conf("pharmacie num_days_date_min");
$day_min = mbDate("-$num_days_date_min DAY");
$day_max = mbDate("+2 DAY");

$schedule = str_split(CAppUI::conf('pharmacie dispensation_schedule'));
sort($schedule);

if (false && count($schedule)) {
  $now = mbDate();
  $tomorrow = mbDate("+1 DAY");
  $list_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
  $selected_days = array_intersect_key($list_days, array_flip($schedule));
  
  $relative_days = array_fill_keys($schedule, null);
  foreach($selected_days as $_key => $_day) {
    $last = mbDate("LAST $_day", $tomorrow);
    $relative_days[$_key] = mbDaysRelative($tomorrow, $last);
  }
  
  $min_key = 50;
  $min_relative = 50;
  $max_key = null;
  $lower = false;
  foreach($relative_days as $_key => $_relative) {
    if ($_relative < $min_relative) {
      $min_key = $_key;
      $min_relative = $_relative;
      $lower = true;
    }
    else {
      if ($lower) {
        $max_key = $_key;
      }
      $lower = false;
    }
  }
  
  mbTrace($relative_days);
  mbTrace($min_key);
  mbTrace($max_key);
  
  /*if ($max_key === null) {
    $keys = array_keys($relative_days);
    $max_key = reset($keys);
  }*/
  
  $min_rel = $relative_days[$min_key];
  $max_rel = $relative_days[$max_key];
  
  $day_min = mbDate("+$min_rel DAYS");
  $day_max = mbDate("+$max_rel DAYS");
  
  mbTrace($min_rel);
  mbTrace($max_rel);
  
  mbTrace($day_min);
  mbTrace($day_max);
}

$date_min = CValue::get('_date_min');
$date_max = CValue::get('_date_max');

if (!$date_min) {
  $date_min = CValue::session('_date_delivrance_min', mbDate("-$num_days_date_min DAY"));
}
if (!$date_max) {
  $date_max = CValue::session('_date_delivrance_max', mbDate("+2 DAY"));
}

CValue::setSession('_date_delivrance_min', $date_min);
CValue::setSession('_date_delivrance_max', $date_max);

$delivrance = new CProductDelivery();
$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('patient_id', $patient_id);
$smarty->assign('service_id', $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('delivrance', $delivrance);

$smarty->display('vw_idx_dispensation.tpl');

?>