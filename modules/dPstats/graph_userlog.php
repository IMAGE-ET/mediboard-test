<?php /* $Id: graph_accesslog.php 12486 2011-06-21 08:18:52Z rhum1 $ */

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
    /*
    "mouse" => array(
      "track" => true,
      "relative" => true
    ),
    */
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
?>