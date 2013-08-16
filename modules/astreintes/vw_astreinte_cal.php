<?php 

/**
 * View astreintes calendar
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
$mode = CValue::getOrSession("mode", "week");

$user = CMediusers::get();
$group = CGroups::loadCurrent();

$astreinte = new CPlageAstreinte;
$where = array();
$order = "start ASC,end ASC";

switch ($mode) {
  case 'day':
    $where["start"] = "< '$date 23:59:00'";
    $where["end"]   = "> '$date 00:00:00'";
    $date_next = CMbDT::date("+1 DAY"   , $date);
    $date_prev = CMbDT::date("-1 DAY"   , $date);
    $calendar = new CPlanningDay($date);
    break;

  case 'month':
    $month_first = CMbDT::date("first day of this month"   , $date);
    $month_last = CMbDT::date("last day of this month", $month_first);
    $where["start"] = "< '$month_last 23:59:00'";
    $where["end"]   = "> '$month_first 00:00:00'";
    $date_next = CMbDT::date("+1 DAY"   , $month_last);
    $date_prev = CMbDT::date("-1 DAY"   , $month_first);
    $calendar = new CPlanningMonth($date);
    break;

  default:  // week
    // hack for first day of week
    if (date("w", strtotime($date)) == 0) {
      $date = CMbDT::date("-1 DAY", $date);
    }
    $week_monday = CMbDT::date("this week"   , $date);
    $week_sunday = CMbDT::date("Next Sunday", $week_monday);
    $where["start"] = "< '$week_sunday 23:59:00'";
    $where["end"]   = "> '$week_monday 00:00:00'";
    $date_next = CMbDT::date("+1 WEEK"   , $date);
    $date_prev = CMbDT::date("-1 WEEK"   , $date);
    $calendar = new CPlanningWeekNew($date, $week_monday, $week_sunday);
    break;
}

$calendar->guid = "CPlanning-$mode-$date";
$calendar->title = "Astreintes-$mode-$date";

$astreintes = $astreinte->loadList($where, $order);

/** @var $astreintes CPlageAstreinte[] */
foreach ($astreintes as $_astreinte) {
  $length = CMbDT::minutesRelative($_astreinte->start, $_astreinte->end);

  //not in the current group
  $_astreinte->loadRefUser();
  if ($_astreinte->_ref_user->_group_id != $group->_id) {
    continue;
  }

  $_astreinte->loadRefColor();

  $libelle = "<span style=\"text-align:center;\">";
  $libelle.= ($_astreinte->libelle) ? "<strong>$_astreinte->libelle</strong><br/>": null;
  $libelle.= $_astreinte->_ref_user.'<br/>'.$_astreinte->phone_astreinte."</span>";
  $libelle = CMbString::purifyHTML($libelle);
  $plage = new CPlanningEvent($_astreinte->_guid, $_astreinte->start, $length, $libelle, "#".$_astreinte->_color, true, 'astreinte', false, false);
  $plage->setObject($_astreinte);
  $plage->plage["id"] = $_astreinte->_id;
  $plage->type = $_astreinte->type;
  $plage->end = $_astreinte->end;
  $plage->display_hours = true;

  if ($_astreinte->getPerm(PERM_EDIT)) {
    $plage->addMenuItem("edit", utf8_encode("Modifier l'astreinte"));
  }
  //add the event to the planning
  $calendar->addEvent($plage);
}

$calendar->hour_min  = "00";
$calendar->rearrange();


//smarty
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("next", $date_next);
$smarty->assign("prev", $date_prev);
$smarty->assign("planning", $calendar);
$smarty->assign("height_planning_astreinte", CAppUI::pref("planning_resa_height", 1500));
$smarty->assign("mode", $mode);
$smarty->display("vw_calendar.tpl");

//mbTrace($calendar);