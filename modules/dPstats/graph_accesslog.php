<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphAccessLog($module_name, $action_name, $date, $interval = 'day', $left, $right) {
  if (!$date) $date = mbDate();

	switch($interval) {
	  case "day":
	    $startx = "$date 00:00:00";
	    $endx   = "$date 23:59:59";
	    $step = "+1 HOUR";
	    $period_format = "%Hh";
	    $max = 24;
      $hours = 1;
	    break;
	  case "month":
	    $startx = mbDateTime("-1 MONTH", "$date 00:00:00");
	    $endx   = "$date 23:59:59";
	    $step = "+1 DAY";
	    $period_format = "%d/%m";
	    $max = 32;
      $hours = 24;
	    break;
	  case "hyear":
	    $startx = mbDateTime("-6 MONTHS", "$date 00:00:00");
	    $endx   = "$date 23:59:59";
	    $step = "+1 WEEK";
	    $period_format = "%U";
	    $max = 27;
      $hours = 24 * 7;
	    break;
	  case "twoyears":
      $startx = mbDateTime("-2 YEARS", "$date 00:00:00");
      $endx   = "$date 23:59:59";
      $step = "+1 MONTH";
      $period_format = "%m/%Y";
      $max = 25;
      $hours = 24 * 30;
      break;
    case "twentyyears":
      $startx = mbDateTime("-20 YEARS", "$date 00:00:00");
      $endx   = "$date 23:59:59";
      $step = "+1 YEAR";
      $period_format = "%Y";
      $max = 21;
      $hours = 24 * 30 * 12;
      break;
	}
	
	$datax = array();
	$i = 0;
	for($d = $startx; $d <= $endx; $d = mbDateTime($step, $d)) {
	  $datax[] = array($i, mbTransformTime(null, $d, $period_format));
	  $i++;
	}
	
	$logs = CAccessLog::loadPeriodAggregation($startx, $endx, $period_format, $module_name, $action_name);
  
	$duration = array();
	$request = array();
  $errors = array();
  $warnings = array();
  $notices = array();
  
  $hits = array();
  $size = array();
  
  $errors_total = 0;
	foreach($datax as $x) {
	  // Needed
    $duration[$x[0]] = array($x[0], 0);
    $request[$x[0]]  = array($x[0], 0);
    $errors[$x[0]]   = array($x[0], 0);
    $warnings[$x[0]] = array($x[0], 0);
    $notices[$x[0]]  = array($x[0], 0);
    
    $hits[$x[0]] = array($x[0], 0);
    $size[$x[0]] = array($x[0], 0);
    
	  foreach($logs as $log) {
	    if($x[1] == mbTransformTime(null, $log->period, $period_format)) {
	      $duration[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'duration'});
	      $request[$x[0]]  = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'request'});
        $errors[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'errors'});
        $warnings[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'warnings'});
        $notices[$x[0]]  = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'notices'});
        $errors_total += $log->_average_errors + $log->_average_warnings + $log->_average_notices;
        
        $hits[$x[0]] = array($x[0], $log->{($right[1] == 'mean' ? '_average_' : '').'hits'} / ($right[1] == 'mean' ? $hours : 1));
        $size[$x[0]] = array($x[0], $log->{($right[1] == 'mean' ? '_average_' : '').'size'} / ($right[1] == 'mean' ? $hours : 1));
	    }
	  }
	}
	
	if ($interval == 'month') {
	  foreach($datax as $i => &$x) {
	    if ($i % 2) $x[1] = '';
	  }
	}
	
	$title = '';
  if($module_name) $title .= CAppUI::tr("module-$module_name-court");
  if($action_name) $title .= " - $action_name";
  
  $subtitle = mbTransformTime(null, $date, CAppUI::conf("longdate"));
	
	$options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
	  'xaxis' => array(
	    'labelsAngle' => 45,
	    'ticks' => $datax,
	  ),
	  'yaxis' => array(
	    'min' => 0,
      'title' => utf8_encode(($left[0] == 'request_time' ? 'Temps de réponse' : 'Erreurs') . ($left[1] == 'mean' ? ' (par hit)' : '')),
	    'autoscaleMargin' => 1
	  ),
	  'y2axis' => array(
      'min' => 0,
	    'title' => utf8_encode(($right[0] == 'hits' ? 'Hits' : 'Bande passante') . ($right[1] == 'mean' ? (($right[0] == 'hits' ? ' (par minute)' : ' (octets/s)')) : '')),
      'autoscaleMargin' => 1
    ),
    'grid' => array(
      'verticalLines' => false
    ),
    /*'mouse' => array(
      'track' => true,
      'relative' => true
    ),*/
	  'HtmlText' => false,
    'spreadsheet' => array(
		  'show' => true, 
			'csvFileSeparator' => ';',
			'decimalSeparator' => ','
		)
	);
	
	$series = array();
  
  // Right axis (before in order the lines to be on top)
  if ($right[0] == 'hits') {
    $series[] = array(
     'label' => 'Hits',
     'data' => $hits,
     'bars' => array('show' => true),
     'yaxis' => 2
    );
  }
  
  else {
    $series[] = array(
     'label' => 'Bande passante',
     'data' => $size,
     'bars' => array('show' => true),
     'yaxis' => 2
    );
  }
  
  // Left axis
  if ($left[0] == 'request_time') {
  	$series[] = array(
     'label' => 'Page (s)',
     'data' => $duration,
     'lines' => array('show' => true),
    );
      
    $series[] = array(
     'label' => 'DB (s)',
     'data' => $request,
     'lines' => array('show' => true),
    );
  }
  
  else {
    if ($errors_total == 0) {
      $options['yaxis']['max'] = 1;
    }
    
    $series[] = array(
     'label' => 'Errors',
     'data' => $errors,
     'color' => 'red',
     'lines' => array('show' => true),
    );
    
    $series[] = array(
     'label' => 'Warnings',
     'data' => $warnings,
     'color' => 'orange',
     'lines' => array('show' => true),
    );
      
    $series[] = array(
     'label' => 'Notices',
     'data' => $notices,
     'color' => 'yellow',
     'lines' => array('show' => true),
    );
  }
	
	return array('series' => $series, 'options' => $options, 'module' => $module_name);
}
?>