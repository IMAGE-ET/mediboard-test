<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

CAppUI::requireLibraryFile("jpgraph/src/mbjpgraph");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_bar");

$user_id = CValue::get("user_id", $AppUI->user_id);
$user = new CMediusers;
$user->load($user_id);
$debut = CValue::get("debut", mbDate("-1 WEEK"));
$fin = CValue::get("fin", mbDate());

$sql = "SELECT COUNT(user_log.user_log_id) AS total," .
    "\nDATE_FORMAT(user_log.date, '%Y-%m-%d') AS day" .
    "\nFROM user_log" .
    "\nWHERE user_log.date BETWEEN '$debut' AND '$fin 23:59:59'" .
    "\nAND user_log.user_id = '$user_id'" .
    "\nGROUP BY day" .
    "\nORDER BY day";
$ds = CSQLDataSource::get("std");
$logs = $ds->loadlist($sql);
$datax = array();
$datay = array();
for($i = $debut; $i <= $fin; $i = mbDate("+1 DAY", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%a %d/%m/%Y");
  $f = true;
  foreach($logs as $value) {
    if($value["day"] == $i) {
      $datay[] = $value["total"];
      $f = false;
    }
  }
  if($f)
    $datay[] = 0;
}

$options = array( "width" => 400,
									"height" => 300,
									"title" => $user->_view,
									"sizeFontTitle" => 10,
									"margin" => array(40,10,30,70),
									"sizeFontAxis" => 6,
									"labelAngle" => 50,
									"textTickInterval" => 1,
									"from" => "navy",
									"to" => "#EEEEEE",
									"graphBarColor" => "white",
									"dataBar" => $datay,
									"datax" => $datax );
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addDataBarPlot($options);
$graph->render("out",$options);

?>