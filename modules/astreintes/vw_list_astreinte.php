<?php

/**
 * list sort by day for a week
 *
 * @category Astreintes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkEdit();

$date = CValue::getOrSession("date", CMbDT::date());
$mode = CValue::getOrSession("mode", "day");


$astreinte = new CPlageAstreinte;
$where = array();
$order = "start DESC,end";

switch ($mode) {
  case 'year':
    $year_first = CMbDT::transform(null, $date, "%Y-01-01" );
    $year_last =  CMbDT::transform(null, $date, "%Y-12-31" );
    $where["start"] = "< '$year_last 23:59:00'";
    $where["end"]   = "> '$year_first 00:00:00'";
    $date_next = CMbDT::date("+1 YEAR", $date);
    $date_prev = CMbDT::date("-1 YEAR", $date);
    break;

  case 'month':
    $month_monday = CMbDT::date("first day of this month"   , $date);
    $month_sunday = CMbDT::date("last day of this month", $month_monday);
    $where["start"] = "< '$month_sunday 23:59:00'";
    $where["end"]   = "> '$month_monday 00:00:00'";
    $date_next = CMbDT::date("+1 MONTH", $month_monday);
    $date_prev = CMbDT::date("-1 MONTH", $month_monday);
    break;

  case 'week':
    $week_monday = CMbDT::date("this week"   , $date);
    $week_sunday = CMbDT::date("next sunday", $week_monday);
    $where["start"] = "<= '$week_sunday 23:59:00'";
    $where["end"]   = ">= '$week_monday 00:00:00'";
    $date_next = CMbDT::date("+1 WEEK", $date);
    $date_prev = CMbDT::date("-1 WEEK", $date);
    break;

  default:
    $where["start"] = "< '$date 23:59:00'";
    $where["end"]   = "> '$date 00:00:00'";
    $date_next = CMbDT::date("+1 DAY", $date);
    $date_prev = CMbDT::date("-1 DAY", $date);
    break;
}

$astreintes = $astreinte->loadList($where, $order);


/**
 * @var CPlageAstreinte $_astreinte
 */
foreach ($astreintes as $_astreinte) {
  $_astreinte->loadRefUser();
  $_astreinte->loadRefColor();
  $_astreinte->getCollisions();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("astreintes", $astreintes);
$smarty->assign("today", $date);
$smarty->assign("date_next", $date_next);
$smarty->assign("date_prev", $date_prev);
$smarty->assign("mode", $mode);
$smarty->display("vw_list_astreintes.tpl");
