<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphAccessLog($module_name, $action_name, $startx, $endx, $interval = 'day', $left, $right) {
  switch($interval) {
    case "day":
      $step = "+1 HOUR";
      $period_format = "%Hh";
      $max = 24;
      $hours = 1;
      break;
    case "month":
      $step = "+1 DAY";
      $period_format = "%d/%m";
      $max = 32;
      $hours = 24;
      break;
    case "hyear":
      $step = "+1 WEEK";
      $period_format = "%U";
      $max = 27;
      $hours = 24 * 7;
      break;
    case "twoyears":
      $step = "+1 MONTH";
      $period_format = "%m/%Y";
      $max = 25;
      $hours = 24 * 30;
      break;
    case "twentyyears":
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
  
  $duration  = array();
  $processus = array();
  $processor = array();
  $request   = array();
  $errors    = array();
  $warnings  = array();
  $notices   = array();
  
  $hits = array();
  $size = array();
  
  $errors_total = 0;
  foreach($datax as $x) {
    // Needed
    $duration[$x[0]]    = array($x[0], 0);
    $processus[$x[0]]   = array($x[0], 0);
    $processor[$x[0]]   = array($x[0], 0);
    $request[$x[0]]     = array($x[0], 0);
    $peak_memory[$x[0]] = array($x[0], 0);
    $errors[$x[0]]      = array($x[0], 0);
    $warnings[$x[0]]    = array($x[0], 0);
    $notices[$x[0]]     = array($x[0], 0);
    
    $hits[$x[0]] = array($x[0], 0);
    $size[$x[0]] = array($x[0], 0);
    
    foreach($logs as $log) {
      if($x[1] == mbTransformTime(null, $log->period, $period_format)) {
        $duration[$x[0]]    = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'duration'});
        $processus[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'processus'});
        $processor[$x[0]]   = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'processor'});
        $request[$x[0]]     = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'request'});
        $peak_memory[$x[0]] = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'peak_memory'});
        $errors[$x[0]]      = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'errors'});
        $warnings[$x[0]]    = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'warnings'});
        $notices[$x[0]]     = array($x[0], $log->{($left[1] == 'mean' ? '_average_' : '').'notices'});
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
  
  $subtitle = mbTransformTime(null, $endx, CAppUI::conf("longdate"));
  
  $options = array(
    'title' => utf8_encode($title),
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array(
      'labelsAngle' => 45,
      'ticks' => $datax,
    ),
    'yaxis' => array(
      'min' => 0,
      'title' => utf8_encode(($left[0] == 'request_time' ? 'Temps de réponse' :
                                ($left[0] == 'cpu_time' ? 'Temps CPU' :
                                  ($left[0] == 'errors' ? 'Erreurs' : 'Mémoire'))) .
                              ($left[1] == 'mean' ? ' (par hit)' : '')),
      'autoscaleMargin' => 1
    ),
    'y2axis' => array(
      'min' => 0,
      'title' => utf8_encode(($right[0] == 'hits' ? 'Hits' : 'Bande passante') .
                             ($right[1] == 'mean' ? (($right[0] == 'hits' ? ' (par minute)' : ' (octets/s)')) : '')),
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
  } elseif($left[0] == 'cpu_time') {
    $series[] = array(
     'label' => 'Page (s)',
     'data' => $duration,
     'lines' => array('show' => true),
    );
      
    $series[] = array(
     'label' => 'Process (s)',
     'data' => $processus,
     'lines' => array('show' => true),
    );
      
    $series[] = array(
     'label' => 'CPU (s)',
     'data' => $processor,
     'lines' => array('show' => true),
    );
      
    $series[] = array(
     'label' => 'DB (s)',
     'data' => $request,
     'lines' => array('show' => true),
    );
  }
  
  elseif ($left[0] == 'errors') {
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
  
  else {
    $series[] = array(
     'label' => 'Pic (byte)',
     'data' => $peak_memory,
     'lines' => array('show' => true),
    );
  }
  
  return array('series' => $series, 'options' => $options, 'module' => $module_name);
}
?>