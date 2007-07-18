<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

require_once($AppUI->getSystemClass("mbGraph"));

$total = 0;

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$salleSel = new CSalle;
$salleSel->load($salle_id);

$disciplineSel = new CDiscipline;
$disciplineSel->load($discipline_id);

$labels = array();
$jscalls = array();

for($i = $debutact; $i <= $finact; $i = mbDate("+1 MONTH", $i)) {
  $nameMonth = mbTranformTime("+0 DAY", $i, "%m/%Y");
  $datax[] = $nameMonth; 
  $labels[] = "Voir le détails par jour pour le $nameMonth";
  $jscalls[] = "javascript:zoomGraphIntervention('$nameMonth');";
}

$sql = "SELECT * FROM sallesbloc WHERE stats = '1'";
if($salle_id)
  $sql .= "\nAND salle_id = '$salle_id'";

$ds = CSQLDataSource::get("std");
$salles = $ds->loadlist($sql);

$opbysalle = array();
$i=0;
foreach($salles as $salle) {
  $curr_salle_id = $salle["salle_id"];
  $opbysalle[$curr_salle_id]["legend"] = $salle["nom"];
  
  $sql = "SELECT COUNT(operations.operation_id) AS total," .
    "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(plagesop.date, '%Y%m') AS orderitem," .
    "\nsallesbloc.nom AS nom" .
    "\nFROM plagesop" .
    "\nINNER JOIN sallesbloc" .
    "\nON plagesop.salle_id = sallesbloc.salle_id" .
    "\nINNER JOIN operations" .
    "\nON operations.plageop_id = plagesop.plageop_id" .
    "\nAND operations.annulee = '0'" .
    "\nINNER JOIN users_mediboard" .
    "\nON operations.chir_id = users_mediboard.user_id" .
    "\nWHERE plagesop.date BETWEEN '$debutact' AND '$finact'";
  if($prat_id)
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  if($discipline_id)
    $sql .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if($codes_ccam)
    $sql .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
  $sql .= "\nAND sallesbloc.salle_id = '$curr_salle_id'" .
    "\nGROUP BY mois" .
    "\nORDER BY orderitem";
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {        
        $opbysalle[$curr_salle_id]["data"][] = $totaux["total"];
        $total += $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $opbysalle[$curr_salle_id]["data"][] = 0;
    }
  }
}

// Set up the title for the graph
$title = "Nombre d'interventions par salle";
$subtitle = "- $total opérations -";
if($prat_id) {
  $subtitle .= " Dr. $pratSel->_view -";
}
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}
if($codes_ccam) {
  $subtitle .= " CCAM : $codes_ccam -";
}

$options = array( "width" => 480,
									"height" => 300,
									"title" => $title,
									"subtitle" => $subtitle,
									"sizeFontTitle" => 10,
									"margin" => array(50,40,50,70),
									"posLegend" => array(0.02, 0.06, "right", "top"), 
									"sizeFontAxis" => 8,
									"labelAngle" => 50,
									"textTickInterval" => 2,
									"posXAbsDelta" => 15,
									"posYAbsDelta" => -15,
									"dataAccBar" => $opbysalle,
									"datax" => $datax,
									"graphAccLegend" => $opbysalle,
									"map" => "oui",
									"mapInfo" => array($jscalls, $labels),
									"nameHtmlImageMap" => "graph_interventions" );
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addAccBarPlot($options);

?>