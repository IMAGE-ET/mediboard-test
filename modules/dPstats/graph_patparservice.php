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

$sejour = new CSejour;
$listHospis = array();
$listHospis[1] = "Hospi complètes + ambu";
$listHospis = $listHospis + $sejour->_enumsTrans["type"];

$total = 0;

$ds = CSQLDataSource::get("std");

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$serviceSel = new CService;
$serviceSel->load($service_id);

$disciplineSel = new CDiscipline;
$disciplineSel->load($discipline_id);

for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
  $datax[] = mbTransformTime("+0 DAY", $i, "%m/%Y");
}


$where = array();
if($service_id) {
  $where["service_id"] = "= '$service_id'";
}
$services = $serviceSel->loadGroupList($where);

$patbyservice = array();
foreach($services as $service) {
  $id = $service->_id;
  $patbyservice[$id]["legend"] = wordwrap($service->nom, 10);
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
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {
        $patbyservice[$id]["data"][] = $totaux["total"];
        $total += $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $patbyservice[$id]["data"][] = 0;
    }
  }
}

$taille = array(480,300);
// Set up the title for the graph
$title = "Nombre de patients par service";
$subtitle = "- $total passages -";
if($prat_id) {
  $subtitle .= " Dr $pratSel->_view -";
}
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}
if($type_adm) {
  $subtitle .= " ".$listHospis[$type_adm]." -";
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
									"dataAccBar" => $patbyservice,
									"datax" => $datax,
									"graphAccLegend" => $patbyservice,);
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addAccBarPlot($options);
$graph->render("out",$options);
