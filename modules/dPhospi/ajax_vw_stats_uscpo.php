<?php

/**
 * dPhospi
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date_min   = CValue::getOrSession("date_min", mbDate("-1 month"));
$date_max   = CValue::getOrSession("date_max", mbDate());
$service_id = CValue::getOrSession("service_id", "");
 
if ($date_min > $date_max) {
  list($date_min, $date_max) = array($date_max, $date_min);
}

$operation = new COperation;

$where = array();
$ljoin = array();

$where["duree_uscpo"] = "> 0";

$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
if ($service_id) {
  $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
  $where["sejour.service_id"] = "= '$service_id'";
}

$day = $date_min;
$dates = array();
$series = array();
$serie = array(
  'data' => array(),
  'label' => utf8_encode("Nombre de nuits pr�vu�es")
);
  
while ($day <= $date_max) {
  $dates[] = array(count($dates), mbDateToLocale($day));
  $where[2] = "plagesop.date <= '$day' AND DATE_ADD(plagesop.date, INTERVAL duree_uscpo DAY) > '$day'";
  $count = count($operation->loadIds($where, null, null, null, $ljoin));
  $day = mbDate("+1 day", $day);
  $serie['data'][] = array(count($serie['data'])-0.2, $count);
}


$series[] = $serie;
$day = $date_min;
$serie = array(
 'data' => array(),
 'label' => utf8_encode("Nombre de nuits r�alis�es")
);

$ljoin["affectation"] = "affectation.sejour_id = operations.sejour_id";

while ($day <= $date_max) {
  $where[2] = "plagesop.date <= '$day' AND DATE_ADD(plagesop.date, INTERVAL duree_uscpo DAY) > '$day'";
  $where[3] = "DATE_ADD(plagesop.date, INTERVAL duree_uscpo DAY) <= affectation.sortie";
  $day = mbDate("+1 day", $day);
  $count = count($operation->loadIds($where, null, null, null, $ljoin));
  $serie['data'][] = array(count($serie['data'])+0.2, $count); 
}

$series[] = $serie;

$options = CFlotrGraph::merge("bars", array(
  'title'    => utf8_encode("Dur�es USCPO"),
  'xaxis'    => array('ticks' => $dates),
  'grid'     => array('verticalLines' => true),
  'bars'     => array('barWidth' => 0.4)
));

$graph = array('series' => $series, 'options' => $options);

$service = new CService;
$services = $service->loadListWithPerms(PERM_READ);

$smarty = new CSmartyDP;

$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("services", $services);
$smarty->assign("graph"   , $graph);
$smarty->assign("service_id", $service_id);

$smarty->display("inc_vw_stats_uscpo.tpl");
?>