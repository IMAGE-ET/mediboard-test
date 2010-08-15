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

$debut      = CValue::get("debut"     , mbDate("-1 YEAR"));
$fin        = CValue::get("fin"       , mbDate()         );
$prat_id    = CValue::get("prat_id"   , 0                );
$service_id = CValue::get("service_id", 0                );

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$service = new CService;
$service->load($service_id);

$datax = array("ticks" => array(), "date" => array());
for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax["ticks"][] = mbTransformTime("+0 DAY", $i, "%m/%Y");
  $datax["date"][]  = mbTransformTime("+0 DAY", $i, "%Y-%m");
}

$sql = "SELECT * FROM service";
if($service_id)
  $sql .= "\nWHERE service_id = '$service_id'";
$ds = CSQLDataSource::get("std");
$services = $ds->loadlist($sql);

$maxDuree = 0;
$hoursByService = array();
foreach($services as $service) {
  $sql = "SELECT COUNT(lit.lit_id) AS total" .
      "\nFROM lit, chambre, service" .
      "\nWHERE lit.chambre_id = chambre.chambre_id" .
      "\nAND chambre.service_id = service.service_id" .
      "\nAND service.service_id = '".$service["service_id"]."'" .
      "\nGROUP BY service.service_id";
  $result = $ds->loadResult($sql);
  $numLits = $result["total"];
  foreach($datax["date"] as $x) {
    $debMonth = $x."-01";
    $endMonth = $debMonth;
    $endMonth = mbDate("+ 1 MONTH", $endMonth);
    $endMonth = mbDate("-1 DAY", $endMonth);
    $f = true;
    $sql = "SELECT affectation.entree, affectation.sortie" .
        "\nFROM affectation, operations, service, chambre, lit" .
        "\nWHERE operations.operation_id = affectation.operation_id";
    if($prat_id)
      $sql .= "\nAND operations.chir_id = '$prat_id'";
    $sql .= "\nAND affectation.lit_id = lit.lit_id" .
        "\nAND lit.chambre_id = chambre.chambre_id" .
        "\nAND chambre.service_id = service.service_id" .
        "\nAND service.service_id = '".$service["service_id"]."'" .
        "\nAND" .
          "\n(DATE_FORMAT(affectation.entree, '%Y-%m') = '".$x."'" .
          "\nOR DATE_FORMAT(affectation.sortie, '%Y-%m') = '".$x."')";
    $result = $ds->loadlist($sql);
    $duree = 0;
    foreach($result as $value) {
      $entree = strtotime(max($value["entree"], $debMonth));
      $sortie = strtotime(max($value["sortie"], $endMonth));
      $duree += $sortie - $entree;
    }
    $hoursByService[$service["service_id"]]["nom"] = $service["nom"];
    $maxDuree = max($maxDuree, $hoursByService[$service["service_id"]]["total"][] = ($duree/($numLits*60*60*60*24))*24/100);
  }
}

// Setup the graph.
$graph = new Graph(480,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin", 0, $maxDuree);
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Occupation moyenne des lits par mois (en heures par jour)";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr $pratSel->_view ";
}
if($service_id) {
  $subtitle .= "- $service->nom ";
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

// Setup font for axis
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax["ticks"]);
$graph->xaxis->SetPosAbsDelta(15);
$graph->yaxis->SetPosAbsDelta(-15);
$graph->xaxis->SetLabelAngle(50);

// Legend
$graph->legend->SetMarkAbsSize(5);
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
$graph->legend->Pos(0.02,0.02, "right", "top");

// Create the bar plot
$colors = array("#aa5500", "#55aa00", "#0055aa", "#aa0055", "#5500aa", "#00aa55");
$listPlots = array();
foreach($hoursByService as $key => $value) {
  $bplot = new BarPlot($value["total"]);
  $from = $colors[$key];
  $to = "#EEEEEE";
  //$bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
  $bplot->SetFillColor($colors[$key]);
  $bplot->setLegend($value["nom"]);
  $bplot->value->SetFormat("%01.0f");
  $bplot->value->SetColor($colors[$key]);
  $bplot->value->SetFont(FF_ARIAL,FS_NORMAL, 8); 
  //$bplot->value->show();
  $listPlots[] = $bplot;
}
//mbTrace($listPlots, "listPlots", true, true);
$gbarplot = new GroupBarPlot($listPlots);
$graph->Add($gbarplot);

// Finally send the graph to the browser
$graph->Stroke();