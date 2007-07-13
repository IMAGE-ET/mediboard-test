<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

require_once($AppUI->getSystemClass("mbGraph"));

$date       = mbGetValueFromGet("date"       , mbDate());
$module     = mbGetValueFromGet("module"     , 0);
$actionName = mbGetValueFromGet("actionName" , 0);
$size       = mbGetValueFromGet("size"       , 1);
$interval   = mbGetValueFromGet("interval"   , "day");

$datax = array();

switch($interval) {
  case "day":
    $startx = "$date 00:00:00";
    $endx   = "$date 23:59:59";
    $step = "+1 HOUR";
    $date_format = "%Hh";
    break;
  case "month":
    $startx = mbDateTime("-1 MONTH", "$date 00:00:00");
    $endx   = "$date 23:59:59";
    $step = "+1 DAY";
    $date_format = "%d/%m";
    break;
  case "hyear":
    $startx = mbDateTime("-27 WEEKS", "$date 00:00:00");
    $endx   = "$date 23:59:59";
    $step = "+1 WEEK";
    $date_format = "%U";
    break;
}

for($i = $startx; $i <= $endx; $i = mbDateTime($step, $i)) {
  $datax[] = mbTranformTime(null, $i, $date_format);
}

$logs = new CAccessLog();

$sql = "SELECT `accesslog_id`, `module`, `action`, `period`," .
      "\nSUM(`hits`) AS `hits`, SUM(`duration`) AS `duration`, SUM(`request`) AS `request`," .
      "\nDATE_FORMAT(`period`, '$date_format') AS `gperiod`" .
      "\nFROM `access_log`" .
      "\nWHERE DATE(`period`) BETWEEN '".mbDate($startx)."' AND '".mbDate($endx)."'";
if($module) {
  $sql .= "\nAND `module` = '$module'";
}
if($actionName) {
  $sql .= "\nAND `action` = '$actionName'";
}
$sql .= "\nGROUP BY `gperiod`" .
    "\nORDER BY `period`";

$logs = db_loadObjectList($sql, $logs);

$nbHits = array();
$duration = array();
$request = array();
foreach($datax as $x) {
  $f = true;
  foreach($logs as $log) {
    if($x == mbTranformTime(null, $log->period, $date_format)) {
      $nbHits[] = $log->hits;
      $duration[] = $log->_average_duration;
      $request[] = $log->_average_request;
      $f = false;
    }
  }
  if($f) {
    $nbHits[] = 0;
    $duration[] = 0;
    $request[] = 0;
  }
}

$title = mbTranformTime(null, $date, "%A %d %b %Y");
if($module) $title .= " : ".$AppUI->_($module);
if($actionName) $title .= " - $actionName";

$options = array( "size" => $size,
									"title" => $title,
									"margin" => array(15+$size*10, 75+$size*10, 10+$size*10, 15+$size*10),
									"posLegend" => array(0.015,0.79, "right", "center"), 
									"sizeFontAxis" => 6,
									"labelAngle" => 50,
									"textTickInterval" => 2,
									"posXAbsDelta" => 0,
									"posYAbsDelta" => 0,
									"dataBar" => $nbHits,
									"dataLine" => array($duration, $request),
									"datax" => $datax,
									"graphBarLegend" => "Hits",
									"graphLineLegend" => array("Page (s)","DB (s)") );
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addSecondAxis($options);
$graph->addDataBarPlot($options);
$graph->addDataLinePlot($options);
$graph->render("out",$options);

?>