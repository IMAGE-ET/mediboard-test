<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getLibraryFile("jpgraph/src/mbjpgraph"    ));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_bar"));

$debut         = mbGetValueFromGet("debut"        , mbDate("-1 YEAR"));
$fin           = mbGetValueFromGet("fin"          , mbDate()         );
$prat_id       = mbGetValueFromGet("prat_id"      , 0                );
$service_id    = mbGetValueFromGet("service_id"   , 0                );
$type_adm      = mbGetValueFromGet("type_adm"     , 0                );
$discipline_id = mbGetValueFromGet("discipline_id", 0                );

$sejour = new CSejour;
$listHospis = array();
$listHospis[1] = "Hospi complètes + ambu";
$listHospis = $listHospis + $sejour->_enumsTrans["type"];

$total = 0;

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$serviceSel = new CService;
$serviceSel->load($service_id);

$disciplineSel = new CDiscipline;
$disciplineSel->load($discipline_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTranformTime("+0 DAY", $i, "%m/%Y");
}

$sql = "SELECT * FROM service";
if($service_id)
  $sql .= "\nWHERE service_id = '$service_id'";
$services = db_loadlist($sql);

$patbyservice = array();
foreach($services as $service) {
  $id = $service["service_id"];
  $patbyservice[$id]["nom"] = $service["nom"];
  $sql = "SELECT COUNT(DISTINCT affectation.sejour_id) AS total," .
    "\nservice.nom AS nom," .
    "\nDATE_FORMAT(affectation.entree, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(affectation.entree, '%Y%m') AS orderitem" .
    "\nFROM sejour" .
    "\nINNER JOIN users_mediboard" .
    "\nON sejour.praticien_id = users_mediboard.user_id" .
    "\nINNER JOIN affectation" .
    "\nON sejour.sejour_id = affectation.sejour_id" .
    "\nAND affectation.entree BETWEEN '$debut' AND '$fin'" .
    "\nINNER JOIN lit" .
    "\nON affectation.lit_id = lit.lit_id" .
    "\nINNER JOIN chambre" .
    "\nON lit.chambre_id = chambre.chambre_id" .
    "\nINNER JOIN service" .
    "\nON chambre.service_id = service.service_id" .
    "\nAND service.service_id = '$id'" .
    "\nWHERE sejour.annule = '0'";
  if($prat_id) {
    $sql .= "\nAND sejour.praticien_id = '$prat_id'";
  }
  if($discipline_id) {
    $sql .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  }
  if($type_adm) {
    if($type_adm == 1) {
      $sql .= "\nAND (sejour.type = 'comp' OR sejour.type = 'ambu')";
    } else {
      $sql .= "\nAND sejour.type = '$type_adm'";
    }
  }
  $sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
  $result = db_loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $patbyservice[$id]["op"][] = $totaux["total"];
        $total += $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $patbyservice[$id]["op"][] = 0;
    }
  }
}

// Setup the graph.
$graph = new Graph(500,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Nombre de patients par service";
$subtitle = "- $total passages -";
if($prat_id) {
  $subtitle .= " Dr. $pratSel->_view -";
}
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}
if($type_adm) {
  $subtitle .= " ".$listHospis[$type_adm]." -";
}
$graph->title->Set($title);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,10);
$graph->title->SetColor("darkred");
$graph->subtitle->Set($subtitle);
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
foreach($patbyservice as $key => $value) {
  $bplot = new BarPlot($value["op"]);
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