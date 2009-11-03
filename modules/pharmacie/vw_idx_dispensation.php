<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsRead();

$service_id = CValue::getOrSession('service_id');
$patient_id = CValue::getOrSession('patient_id');

// Services list
$service = new CService();
$list_services = $service->loadGroupList();

$day_min = $day_max = mbDate();

$schedule = str_split(CAppUI::conf('pharmacie dispensation_schedule'));
$schedule2 = str_split(CAppUI::conf('pharmacie dispensation_schedule'));
sort($schedule);
$schedule = array_combine($schedule, $schedule);
$schedule2 = $schedule;
/*if (count($schedule)) {
  $today = intval(mbTransformTime(null, null, '%l'));
  $list_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
  $array_next = $array_last = $schedule;
  
  $curr = isset($schedule[6]) ? 6 : reset($schedule);
  for($i = 6; $i > -1; $i--) {
    if (isset($schedule[$i])) $curr = $i;
    $array_last[$i] = $curr;
  }
  
  foreach($schedule as $key => $d) {
  	$prev = prev($schedule);
  	$schedule2[$key] = $prev ? $prev : end($schedule);
  }
  //$schedule2 = array_reverse($schedule2, true);
  
  foreach($array_last as $key => $d) {
  	$array_next[$key] = $schedule2[$d];
  }
	
	$day_min = mbTransformTime(($today != $array_last[$today] ? 'last '.$list_days[$array_last[$today]] : null), $day_min, '%Y-%m-%d');
	$day_max = mbTransformTime(($today != $array_next[$today] ? 'next '.$list_days[$array_next[$today]] : null), $day_max, '%Y-%m-%d');
}

ksort($array_last);
ksort($array_next);

mbTrace($schedule);
mbTrace($schedule2);

mbTrace($array_last);
mbTrace($array_next);

mbTrace($array_last[$today]);
mbTrace($today);
mbTrace($array_next[$today]);*/

$date_min = CValue::getOrSession('_date_min', $day_min);
$date_max = CValue::getOrSession('_date_max', $day_max);

CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

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