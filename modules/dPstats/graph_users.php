<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_bar'));

$user_id = mbGetValueFromGet("user_id", $AppUI->user_id);
$user = new CMediusers;
$user->load($user_id);
$debut = mbGetValueFromGet("debut", mbDate("-1 WEEK"));
$fin = mbGetValueFromGet("fin", mbDate());

$sql = "SELECT COUNT(user_log.user_log_id) AS total," .
    "\nDATE_FORMAT(user_log.date, '%Y-%m-%d') AS day" .
    "\nFROM user_log" .
    "\nWHERE user_log.date BETWEEN '$debut' AND '$fin'" .
    "\nAND user_log.user_id = '$user_id'" .
    "\nGROUP BY day" .
    "\nORDER BY day";
$logs = db_loadlist($sql);
$datax = array();
$datay = array();
for($i = $debut; $i <= $fin; $i = mbDate("+1 DAY", $i)) {
  $datax[] = mbTranformTime("+0 DAY", $i, "%a %d/%m/%Y");
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

// Setup the graph.
$graph = new Graph(400,300,"auto");    
$graph->img->SetMargin(40,10,30,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");
//$graph->SetShadow();

// Set up the title for the graph
$graph->title->Set($user->_view);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,10);
$graph->title->SetColor("darkred");

// Setup font for axis
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(50);

// Create the bar pot
$bplot = new BarPlot($datay);
$bplot->SetWidth(0.6);
$bplot->SetFillGradient("navy","#EEEEEE",GRAD_LEFT_REFLECTION);
$bplot->SetColor("white");

// Set color for the frame of each bar
$graph->Add($bplot);

// Finally send the graph to the browser
$graph->Stroke();