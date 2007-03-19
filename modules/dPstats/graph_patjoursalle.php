<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

require_once($AppUI->getLibraryFile("jpgraph/src/mbjpgraph"        ));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_line"   ));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_regstat"));

$debut    = mbGetValueFromGet("debut"   , mbDate("-1 YEAR"));
$fin      = mbGetValueFromGet("fin"     , mbDate()         );
$prat_id  = mbGetValueFromGet("prat_id" , 0                );
$salle_id = mbGetValueFromGet("salle_id", 0                );
$codeCCAM = mbGetValueFromGet("codeCCAM", ""               );

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$salleSel = new CSalle;
$salleSel->load($salle_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTranformTime("+0 DAY", $i, "%m/%Y");
}

$sql = "SELECT * FROM sallesbloc WHERE stats = '1'";
if($salle_id)
  $sql .= "\nAND salle_id = '$salle_id'";
$salles = db_loadlist($sql);

$op = array();
$sql = "SELECT COUNT(operations.operation_id) AS total," .
  "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
  "\nDATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem" .
  "\nFROM plagesop" .
  "\nINNER JOIN sallesbloc" .
  "\nON plagesop.salle_id = sallesbloc.salle_id" .
  "\nLEFT JOIN operations" .
  "\nON operations.plageop_id = plagesop.plageop_id" .
  "\nAND operations.annulee = '0'" .
  "\nWHERE sallesbloc.stats = '1'" .
  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'";
  if($prat_id)
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM)
    $sql .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  if($salle_id)
    $sql .= "\nAND plagesop.salle_id = '$salle_id'";
$sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
$result = db_loadlist($sql);
foreach($datax as $x) {
  $f = true;
  foreach($result as $total) {
    if($x == $total["mois"]) {
      $nbjours = mbWorkDaysInMonth($total["orderitem"]);
      $op[] = $total["total"]/($nbjours*count($salles));
      $f = false;
    }
  }
  if($f) {
    $op[] = 0;
  }
}

// Setup the graph.
$graph = new Graph(500,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Patients / jour / salle";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr. $pratSel->_view ";
}
if($salle_id) {
  $subtitle .= "- $salleSel->nom ";
}
if($codeCCAM) {
  $subtitle .= "- CCAM : $codeCCAM ";
}
if($subtitle) {
  $subtitle .= "-";
  $graph->subtitle->Set($subtitle);
}
$graph->title->Set($title);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,10);
$graph->title->SetColor("darkred");
$graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,7);
$graph->subtitle->SetColor("black");
//$graph->img->SetAntiAliasing();
$opSorted = $op;
rsort($opSorted);
$graph->SetScale("intint", 0, intval($opSorted[0])+1);

// Setup font for axis
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetPosAbsDelta(15);
$graph->xgrid->Show();
$graph->xaxis->SetLabelAngle(50);
$graph->yaxis->SetPosAbsDelta(-15);

// Create the plot
$lplot = new LinePlot($op);
$lplot->SetColor("blue");
$lplot->SetWeight(-10);
$lplot->value->SetFormat("%01.2f");
$lplot->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
$lplot->value->SetMargin(10);
$lplot->mark->SetType(MARK_FILLEDCIRCLE);
$lplot->mark->SetColor("blue");
$lplot->mark->SetFillColor("blue:1.5");
$lplot->value->show();

// Create the spline plot
$spline = new Spline(array_keys($datax), array_values($op));
list($sdatax,$sdatay) = $spline->Get(50);
$lplot2 = new LinePlot($sdatay, $sdatax);
$lplot2->SetFillGradient("white", "darkgray");
$lplot2->SetColor("black");

// Add the plots to the graph
$graph->Add($lplot2);
$graph->Add($lplot);

// Finally send the graph to the browser
$graph->Stroke();