<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

CAppUI::requireSystemClass("mbGraph");

$debut         = mbGetValueFromGet("debut"        , mbDate("-1 YEAR"));
$fin           = mbGetValueFromGet("fin"          , mbDate()         );
$prat_id       = mbGetValueFromGet("prat_id"      , 0                );
$service_id    = mbGetValueFromGet("service_id"   , 0                );
$type_adm      = mbGetValueFromGet("type_adm"     , 0                );
$discipline_id = mbGetValueFromGet("discipline_id", 0                );

$total = 0;

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$disciplineSel = new CDiscipline;
$disciplineSel->load($discipline_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%m/%Y");
}

$sejour = new CSejour;
$listHospis = array();

foreach($sejour->_enumsTrans["type"] as $keyType=>$vType){
  $testAmbuOrComp = (($keyType=="comp" || $keyType=="ambu") && $type_adm == "1");
  $testCourant    = ($type_adm == $keyType);
  $testTous       = ($type_adm == null);
  if( $testTous || $testCourant || $testAmbuOrComp){
    $listHospis[$keyType] = wordwrap($vType, 10);
  }
}

$patbyhospi = array();
$i = 0;
foreach($listHospis as $type=>$vType) {
  $patbyhospi[$i]["legend"] = $vType;
  $sql = "SELECT COUNT(sejour.sejour_id) AS total," .
    "\nsejour.type," .
    "\nDATE_FORMAT(sejour.entree_prevue, '%m/%Y') AS mois," .
    "\nDATE_FORMAT(sejour.entree_prevue, '%Y%m') AS orderitem" .
    "\nFROM sejour" .
    "\nINNER JOIN users_mediboard" .
    "\nON sejour.praticien_id = users_mediboard.user_id" .
    "\nWHERE sejour.entree_prevue BETWEEN '$debut 00:00:00' AND '$fin 23:59:59'" .
    "\nAND sejour.group_id = '".CGroups::loadCurrent()->_id."'" .
    "\nAND sejour.type = '$type'" .
    "\nAND sejour.annule = '0'";
  if($prat_id)
    $sql .= "\nAND sejour.praticien_id = '$prat_id'";
  if($discipline_id)
    $sql .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  $sql .= "\nGROUP BY mois" .
    "\nORDER BY orderitem";
  $ds = CSQLDataSource::get("std");
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $patbyhospi[$i]["data"][] = $totaux["total"];
        $total += $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $patbyhospi[$i]["data"][] = 0;
    }
  }
  $i++;
}

// Set up the title for the graph
$title = "Nombre d'admissions par type d'hospitalisation";
$subtitle = "- $total patients -";
if($prat_id) {
  $subtitle .= " Dr $pratSel->_view -";
}
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}

$options = array( "width" => 480,
									"height" => 300,
									"title" => $title,
									"subtitle" => $subtitle,
									"sizeFontTitle" => 10,
									"margin" => array(50,100,50,70),
									"posLegend" => array(0.02, 0.06, "right", "top"), 
									"sizeFontAxis" => 8,
									"labelAngle" => 50,
									"textTickInterval" => 2,
									"posXAbsDelta" => 15,
									"posYAbsDelta" => -15,
									"dataAccBar" => $patbyhospi,
									"datax" => $datax,
									"graphAccLegend" => $patbyhospi,);
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addAccBarPlot($options);
$graph->render("out",$options);