<?php /* $Id: graph_activite.php,v 1.12 2006/03/08 14:48:40 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.12 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPbloc', 'salle') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_bar'));

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

$opbysalle = array();
foreach($salles as $salle) {
  $id = $salle["id"];
  $opbysalle[$id]["nom"] = $salle["nom"];
  $sql = "SELECT COUNT(operations.operation_id) AS total," .
    "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(plagesop.date, '%Y%m') AS orderitem," .
    "\nsallesbloc.nom AS nom" .
    "\nFROM plagesop, sallesbloc" .
    "\nINNER JOIN operations" .
    "\nON operations.plageop_id = plagesop.id" .
    "\nAND operations.annulee = 0" .
    "\nWHERE plagesop.date BETWEEN '$debut' AND '$fin'";
  if($prat_id)
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM)
    $sql .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  $sql .= "\nAND plagesop.id_salle = sallesbloc.id" .
    "\nAND sallesbloc.id = '$id'" .
    "\nGROUP BY mois" .
    "\nORDER BY orderitem";
  $result = db_loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $opbysalle[$id]["op"][] = $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $opbysalle[$id]["op"][] = 0;
    }
  }
}

// Setup the graph.
$graph = new Graph(500,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Nombre d'interventions";
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
  $bplot = new BarPlot($value["op"]);
  $from = $colors[$key];
  $to = "#EEEEEE";
  $bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
  $bplot->SetColor("white");
  $bplot->setLegend($value["nom"]);
  $bplot->value->SetFormat('%01.0f');
  $bplot->value->SetColor($colors[$key]);
  $bplot->value->SetFont(FF_ARIAL,FS_NORMAL, 8); 
  //$bplot->value->show();
  $listPlots[] = $bplot;
}

$gbplot = new AccBarPlot($listPlots);
$gbplot->SetWidth(0.6);
$gbplot->value->SetFormat('%01.0f'); 
$gbplot->value->show();

// Set color for the frame of each bar
$graph->Add($gbplot);

// Finally send the graph to the browser
$graph->Stroke();