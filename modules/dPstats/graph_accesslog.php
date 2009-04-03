<?php /* $Id: graph_patjoursalle.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 23 $
* @author Romain Ollivier
*/

function graphAccessLog($module, $actionName, $date, $interval = 'day') {
  if (!$date) $date = mbDate();

	switch($interval) {
	  case "day":
	    $startx = "$date 00:00:00";
	    $endx   = "$date 23:59:59";
	    $step = "+1 HOUR";
	    $date_format = "%Hh";
	    $max = 24;
	    break;
	  case "month":
	    $startx = mbDateTime("-1 MONTH", "$date 00:00:00");
	    $endx   = "$date 23:59:59";
	    $step = "+1 DAY";
	    $date_format = "%d/%m";
	    $max = 32;
	    break;
	  case "hyear":
	    $startx = mbDateTime("-6 MONTHS", "$date 00:00:00");
	    $endx   = "$date 23:59:59";
	    $step = "+1 WEEK";
	    $date_format = "%U";
	    $max = 27;
	    break;
	  case "twoyears":
      $startx = mbDateTime("-2 YEARS", "$date 00:00:00");
      $endx   = "$date 23:59:59";
      $step = "+1 MONTH";
      $date_format = "%m/%Y";
      $max = 25;
      break;
    case "twentyyears":
      $startx = mbDateTime("-20 YEARS", "$date 00:00:00");
      $endx   = "$date 23:59:59";
      $step = "+1 YEAR";
      $date_format = "%Y";
      $max = 21;
      break;
	}
	
	$datax = array();
	$i = 0;
	for($d = $startx; $d <= $endx; $d = mbDateTime($step, $d)) {
	  $datax[] = array($i, mbTransformTime(null, $d, $date_format));
	  $i++;
	}
	
	$logs = new CAccessLog();
	
	$sql = "SELECT `accesslog_id`, `module`, `action`, `period`,
	      SUM(`hits`) AS `hits`, SUM(`duration`) AS `duration`, SUM(`request`) AS `request`,
	      DATE_FORMAT(`period`, '$date_format') AS `gperiod`
	      FROM `access_log`
	      WHERE DATE(`period`) BETWEEN '".mbDate($startx)."' AND '".mbDate($endx)."'";
	if($module) {
	  $sql .= "\nAND `module` = '$module'";
	}
	if($actionName) {
	  $sql .= "\nAND `action` = '$actionName'";
	}
	$sql .= "\nGROUP BY `gperiod` ORDER BY `period`";
	
	$logs = $logs->loadQueryList($sql);
	
	$nbHits = array();
	$duration = array();
	$request = array();
	foreach($datax as $x) {
    $nbHits[$x[0]] = array($x[0], 0);
    $duration[$x[0]] = array($x[0], 0);
    $request[$x[0]] = array($x[0], 0);
    
	  foreach($logs as $log) {
	    if($x[1] == mbTransformTime(null, $log->period, $date_format)) {
	      $nbHits[$x[0]] = array($x[0], $log->hits);
	      $duration[$x[0]] = array($x[0], $log->_average_duration);
	      $request[$x[0]] = array($x[0], $log->_average_request);
	    }
	  }
	}
	
  foreach($datax as $i => &$x) {
    if ($i % 2) $x[1] = '';
  }
	
	$title = '';
  if($module) $title .= CAppUI::tr($module);
  if($actionName) $title .= " - $actionName";
  
  $subtitle = mbTransformTime(null, $date, "%A %d %b %Y");
	
	$options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
	  'xaxis' => array(
	    'labelsAngle' => 45,
	    'ticks' => $datax,
	  ),
	  'yaxis' => array(
	    'min' => 0,
	    'autoscaleMargin' => 1
	  ),
	  'y2axis' => array(
      'min' => 0,
	    'title' => 'Hits',
      'autoscaleMargin' => 1
    ),
    'grid' => array(
      'verticalLines' => false
    ),
	  'HtmlText' => false
	);
	
	$series = array(
    array(
     'label' => 'Hits',
     'data' => $nbHits,
     'bars' => array('show' => true),
     'yaxis' => 2
    ),
	  array(
	   'label' => 'Page (s)',
	   'data' => $duration,
	   'lines' => array('show' => true),
	  ),
    array(
     'label' => 'DB (s)',
     'data' => $request,
     'lines' => array('show' => true),
    ),
	);
	
	return array('series' => $series, 'options' => $options);
}
?>