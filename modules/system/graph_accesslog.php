<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('system', 'accesslog') );
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_bar'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_line'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_regstat'));

$date   = mbGetValueFromGet("date"   , mbDate());
$module = mbGetValueFromGet("module" , 0);
$action = mbGetValueFromGet("action" , 0);
$next = mbDate("+ 1 day", $date);

for($i = 0; $i <= 23; $i++) {
  if($i <= 9)
    $datax[] = "0$i h";
  else
    $datax[] = "$i h";
}

$logs = new CAccessLog();
$where["period"] = "BETWEEN '$date' AND '$next'";
$where["module"] = "= '$module'";
$where["action"] = "= '$action'";
$order = "period";

$logs = $logs->loadList($where, $order);

$nbHits = array();
$duration = array();
$request = array();
foreach($datax as $x) {
  $f = true;
  foreach($logs as $log) {
    if($x == mbTranformTime(null, $log->period, "%H h")) {
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
$graph = new Graph(450,125,"auto");    
$graph->img->SetMargin(25,120,20,25);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Analyse par heures $module - $action";
$graph->title->Set($title);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,7);
$graph->title->SetColor("black");
$graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,7);
$graph->img->SetAntiAliasing();
$graph->SetScale("textint");
$graph->SetY2Scale("lin");

// Setup font for axis
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
$graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,7);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);
$graph->y2axis->SetColor("#000088");
$graph->yaxis->SetColor("black");

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(50);

// Legend
//$graph->legend->SetMarkAbsSize(5);
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
$graph->legend->Pos(0.02,0.7, "right", "center");

// Create the bar hits pot
$listPlots = array();
$bplot = new BarPlot($nbHits);
$from = "#000088";
$to = "#EEEEEE";
$bplot->SetWidth(0.6);
$bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
$bplot->SetColor("white");
$bplot->setLegend("Hits");

// Create the line main duration plot
$lplot = new LinePlot($duration);
$lplot->SetColor("#008800");
$lplot->SetWeight(1);
//$lplot->value->SetFormat("%01.2f");
//$lplot->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
//$lplot->value->SetMargin(10);
$lplot->setLegend("Durée page");

// Create the line database duration plot
$lplot2 = new LinePlot($request);
$lplot2->SetColor("#880000");
$lplot2->SetWeight(1);
//$lplot2->value->SetFormat("%01.2f");
//$lplot2->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
//$lplot2->value->SetMargin(10);
$lplot2->setLegend("Durée db");

// Add the graphs
$graph->Add($lplot);
$graph->Add($lplot2);
$graph->AddY2($bplot);

// Finally send the graph to the browser
$oldCfg = $dPconfig['debug'];
$dPconfig['debug'] = '0';
$graph->Stroke();
$dPconfig['debug'] = $oldCfg;