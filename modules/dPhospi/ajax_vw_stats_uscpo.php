<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$operation = new COperation();
$max_uscpo = $operation->_specs["duree_uscpo"]->max;
$default_week = $operation->conf("default_week_stat_uscpo");

/** @var date $date_min */
/** @var date $date_max */
$date_min   = CValue::getOrSession("date_min", CMbDT::date($default_week == "last" ? "-1 week" : null));
$date_max   = CValue::getOrSession("date_max", CMbDT::date($default_week == "next" ? "+1 week" : null));
$service_id = CValue::getOrSession("service_id", "");
 
if ($date_min > $date_max) {
  list($date_min, $date_max) = array($date_max, $date_min);
}

$operation = new COperation;

$where = array();
$ljoin = array();

$where["duree_uscpo"] = "> 0";
$where["annulee"] = "!= '1'";

if ($service_id) {
  $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
  $where["sejour.service_id"] = "= '$service_id'";
}

$day = $date_min;
$dates = array();
$series = array();
$serie = array(
  'data' => array(),
  'label' => utf8_encode("Nombre de nuits prévues")
);

$today = CMbDT::date();

while ($day <= $date_max) {
  $display = CMbDT::dateToLocale($day);
  
  // On préfixe d'une étoile si c'est le jour courant
  if ($day == $today) {
    $display = "* ".$display;
  }
  
  $dates[] = array(count($dates), $display);
  $day_min = CMbDT::date("-$max_uscpo DAY", $day);
  $where[10] = "operations.date BETWEEN '$day_min' AND '$day'";
  $where[11] = "DATE_ADD(operations.date, INTERVAL duree_uscpo DAY) > '$day'";
  $count = $operation->countList($where, null, $ljoin);
  $day = CMbDT::date("+1 day", $day);
  $serie['data'][] = array(count($serie['data'])-0.2, $count);
}


$series[] = $serie;
$day = $date_min;
$serie = array(
 'data' => array(),
 'label' => utf8_encode("Nombre de nuits placées")
);

$ljoin["affectation"] = "affectation.sejour_id = operations.sejour_id";

while ($day <= $date_max) {
  $day_min = CMbDT::date("-$max_uscpo DAY", $day);
  $where[10] = "operations.date BETWEEN '$day_min' AND '$day'";
  $where[11] = "DATE_ADD(operations.date, INTERVAL duree_uscpo DAY) > '$day'";
  $where[12] = "DATE_ADD(operations.date, INTERVAL duree_uscpo DAY) <= affectation.sortie";
  $day = CMbDT::date("+1 day", $day);
  $count = $operation->countList($where, null, $ljoin);
  $serie['data'][] = array(count($serie['data'])+0.2, intval($count)); 
}

$series[] = $serie;

$options = CFlotrGraph::merge("bars", array(
  'title'    => utf8_encode("Durées USCPO"),
  'xaxis'    => array('ticks' => $dates),
  'yaxis'    => array('tickDecimals' => 0),
  'grid'     => array('verticalLines' => true),
  'bars'     => array('barWidth' => 0.4)
));

$graph = array('series' => $series, 'options' => $options);

$group   = CGroups::loadCurrent();
$service = new CService();
$where   = array();
$where["group_id"]  = "= '$group->_id'";
$where["cancelled"] = "= '0'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ, $where, $order);

$dates = array();
$day = $date_min;

while ($day <= $date_max) {
  $dates[] = $day;
  $day = CMbDT::date("+1 day", $day);
}

$smarty = new CSmartyDP;

$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("services", $services);
$smarty->assign("graph"   , $graph);
$smarty->assign("service_id", $service_id);
$smarty->assign("dates"   , $dates);

$smarty->display("inc_vw_stats_uscpo.tpl");
