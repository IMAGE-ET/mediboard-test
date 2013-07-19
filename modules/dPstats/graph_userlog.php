<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
  
  $endx   = ($interval == "day") ? CMbDT::date($endx) : CMbDT::dateTime($endx);
  $datax  = array();
  $i = 0;
  for ($d = $startx; $d <= $endx; $d = CMbDT::dateTime($step, $d)) {
    $period = CMbDT::format($d, $period_format);
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

/**
 * User logs graphic for system view
 *
 * @param datetime $startx    Datetime where the search starts
 * @param datetime $endx      Datetime where the search ends
 * @param string   $period    Aggregation period
 * @param string   $type      User log type to filter
 * @param int      $user_id   User ID to filter
 * @param string   $class     Class to filter
 * @param int      $object_id Object ID to filter
 *
 * @return array|bool
 */
function graphUserLogSystem($startx, $endx, $period, $type = null, $user_id = null, $class = null, $object_id = null) {
  switch ($period) {
    default:
    case "hour":
      $step = "+1 HOUR";
      $period_format = "%d-%m-%Y %Hh";
      break;

    case "day":
      $step = "+1 DAY";
      $period_format = "%d-%m-%Y";
      break;

    case "week":
      $step = "+1 WEEK";
      $period_format = "%Y Sem. %W";
      break;

    case "month":
      $step = "+1 MONTH";
      $period_format = "%m-%Y";
      break;

    case "year":
      $step = "+1 YEAR";
      $period_format = "%Y";
  }

  $datax = array();
  $i = 0;
  for ($d = $startx; $d <= $endx; $d = CMbDT::dateTime($step, $d)) {
    $datax[] = array($i, CMbDT::format($d, $period_format));
    $i++;
  }

  // Compute a good spacement for label ticks
  $count_datax = count($datax);
  $space       = 1;
  if ($count_datax > 35) {
    $space = round($count_datax / 35);
  }

  $logs = CUserLog::loadPeriodAggregation($startx, $endx, $period, $type, $user_id, $class, $object_id);

  if (!$logs) {
    return false;
  }

  $count = array();

  foreach ($datax as $x) {
    // Needed
    $count[$x[0]] = array($x[0], 0);

    foreach ($logs as $_log) {
      if ($x[1] == $_log['gperiod']) {
        $count[$x[0]] = array($x[0], $_log['count'], $_log['gperiod']);
      }
    }
  }

  // Label ticks spacement
  foreach ($datax as $i => &$x) {
    if ($i % $space) $x[1] = '';
  }

  $subtitle = CMbDT::format($endx, CAppUI::conf("longdate"));

  $options = array(
    'subtitle' => utf8_encode($subtitle),
    'xaxis' => array(
      'labelsAngle' => 45,
      'ticks'       => $datax,
    ),
    'yaxis' => array(
      'min'             => 0,
      'autoscaleMargin' => 1
    ),
    'y2axis' => array(
      'min'             => 0,
      'title'           => utf8_encode('Quantité'),
      'autoscaleMargin' => 1
    ),
    'grid' => array(
      'verticalLines' => false
    ),
    'HtmlText' => false,
    'spreadsheet' => array(
      'show'             => true,
      'csvFileSeparator' => ';',
      'decimalSeparator' => ','
    ),
    'mouse' => array(
      'track' => true
    )
  );

  $series = array();

  // Right axis (before in order the lines to be on top)
  $series[] = array(
    'data'    => $count,
    'bars'    => array('show' => true),
    'yaxis'   => 2
  );

  return array('series' => $series, 'options' => $options);
}
