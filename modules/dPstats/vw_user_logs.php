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

CAppUI::requireModuleFile("dPstats", "graph_userlog");

$to = CMbDT::date("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "day")) {
  default:
  case "day":
    $from = CMbDT::date("-1 DAY", $to);
    break;
  case "month":
    $from = CMbDT::date("-1 MONTH", $to);
    break;
  case "hyear":
    $from = CMbDT::date("-6 MONTH", $to);
    break;
  case "twoyears":
    $from = CMbDT::date("-2 YEARS", $to);
    break;
  case "twentyyears":
    $from = CMbDT::date("-20 YEARS", $to);
    break;
}

$graph = graphUserLog($from, $to, $interval, $user_id);

// Chargement des utilisateurs
$users = CMediusers::loadListFromType();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graph"   , $graph);
$smarty->assign("date"    , $date);
$smarty->assign("user_id" , $user_id);
$smarty->assign("users"   , $users);
$smarty->assign("interval", $interval);

$smarty->display("vw_user_logs.tpl");
