<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

CAppUI::requireLibraryFile("jpgraph/src/mbjpgraph");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_line");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_regstat");

$debut    = mbGetValueFromGet("debut"   , mbDate("-1 YEAR"));
$fin      = mbGetValueFromGet("fin"     , mbDate()         );
$prat_id  = mbGetValueFromGet("prat_id" , 0                );
$salle_id = mbGetValueFromGet("salle_id", 0                );
$bloc_id  = mbGetValueFromGet("bloc_id" , 0                );
$codeCCAM = mbGetValueFromGet("codeCCAM", ""               );

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$salleSel = new CSalle;
$salleSel->load($salle_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%m/%Y");
}

$sql = "SELECT * FROM sallesbloc WHERE stats = '1'";
if($salle_id)
  $sql .= "\nAND salle_id = '$salle_id'";
$ds = CSQLDataSource::get("std");
$salles = $ds->loadlist($sql);

$nbHours = array();
$sql = "SELECT SUM(TIME_TO_SEC(plagesop.fin) - TIME_TO_SEC(plagesop.debut)) AS total," .
  "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
  "\nDATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem" .
  "\nFROM plagesop" .
  "\nINNER JOIN sallesbloc" .
  "\nON plagesop.salle_id = sallesbloc.salle_id" .
  "\nWHERE sallesbloc.stats = '1'" .
  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'";
  if($prat_id)
    $sql .= "\nAND plagesop.chir_id = '$prat_id'";
  if($salle_id) {
    $sql .= "\nAND sallesbloc.salle_id = '$salle_id'";
  } elseif($bloc_id) {
    $sql .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
$sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
$result = $ds->loadlist($sql);
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
$sql = "SELECT SUM(TIME_TO_SEC(operations.sortie_salle) - TIME_TO_SEC(operations.entree_salle)) AS total," .
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
  if($salle_id) {
    $sql .= "\nAND sallesbloc.salle_id = '$salle_id'";
  } elseif($bloc_id) {
    $sql .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
$sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
$result = $ds->loadlist($sql);
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

// Set up the title for the graph
$title = "Heures réservées / occupées par mois";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr $pratSel->_view ";
}
if($salle_id) {
  $subtitle .= "- $salleSel->nom ";
}
if($subtitle) {
  $subtitle .= "-";
}

$hours1Sorted = $nbHours;
rsort($hours1Sorted);
$hours2Sorted = $doneHours;
rsort($hours2Sorted);
$scale = max(intval($hours1Sorted[0]), intval($hours2Sorted[0]));

$options = array( "width" => 480,
									"height" => 300,				
									"title" => $title,
									"subtitle" => $subtitle,
									"margin" => array(50,40,50,70),
									"posLegend" => array(0.02,0.02, "right", "top"), 
									"sizeFontAxis" => 8,
									"labelAngle" => 50,
									"textTickInterval" => 1,
									"posXAbsDelta" => 15,
									"posYAbsDelta" => -15,
									"datax" => $datax,
									"dataLine" => array($nbHours, $doneHours),
									"graphSplineLegend" => array("Réservé","Occupé"),
									"scale" => array(0, $scale + $scale/10),
								);
		
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addDataLinePlot($options);
$graph->addSplinePlot($options);
$graph->render("out",$options);
