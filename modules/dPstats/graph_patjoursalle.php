<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

CAppUI::requireSystemClass("mbGraph");

$debut      = mbGetValueFromGet("debut"     , mbDate("-1 YEAR"));
$fin        = mbGetValueFromGet("fin"       , mbDate()         );
$prat_id    = mbGetValueFromGet("prat_id"   , 0                );
$salle_id   = mbGetValueFromGet("salle_id"  , 0                );
$bloc_id    = mbGetValueFromGet("bloc_id"   , 0                );
$codes_ccam = mbGetValueFromGet("codes_ccam", ""               );

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$salleSel = new CSalle;
$salleSel->load($salle_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%m/%Y");
}

$ds = CSQLDataSource::get("std");

$salles = new CSalle();

$where = array();
$where['stats'] = " = '1'";
if($salle_id) {
  $where['salle_id'] = " = '$salle_id'";
} elseif($bloc_id) {
  $where['bloc_id'] = "= '$bloc_id'";
}

$salles = $salles->loadList($where);

$op = array();
$sql = "SELECT COUNT(operations.operation_id) AS total," .
  "\nDATE_FORMAT(plagesop.date, '%m/%Y') AS mois," .
  "\nDATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem" .
  "\nFROM operations" .
  "\nINNER JOIN sallesbloc" .
  "\nON operations.salle_id = sallesbloc.salle_id" .
  "\nLEFT JOIN plagesop" .
  "\nON operations.plageop_id = plagesop.plageop_id" .
  "\nWHERE sallesbloc.stats = '1'" .
  "\nAND plagesop.date BETWEEN '$debut' AND '$fin'" .
  "\nAND operations.annulee = '0'";
  if($prat_id) {
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  }
  if($codes_ccam) {
    $sql .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
  }
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
      $nbjours = mbWorkDaysInMonth($total["orderitem"]);
      $op[] = $total["total"]/($nbjours*count($salles));
      $f = false;
    }
  }
  if($f) {
    $op[] = 0;
  }
}

// Set up the title for the graph
$title = "Patients / jour / salle";
$subtitle = "";
if($prat_id) {
  $subtitle .= "- Dr $pratSel->_view ";
}
if($salle_id) {
  $subtitle .= "- $salleSel->nom ";
}
if($codes_ccam) {
  $subtitle .= "- CCAM : $codes_ccam ";
}
if($subtitle) {
  $subtitle .= "-";
}

$opSorted = $op;
rsort($opSorted);

$options = array( 
	"width" => 480,
	"height" => 300,
	"title" => $title,
	"margin" => array(50,40,50,70),
	"posLegend" => array(0.015,0.79, "right", "center"), 
	"sizeFontAxis" => 8,
	"labelAngle" => 50,
	"textTickInterval" => 2,
	"posXAbsDelta" => 15,
	"posYAbsDelta" => -15,
	"dataLine" => $op,
	"datax" => $datax,
	"scale" => array(0,intval($opSorted[0])+1),
);
								
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addSplinePlot($options);
$graph->render("out",$options);
