<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 12962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$date     = CValue::getOrSession("date"    , CMbDT::date());
$groupmod = CValue::getOrSession("groupmod", 2);

// Hour range for daily stats
$hour_min = CValue::getOrSession("hour_min", "6");
$hour_max = CValue::getOrSession("hour_max", "23");
$hours = range(0, 23);

$left_mode = CValue::getOrSession("left_mode", "type"); // type, classe

$module = null;
if (!is_numeric($groupmod)) {
  $module = $groupmod;
  $groupmod = 0;
}

CAppUI::requireModuleFile('dPstats', 'graph_userlog');

$listModules = CModule::getInstalled();

$to    = CMbDT::date("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "day")) {
  default:
  case "day":
    $from = CMbDT::date("-1 DAY", $to);
    // Hours limitation
    $from = CMbDT::dateTime("+$hour_min HOUR", $from);
    $to   = CMbDT::dateTime("-1 DAY +$hour_max HOUR", $to  );
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

CSQLDataSource::$trace = false;

$graphs = array();

switch ($groupmod) {
  case 0:
    if ($left_mode == 'type') {
      foreach (array('store', 'create', 'delete', 'merge') as $type) {
        $graph = graphUserLogV2($module, $type, $from, $to, $interval, $left_mode);
        
        if ($graph) {
          $graphs[] = $graph;
        }
      }
    }
    else {
      foreach (CModule::getClassesFor($module) as $oneClass) {
        $graph = graphUserLogV2($module, array($oneClass, 'store', 'create', 'delete', 'merge'), $from, $to, $interval, $left_mode);
        
        if ($graph) {
          $graphs[] = $graph;
        }
      }
    }
    break;
    
  case 1:
    foreach ($listModules as $unModule) {
      $graph = graphUserLogV2($unModule->mod_name, null, $from, $to, $interval, $left_mode);
      
      if ($graph) {
        $graphs[] = $graph;
      }
    }
    break;
    
  case 2:
    $graphs[] = graphUserLogV2(null, null, $from, $to, $interval, $left_mode);
}

CSQLDataSource::$trace = false;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("graphs"     , $graphs);
$smarty->assign("groupmod"   , $groupmod);

$smarty->assign("date"       , $date);
$smarty->assign("hours"      , $hours);
$smarty->assign("hour_min"   , $hour_min);
$smarty->assign("hour_max"   , $hour_max);

$smarty->assign("left_mode"    , $left_mode);

$smarty->assign("module"     , $module);
$smarty->assign("interval"   , $interval);
$smarty->assign("listModules", $listModules);

$smarty->display("view_user_logs.tpl");

?>