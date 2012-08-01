<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 12962 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$date     = CValue::getOrSession("date"    , mbDate());
$groupmod = CValue::getOrSession("groupmod", 2);

// Hour range for daily stats
$hour_min = CValue::getOrSession("hour_min", "6");
$hour_max = CValue::getOrSession("hour_max", "23");
$hours = range(0, 23);

$left_mode      = CValue::getOrSession("left_mode", "counts"); // counts
$left_sampling  = CValue::getOrSession("left_sampling", "total"); // total

$module = null;
if (!is_numeric($groupmod)) {
  $module = $groupmod;
  $groupmod = 0;
}

CAppUI::requireModuleFile('dPstats', 'graph_userlog');

$listModules = CModule::getInstalled();

$to    = mbDate("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "day")) {
  default:
  case "day":
    $from = mbDate("-1 DAY", $to);
    // Hours limitation
    $from = mbDateTime("+$hour_min HOUR", $from);
    $to   = mbDateTime("-1 DAY +$hour_max HOUR", $to  );
    break;
  case "month":
    $from = mbDate("-1 MONTH", $to);
    break;
  case "hyear":
    $from = mbDate("-6 MONTH", $to);
    break;
  case "twoyears":
    $from = mbDate("-2 YEARS", $to);
    break;
  case "twentyyears":
    $from = mbDate("-20 YEARS", $to);
    break;
}

CSQLDataSource::$trace = false;

$graphs = array();
$left   = array($left_mode, $left_sampling);

switch ($groupmod) {
  case 0:
    foreach (array('store', 'create', 'delete', 'merge') as $type) {
      $graphs[] = graphUserLogV2($module, $type, $from, $to, $interval, $left);
    }
    break;
    
  case 1:
    foreach ($listModules as $unModule) {
      $graph = graphUserLogV2($unModule->mod_name, null, $from, $to, $interval, $left);
      
      if ($graph) {
        $graphs[] = $graph;
      }
    }
    break;
    
  case 2:
    $graphs[] = graphUserLogV2(null, null, $from, $to, $interval, $left);
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
$smarty->assign("left_sampling", $left_sampling);

$smarty->assign("module"     , $module);
$smarty->assign("interval"   , $interval);
$smarty->assign("listModules", $listModules);

$smarty->display("view_user_logs.tpl");

?>