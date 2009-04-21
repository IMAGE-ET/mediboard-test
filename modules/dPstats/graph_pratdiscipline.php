<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

CAppUI::requireLibraryFile("jpgraph/src/mbjpgraph");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_bar");

$debut         = mbGetValueFromGet("debut"        , mbDate("-1 YEAR"));
$fin           = mbGetValueFromGet("fin"          , mbDate()         );
$salle_id      = mbGetValueFromGet("salle_id"     , 0                );
$bloc_id       = mbGetValueFromGet("bloc_id"      , 0                );
$discipline_id = mbGetValueFromGet("discipline_id", 0                );
$codeCCAM      = mbGetValueFromGet("codeCCAM"     , ""               );

$total = 0;

$salleSel = new CSalle;
$salleSel->load($salle_id);

$disciplineSel = new CDiscipline;
$disciplineSel->load($discipline_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%m/%Y");
}

$users= new CMediusers;
$where = array();
if($discipline_id)
  $where["discipline_id"] = " = '$discipline_id'";
$users = $users->loadList($where);

$opbyprat = array();
foreach($users as $user) {
  $id = $user->user_id;
  $opbyprat[$id]["nom"] = $user->_view;
  $sql = "SELECT COUNT(operations.operation_id) AS total," .
    "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(plagesop.date, '%Y%m') AS orderitem," .
    "\nusers_mediboard.user_id" .
    "\nFROM plagesop" .
    "\nINNER JOIN sallesbloc" .
    "\nON plagesop.salle_id = sallesbloc.salle_id" .
    "\nINNER JOIN operations" .
    "\nON operations.plageop_id = plagesop.plageop_id" .
    "\nAND operations.annulee = '0'" .
    "\nINNER JOIN users_mediboard" .
    "\nON plagesop.chir_id = users_mediboard.user_id" .
    "\nAND users_mediboard.user_id = '$id'" .
    "\nWHERE plagesop.date BETWEEN '$debut' AND '$fin'";
  if($discipline_id)
    $sql .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if($codeCCAM)
    $sql .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
  if($salle_id) {
    $sql .= "\nAND sallesbloc.salle_id = '$salle_id'";
  } elseif($bloc_id) {
    $sql .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
  $sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
  $ds = CSQLDataSource::get("std");
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $opbyprat[$id]["op"][] = $totaux["total"];
        $total += $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $opbyprat[$id]["op"][] = 0;
    }
  }
}

// Setup the graph.
$graph = new Graph(480,300,"auto");    
$graph->img->SetMargin(50,150,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Nombre d'interventions par praticien";
$subtitle = "- $total opérations -";
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}
if($codeCCAM) {
  $subtitle .= " CCAM : $codeCCAM -";
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
$graph->legend->Pos(0.02,0.5, "right", "center");

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
//mbTrace($opbyprat, "", true);
foreach($opbyprat as $key => $value) {
  $bplot = new BarPlot($value["op"]);
  $from = $colors[($key % 12)];
  $to = "#EEEEEE";
  $bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
  $bplot->SetColor("white");
  $bplot->setLegend(wordwrap($users[$key]->_view, 18));
  $bplot->value->SetFormat("%01.0f");
  $bplot->value->SetColor($colors[($key % 12)]);
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