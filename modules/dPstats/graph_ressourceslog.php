<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getLibraryClass("jpgraph/src/jpgraph"));
require_once($AppUI->getLibraryClass("jpgraph/src/jpgraph_pie"));

$date       = mbGetValueFromGet("date"       , mbDate());
$module     = mbGetValueFromGet("module"     , 0);
$size       = mbGetValueFromGet("size"       , 1);
$element    = mbGetValueFromGet("element"    , "duration");
$interval   = mbGetValueFromGet("interval"   , "day");

$datax = array();

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
if($module) {
  $sql .= "\nAND `module` = '$module'";
  $sql .= "\nGROUP BY `action`";
} else {
  $sql .= "\nGROUP BY `module`";
}

$logs = db_loadObjectList($sql, $logs);

$datas   = array();
$i = 0;
foreach($logs as $data) {
  $datas[$i]["value"] = $data->$element;
  if($module) {
    $datas[$i]["legend"]= $data->action;
  } else {
    $datas[$i]["legend"]= $data->module;
  }
  $i++;
}

function compare($a, $b) {
  global $element;
  return $a["value"] < $b["value"];
}

usort($datas, "compare");
$datas = array_slice($datas, 0, 4);

$values  = array();
$legends = array();
foreach($datas as $data) {
  $values[]  = $data["value"];
  $legends[] = $data["legend"];
}

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

?>