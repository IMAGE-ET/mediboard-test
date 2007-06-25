<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

require_once "ezc/Base/base.php";
function __autoload( $className ){
	ezcBase::autoload( $className );
}
require_once($AppUI->getLibraryFile("jpgraph/src/mbjpgraph"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_pie"));

$date     = mbGetValueFromGet("date"       , mbDate());
$module   = mbGetValueFromGet("module"     , 0);
$size     = mbGetValueFromGet("size"       , 1);
$element  = mbGetValueFromGet("element"    , "duration");
$interval = mbGetValueFromGet("interval"   , "day");
$numelem  = mbGetValueFromGet("numelem"    , 4);

switch($interval) {
  case "day":
    $startx = "$date 00:00:00";
    $endx   = "$date 23:59:59";
    break;
  case "month":
    $startx = mbDateTime("-1 MONTH", "$date 00:00:00");
    $endx   = "$date 23:59:59";
    break;
  case "hyear":
    $startx = mbDateTime("-27 WEEKS", "$date 00:00:00");
    $endx   = "$date 23:59:59";
    break;
}

$logs = new CAccessLog();

$sql = "SELECT `accesslog_id`, `module`, `action`, `period`," .
      "\nSUM(`hits`) AS `hits`, SUM(`duration`) AS `duration`, SUM(`request`) AS `request`" .
      "\nFROM `access_log`" .
      "\nWHERE DATE(`period`) BETWEEN '".mbDate($startx)."' AND '".mbDate($endx)."'";
if($module == "total") {
  $sql .= "\nGROUP BY `action`";
}
elseif($module) {
  $sql .= "\nAND `module` = '$module'";
  $sql .= "\nGROUP BY `action`";
} else {
  $sql .= "\nGROUP BY `module`";
}

$logs = db_loadObjectList($sql, $logs);

$datasTotal = array();
$i = 0;
foreach($logs as $data) {
  $datasTotal[$i]["value"] = $data->$element;
  if($module) {
    $datasTotal[$i]["legend"]= $data->action;
  } else {
    $datasTotal[$i]["legend"]= $data->module;
  }
  $i++;
}

function compare($a, $b) {
  global $element;
  return $a["value"] < $b["value"];
}

usort($datasTotal, "compare");
$datas = array_slice($datasTotal, 0, $numelem);
if(count($datasTotal) > $numelem) {
  $other = array_slice($datasTotal, $numelem);
  $datas[$numelem]["value"]  = 0;
  $datas[$numelem]["legend"] = "Autres";
  $n = 0;
  foreach($other as $curr_other) {
    $datas[$numelem]["value"] += $curr_other["value"];
    $n++;
  }
  $datas[$numelem]["legend"] .= " ($n)";
}

$values  = array();
$legends = array();
foreach($datas as $data) {
  $values[]  = $data["value"];
  $legends[] = $data["legend"];
}
$tab = array();
foreach($datas as $data) {
  $tab[$data['legend']] = $data['value'];
}

if($dPconfig['graph_engine'] == 'eZgraph') {
	$graph = new ezcGraphPieChart();
  	$graph->palette = new ezcGraphPaletteEzRed();
	$title = mbTranformTime(null, $date, "%A %d %b %Y");
	if($module) 
		$title .= " : ".$AppUI->_($module);
  	$graph->title = $title;
  	$graph->options->label = '%2$d (%3$.1f%%)';
   	$graph->data[$title] = new ezcGraphArrayDataSet($tab);
  
  	$graph->renderer = new ezcGraphRenderer3d();
 	$graph->renderer->options->moveOut = .2;
 	$graph->options->font = './shell/arial.ttf';
 	$graph->renderer->options->pieChartOffset = 63;
 	$graph->renderer->options->pieChartGleam = .3;
 	$graph->renderer->options->pieChartGleamColor = '#FFFFFF';
	$graph->renderer->options->pieChartShadowSize = 5;
  	$graph->renderer->options->pieChartShadowColor = '#000000';
 	$graph->renderer->options->legendSymbolGleam = .5;
  	$graph->renderer->options->legendSymbolGleamSize = .9;
  	$graph->renderer->options->legendSymbolGleamColor = '#FFFFFF';
 	$graph->renderer->options->pieChartSymbolColor = '#55575388';
  
  	$graph->renderer->options->pieChartHeight = 5;
  	$graph->renderer->options->pieChartRotation = .8;

  	$graph->renderToOutput( 300, 145);
	
} else {
	// Setup the graph.
	$graph = new PieGraph(300*(1+$size*0.2),200*$size,"auto");    
	$graph->SetMarginColor("lightblue");
	
	// Set up the title for the graph
	$title = mbTranformTime(null, $date, "%A %d %b %Y");
	if($module) $title .= " : ".$AppUI->_($module);
	$graph->title->Set($title);
	$graph->title->SetFont(FF_ARIAL,FS_NORMAL,7+$size);
	$graph->title->SetColor("darkred");
	$graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
	$graph->img->SetAntiAliasing();
	
	// Legend
	$graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
	$graph->legend->Pos(0.015,0.1, "right", "top");
	
	// Create the Pie plot
	$pplot = new PiePlot($values);
	$pplot->SetLegends($legends);
	$pplot->SetCenter(0.25+($size*0.07), 0.55);
	$pplot->SetSize(0.3);
	$pplot ->SetGuideLines ();
	$graph->Add($pplot);
	
	// Finally send the graph to the browser
	$graph->Stroke();
}
?>