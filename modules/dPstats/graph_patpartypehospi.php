<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPhospi', 'service') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph'));
require_once( $AppUI->getLibraryClass('jpgraph/src/jpgraph_bar'));

$debut      = mbGetValueFromGet("debut"     , mbDate("-1 YEAR"));
$fin        = mbGetValueFromGet("fin"       , mbDate());
$prat_id    = mbGetValueFromGet("prat_id"   , 0);
$service_id = mbGetValueFromGet("service_id", 0);
$type_adm   = mbGetValueFromGet("type_adm"  , 0);

$pratSel = new CMediusers;
$pratSel->load($prat_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTranformTime("+0 DAY", $i, "%m/%Y");
}

$listHospis = array();
if($type_adm == "comp" || $type_adm == "0" || $type_adm == "1") {
  $listHospis[0]["code"] = "comp";
  $listHospis[0]["view"] = "Hospi complètes";
}
if($type_adm == "ambu" || $type_adm == "0" || $type_adm == "1") {
  $listHospis[1]["code"] = "ambu";
  $listHospis[1]["view"] = "Ambulatoires";
}
if($type_adm == "exte" || $type_adm == "0") {
  $listHospis[2]["code"] = "exte";
  $listHospis[2]["view"] = "Externes";
}

$patbyhospi = array();
foreach($listHospis as $hospi) {
  $type = $hospi["code"];
  $patbyhospi[$type]["nom"] = $hospi["view"];
  $sql = "SELECT COUNT(sejour.sejour_id) AS total," .
    "\nsejour.type," .
    "\nDATE_FORMAT(sejour.entree_prevue, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(sejour.entree_prevue, '%Y%m') AS orderitem" .
    "\nFROM sejour" .
    "\nWHERE sejour.entree_prevue BETWEEN '$debut 00:00:00' AND '$fin 23:59:59'" .
    "\nAND sejour.type = '$type'" .
    "\nAND sejour.annule = 0";
  if($prat_id)
    $sql .= "\nAND sejour.praticien_id = '$prat_id'";
  $sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
  $result = db_loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $patbyhospi[$type]["sejour"][] = $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $patbyhospi[$type]["sejour"][] = 0;
    }
  }
}

// Setup the graph.
$graph = new Graph(500,300,"auto");    
$graph->img->SetMargin(50,40,50,70);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");

// Set up the title for the graph
$title = "Nombre d'admissions par type d'hospitalisation";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr. $pratSel->_view ";
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
$colors = array('comp' => "#aa5500",
                'ambu' => "#55aa00",
                'exte' => "#0055aa");
$listPlots = array();
foreach($patbyhospi as $key => $value) {
  $bplot = new BarPlot($value["sejour"]);
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