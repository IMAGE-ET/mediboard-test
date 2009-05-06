<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphRessourceLog($module, $date, $element = 'duration', $interval = 'day', $numelem = 4) {
	if (!$date) $date = mbDate();
	
	switch($interval) {
		default:
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
	
	$sql = "SELECT `accesslog_id`, `module`, `action`, `period`,
	      SUM(`hits`) AS `hits`, SUM(`duration`) AS `duration`, SUM(`request`) AS `request`
	      FROM `access_log`
	      WHERE DATE(`period`) BETWEEN '".mbDate($startx)."' AND '".mbDate($endx)."'";
	if($module == "total") {
	  $sql .= "\nGROUP BY `action`";
	}
	elseif($module == "modules") {
	  $sql .= "\nGROUP BY `module`";
	}
	else {
	  $sql .= "\nAND `module` = '$module' GROUP BY `action`";
	}
	
	$logs = $logs->loadQueryList($sql);
	
	$series = array();
	$i = 0;
	foreach($logs as $data) {
	  $series[$i]["data"] = array(array(0, $data->$element));
	  $series[$i]["label"] = $module != 'modules' ? $data->action : $data->module;
	  $i++;
	}
	
	if (!function_exists('compareDataPie')) {
		function compareDataPie($a, $b) {
		  return $a["data"][0][1] < $b["data"][0][1];
		}
	}
	
	usort($series, "compareDataPie");
	$seriesNew = array_slice($series, 0, $numelem);
	if(count($series) > $numelem) {
	  $other = array_slice($series, $numelem);
	  $seriesNew[$numelem]["data"] = array(array(0, 0));
	  $seriesNew[$numelem]["label"] = "Autres";
	  $n = 0;
	  foreach($other as $curr_other) {
	    $seriesNew[$numelem]["data"][0][1] += $curr_other["data"][0][1];
	    $n++;
	  }
	  $seriesNew[$numelem]["label"] .= " ($n)";
	}
	$series = $seriesNew;
	
	// Set up the title for the graph
	$title = mbTransformTime(null, $date, "%A %d %b %Y");
	if($module) $title .= " : ".CAppUI::tr($module);
		
	$options = array(
	  'title' => utf8_encode($title),
	  'HtmlText' => false,
	  'grid' => array(
	    'verticalLines' => false,
	    'horizontalLines' => false,
	    'outlineWidth' => 0
	  ),
	  'xaxis' => array('showLabels' => false),
	  'yaxis' => array('showLabels' => false),
	  'pie' => array(
	    'show' => true,
      'sizeRatio' => 0.5
	  ),
	  'legend' => array(
	    'backgroundOpacity' => 0.3
	  ) 
	);
		
	return array('series' => $series, 'options' => $options);
}
?>