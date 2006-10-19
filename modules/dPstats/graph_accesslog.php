<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getLibraryClass("jpgraph/src/jpgraph"));
require_once($AppUI->getLibraryClass("jpgraph/src/jpgraph_bar"));
require_once($AppUI->getLibraryClass("jpgraph/src/jpgraph_line"));
require_once($AppUI->getLibraryClass("jpgraph/src/jpgraph_regstat"));

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

// Setup the graph.
$graph = new Graph(320*$size,125*$size,"auto");    
$graph->img->SetMargin(15+$size*10,75+$size*10,10+$size*10,15+$size*10);
$graph->SetScale("textlin");
$graph->SetY2Scale("int");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = mbTranformTime(null, $date, "%A %d %b %Y");
if($module) $title .= " : ".$AppUI->_($module);
if($actionName) $title .= " - $actionName";
$graph->title->Set($title);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,7+$size);
$graph->title->SetColor("darkred");
$graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
$graph->img->SetAntiAliasing();

// Setup font for axis
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
$graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,6+$size);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);
$graph->y2axis->SetColor("#888888");
$graph->yaxis->SetColor("black");

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetTextTickInterval(2);
$graph->xaxis->SetLabelAngle(50);

// Legend
//$graph->legend->SetMarkAbsSize(5);
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
$graph->legend->Pos(0.015,0.79, "right", "center");

// Create the bar hits pot
$listPlots = array();
$bplot = new BarPlot($nbHits);
$from = "#aaaaaa";
$to = "#EEEEEE";
$bplot->SetWidth(0.8);
$bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
$bplot->SetColor("white");
$bplot->setLegend("Hits");

// Create the line main duration plot
$lplot = new LinePlot($duration);
$lplot->SetColor("#008800");
$lplot->SetWeight($size);
//$lplot->value->SetFormat("%01.2f");
//$lplot->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
//$lplot->value->SetMargin(10);
$lplot->setLegend("Page (s)");

// Create the line database duration plot
$lplot2 = new LinePlot($request);
$lplot2->SetColor("#880000");
$lplot2->SetWeight(1);
//$lplot2->value->SetFormat("%01.2f");
//$lplot2->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
//$lplot2->value->SetMargin(10);
$lplot2->setLegend("DB (s)");

// Add the graphs
$graph->Add($lplot);
$graph->Add($lplot2);
$graph->AddY2($bplot);

// Finally send the graph to the browser
$graph->Stroke();

?>