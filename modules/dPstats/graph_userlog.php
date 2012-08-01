<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: 12486 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphUserLog($startx, $endx, $interval, $user_id) {
  switch ($interval) {
    case "day":
      $step = "+1 HOUR";
      $period_format = "%Hh";
      break;
    case "month":
      $step = "+1 DAY";
      $period_format = "%d/%m";
      break;
    case "hyear":
      $step = "+1 WEEK";
      $period_format = "%U";
      break;
    case "twoyears":
      $step = "+1 MONTH";
      $period_format = "%m/%Y";
      break;
    case "twentyyears":
      $step = "+1 YEAR";
      $period_format = "%Y";
      break;
  }
  
  $endx   = ($interval == "day") ? mbDate($endx) : mbDateTime($endx);
  $datax  = array();
  $i = 0;
  for ($d = $startx; $d <= $endx; $d = mbDateTime($step, $d)) {
    $period = mbTransformTime(null, $d, $period_format);
    $datax[$period] = array($i, $period);
    $i++;
  }
  
  // Series data
  $hits = array();

  // Series initialisation
  foreach ($datax as $x) {
    $hits[$x[0]] = array($x[0], 0);
  }
  
  // Load query
  $log = new CUserLog;
  $ds = $log->_spec->ds;
  $query = "
    SELECT 
      DATE_FORMAT(date, '$period_format') AS period,
      COUNT(user_log_id) AS total
    FROM `user_log`
    USE INDEX (date)
    WHERE date BETWEEN '$startx' AND '$endx'";
  $query.= $user_id ? "AND user_id = '$user_id'" : "";
  $query.="
    GROUP BY period 
    ORDER BY period
  ";
  
  foreach ($results = $ds->loadHashList($query) as $_period => $_result) {
    $index = $datax[$_period][0];
    $hits[$index][1] = $_result;
  }

  $datax = array_values($datax);
    
  $title = "Bilan d'utilisation";
  
  $user = CUser::get($user_id);
  $subtitle = $user_id ? 
    "Pour $user->_view" :
    "Tous les utilisateurs"; 
  
  $options = array(
    "title"    => utf8_encode($title),
    "subtitle" => utf8_encode($subtitle),
    "xaxis" => array(
      "labelsAngle" => 45,
      "ticks" => $datax,
    ),
    "yaxis" => array(
      "min" => 0,
      "title" => "Actions",
      "autoscaleMargin" => 1
    ),
    "grid" => array(
      "verticalLines" => false
    ),
    "HtmlText" => false,
    "spreadsheet" => array(
      "show" => true, 
      "csvFileSeparator" => ";",
      "decimalSeparator" => ","
    )
  );
  
  // Right axis (before in order the lines to be on top)
  $series[] = array(
   "label" => "Actions utilisateur",
   "data" => $hits,
   "bars" => array("show" => true),
   "yaxis" => 1
  );
    
  return array(
    "series" => $series, 
    "options" => $options
  );
}

function graphUserLogV2($module_name, $action_name, $startx, $endx, $interval = 'day', $left) {
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
  
  $logs = CUserLog::loadPeriodAggregation($startx, $endx, $period_format, $module_name, $action_name);
  
  if (!$logs) {
    return false;
  }
  
  $count = array();
  
  if ($action_name) {
    $_class = null;
    
    if ($left == "type") {
      foreach ($datax as $x) {
        foreach ($logs as $log) {
          $count[$log['object_class']][$x[0]] = array($x[0], 0);
        }
        
        foreach ($logs as $log) {
          if($x[1] == $log['gperiod']) {
            $count[$log['object_class']][$x[0]] = array($x[0], $log['count']);
          }
        }
      }
    }
    else {
      $_class = array_shift($action_name);
      
      foreach ($datax as $x) {
        foreach ($logs as $log) {
          $count[$log['type']][$x[0]] = array($x[0], 0);
        }
        
        foreach ($logs as $log) {
          if($x[1] == $log['gperiod']) {
            $count[$log['type']][$x[0]] = array($x[0], $log['count']);
          }
        }
      }
    }
  }
  else {
    foreach($datax as $x) {
      // Needed
      $count[$x[0]]    = array($x[0], 0);
      
      foreach ($logs as $log) {
        if($x[1] == $log['gperiod']) {
          $count[$x[0]] = array($x[0], $log['count']);
        }
      }
    }
  }

  
  if ($interval == 'month') {
    foreach($datax as $i => &$x) {
      if ($i % 2) $x[1] = '';
    }
  }
  
  $title = '';
  if ($module_name) {
    $title .= CAppUI::tr("module-$module_name-court");
  }
  
  if ($action_name) {
    if ($left == "type") {
      $title .= " - $action_name";
    }
    else {
      $title .= " - $_class";
    }
    
  }
  
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
      'title' => utf8_encode(($left == 'type' ? 'Type' : ($left == 'classe') ? 'Classe' : ''
                              )
                            ),
      'autoscaleMargin' => 1
    ),
    'y2axis' => array(
      'min' => 0,
      'title' => utf8_encode('Counts'),
      'autoscaleMargin' => 1
    ),
    'grid' => array(
      'verticalLines' => false
    ),
    'HtmlText' => false,
    'spreadsheet' => array(
      'show' => true, 
      'csvFileSeparator' => ';',
      'decimalSeparator' => ','
    ),
  );

  $series = array();
  
  if ($action_name) {
    foreach ($count as $key => $oneCount) {
        $series[] = array(
         'label' => $key,
         'data' => $oneCount,
         'bars' => array('show' => true, 'stacked' => true),
         'yaxis' => 2
       );
    }
  }
  else {
    // Right axis (before in order the lines to be on top)
    $series[] = array(
       'label' => 'Counts',
       'data' => $count,
       'bars' => array('show' => true),
       'yaxis' => 2
     );
  }
  
  return array('series' => $series, 'options' => $options, 'module' => $module_name);
}
?>