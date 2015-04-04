<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage system
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date    = CValue::getOrSession("date", CMbDT::date());
$user_id = CValue::getOrSession("user_id");
$interval = CValue::getOrSession("interval", "one-day");

CView::enforceSlave();

CAppUI::requireModuleFile("dPstats", "graph_userlog");

$to = CMbDT::date("+1 DAY", $date);
switch ($interval) {
  case "one-day":
    $from = CMbDT::date("-1 DAY", $to);
    break;

  case "one-week":
    $from = CMbDT::date("-1 WEEK", $to);
    break;

  case "height-weeks":
    $from = CMbDT::date("-8 WEEK", $to);
    break;

  case "one-year":
    $from = CMbDT::date("-1 YEAR", $to);
    break;

  case "four-years":
    $from = CMbDT::date("-4 YEARS", $to);
    break;

  case "twenty-years":
    $from = CMbDT::date("-20 YEARS", $to);
    break;

  default:
    $from = CMbDT::date("-1 DAY", $to);
}

$graph = graphUserLog($from, $to, $interval, $user_id);

// Chargement des utilisateurs
$user = new CMediusers();
$users = $user->loadListFromType();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graph"   , $graph);
$smarty->assign("date"    , $date);
$smarty->assign("user_id" , $user_id);
$smarty->assign("users"   , $users);
$smarty->assign("interval", $interval);

$smarty->display("vw_user_logs.tpl");
