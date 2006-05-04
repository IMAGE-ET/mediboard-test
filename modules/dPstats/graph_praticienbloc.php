<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPbloc', 'salle') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_line'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_regstat'));

$debut    = mbGetValueFromGet("debut"   , mbDate("-1 YEAR"));
$fin      = mbGetValueFromGet("fin"     , mbDate());
$prat_id  = mbGetValueFromGet("prat_id" , 0);
$salle_id = mbGetValueFromGet("salle_id", 0);
$codeCCAM = mbGetValueFromGet("codeCCAM", "");

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$salleSel = new CSalle;
$salleSel->load($salle_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTranformTime("+0 DAY", $i, "%m/%Y");
}

$sql = "SELECT * FROM sallesbloc WHERE stats = 1";
if($salle_id)
  $sql .= "\nAND id = '$salle_id'";
$salles = db_loadlist($sql);

$nbHours = array();
$sql = "SELECT SUM(TIME_TO_SEC(plagesop.fin) - TIME_TO_SEC(plagesop.debut)) AS total," .
  "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
  "\nDATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem" .
  "\nFROM plagesop, sallesbloc" .
  "\nWHERE plagesop.id_salle = sallesbloc.id" .
  "\nAND sallesbloc.stats = 1" .
  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'";
  if($prat_id)
    $sql .= "\nAND plagesop.chir_id = '$prat_id'";
  if($salle_id)
    $sql .= "\nAND plagesop.id_salle = '$salle_id'";
$sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
$result = db_loadlist($sql);
foreach($datax as $x) {
  $f = true;
  foreach($result as $total) {
    if($x == $total["mois"]) {
      $nbHours[] = $total["total"]/(60*60);
      $f = false;
    }
  }
  if($f) {
    $nbHours[] = 0;
  }
}

$doneHours = array();
$sql = "SELECT SUM(TIME_TO_SEC(operations.sortie_bloc) - TIME_TO_SEC(operations.entree_bloc)) AS total," .
  "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
  "\nDATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem" .
  "\nFROM plagesop, sallesbloc" .
  "\nLEFT JOIN operations" .
  "\nON operations.plageop_id = plagesop.id" .
  "\nAND operations.annulee = 0" .
  "\nWHERE plagesop.id_salle = sallesbloc.id" .
  "\nAND sallesbloc.stats = 1" .
  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'";
  if($prat_id)
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  if($salle_id)
    $sql .= "\nAND plagesop.id_salle = '$salle_id'";
$sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
$result = db_loadlist($sql);
foreach($datax as $x) {
  $f = true;
  foreach($result as $total) {
    if($x == $total["mois"]) {
      $doneHours[] = $total["total"]/(60*60);
      $f = false;
    }
  }
  if($f) {
    $doneHours[] = 0;
  }
}

// Setup the graph.
$graph = new Graph(500,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Heures réservées / occupées par mois";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr. $pratSel->_view ";
}
if($salle_id) {
  $subtitle .= "- $salleSel->nom ";
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
$hours1Sorted = $nbHours;
rsort($hours1Sorted);
$hours2Sorted = $doneHours;
rsort($hours2Sorted);
$scale = max(intval($hours1Sorted[0]), intval($hours2Sorted[0]));
$graph->SetScale("intint", 0, $scale + $scale/10);

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

// Legend
$graph->legend->SetMarkAbsSize(5);
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
$graph->legend->Pos(0.02,0.02, "right", "top");

// Create the first plot
$lplot = new LinePlot($nbHours);
$lplot->SetColor("blue");
$lplot->SetWeight(0);
$lplot->value->SetFormat("%01.2f");
$lplot->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
$lplot->value->SetMargin(10);
$lplot->mark->SetType(MARK_FILLEDCIRCLE);
$lplot->mark->SetColor("blue");
$lplot->mark->SetFillColor("blue:1.5");
$lplot->value->show();
$lplot->setLegend("Réservé");

// Create the first spline plot
$spline = new Spline(array_keys($datax), array_values($nbHours));
list($sdatax,$sdatay) = $spline->Get(50);
$splot = new LinePlot($sdatay, $sdatax);
//$splot->SetFillGradient("white", "darkgray");
$splot->SetColor("black");

// Create the second plot
$lplot2 = new LinePlot($doneHours);
$lplot2->SetColor("blue");
$lplot2->SetWeight(0);
$lplot2->value->SetFormat("%01.2f");
$lplot2->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
$lplot2->value->SetMargin(10);
$lplot2->mark->SetType(MARK_FILLEDCIRCLE);
$lplot2->mark->SetColor("red");
$lplot2->mark->SetFillColor("red:1.5");
$lplot2->value->show();
$lplot2->setLegend("Occupé");

// Create the first spline plot
$spline2 = new Spline(array_keys($datax), array_values($doneHours));
list($sdatax2,$sdatay2) = $spline2->Get(50);
$splot2 = new LinePlot($sdatay2, $sdatax2);
//$splot2->SetFillGradient("white", "darkgray");
$splot2->SetColor("black");

// Add the plots to the graph
$graph->Add($splot);
$graph->Add($lplot);
$graph->Add($splot2);
$graph->Add($lplot2);

// Finally send the graph to the browser
$graph->Stroke();