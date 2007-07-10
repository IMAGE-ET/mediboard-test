<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

require_once($AppUI->getSystemClass("mbGraph"));
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

// Set up the title for the graph
$title = mbTranformTime(null, $date, "%A %d %b %Y");
if($module) 		
	$title .= " : ".$AppUI->_($module);

$graph = new CMbGraph();
$graph->selectType("Pie",$title,$size);
$graph->selectPalette("lightblue");
$graph->addDataPiePlot($datas,$title,$size);
$graph->render("out",$size);

?>