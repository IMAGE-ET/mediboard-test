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

$debut    = mbGetValueFromGet("debut"       , mbDate("-1 YEAR"));
$fin      = mbGetValueFromGet("fin"         , mbDate()         );
$prat_id  = mbGetValueFromGet("prat_id"     , 0                );
$service_id = mbGetValueFromGet("service_id", 0                );

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$serviceSel = new CSalle;
$serviceSel->load($service_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%m/%Y");
}

$sql = "SELECT * FROM service WHERE";
if($service_id)
  $sql .= "\nAND id = '$service_id'";
  
$ds = CSQLDataSource::get("std");
$services = $ds->loadlist($sql);

$opbysalle = array();
foreach($services as $service) {
  $id = $service["service_id"];
  $opbysalle[$id]["nom"] = $salle["nom"];
  $sql = "SELECT COUNT(sejour.sejour_id) AS total," .
    "\nDATE_FORMAT(sejour.entree_prevue, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(sejour.entre_prevue, '%Y%m') AS orderitem," .
    "\nservice.nom AS nom" .
    "\nFROM sejour, affectation, services, chambre, lit" .
    "\nWHERE sejour.annule = '0'" .
    "\nAND sejour.entree_prevue BETWEEN '$debut' AND '$fin'";
  if($prat_id)
    $sql .= "\nAND sejour.praticien_id = '$prat_id'";
  $sql .= "\nAND service.service_id = chambre.service_id" .
    "\nAND chambre.chambre_id = lit.chambre_id" .
    "\nAND lit.affectation_id = affectation.affectation_id" .
    "\nAND affectation.sejour_id = sejour.sejour_id" .
    "\nAND service.service_id = '$id'" .
    "\nGROUP BY mois" .
    "\nORDER BY orderitem";
    
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $opbysalle[$id]["sejour"][] = $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $opbysalle[$id]["sejour"][] = 0;
    }
  }
}

// Setup the graph.
$graph = new Graph(480,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Interventions par mois";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr $pratSel->_view ";
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
$graph->SetScale("textint");

// Setup font for axis
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetPosAbsDelta(15);
$graph->yaxis->SetPosAbsDelta(-15);
$graph->xaxis->SetLabelAngle(50);

// Legend
$graph->legend->SetMarkAbsSize(5);
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
$graph->legend->Pos(0.02,0.02, "right", "top");

// Create the bar pot
$colors = array("#aa5500",
                "#55aa00",
                "#0055aa",
                "#aa0055",
                "#5500aa",
                "#00aa55",
                "#ff0000",
                "#00ff00",
                "#0000ff",
                "#ffff00",
                "#ff00ff",
                "#00ffff",);
$listPlots = array();
foreach($opbysalle as $key => $value) {
  $bplot = new BarPlot($value["sejour"]);
  $from = $colors[$key];
  $to = "#EEEEEE";
  $bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
  $bplot->SetColor("white");
  $bplot->setLegend($value["nom"]);
  $bplot->value->SetFormat("%01.0f");
  $bplot->value->SetColor($colors[$key]);
  $bplot->value->SetFont(FF_ARIAL,FS_NORMAL, 8); 
  //$bplot->value->show();
  $listPlots[] = $bplot;
}

$gbplot = new AccBarPlot($listPlots);
$gbplot->SetWidth(0.6);
$gbplot->value->SetFormat("%01.0f"); 
$gbplot->value->show();

// Set color for the frame of each bar
$graph->Add($gbplot);

// Finally send the graph to the browser
$graph->Stroke();