<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

CAppUI::requireSystemClass("mbGraph");

$size          = mbGetValueFromGet("size" , 1);
$date          = mbGetValueFromGetOrSession("date", mbTransformTime("+0 DAY", mbDate(), "%m/%Y"));
$prat_id       = mbGetValueFromGetOrSession("prat_id", 0);
$salle_id      = mbGetValueFromGetOrSession("salle_id", 0);
$bloc_id       = mbGetValueFromGetOrSession("bloc_id");
$discipline_id = mbGetValueFromGetOrSession("discipline_id", 0);
$codes_ccam    = strtoupper(mbGetValueFromGetOrSession("codes_ccam", ""));

$total = 0;

$pratSel = new CMediusers;
$pratSel->load($prat_id);

$salleSel = new CSalle;
$salleSel->load($salle_id);

$disciplineSel = new CDiscipline;
$disciplineSel->load($discipline_id);

// Gestion de la date
$date = substr($date,3,7) ."-" .substr($date,0,2). "-01";
$startx   = "$date 00:00:00";
$endx = mbDateTime("+1 MONTH", "$date 00:00:00");
$endx = mbDateTime("-1 DAY", "$endx");
$step = "+1 DAY";
$date_format = "%d";

// Tableaux des jours
for($i = $startx; $i <= $endx; $i = mbDateTime($step, $i)) {
  $datax[] = mbTransformTime(null, $i, $date_format);
  $datax2[] = mbTransformTime(null, $i, "%a %d");
}

// Chargement des salles

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

$opbysalle = array();
foreach($salles as $salle) {
  $curr_salle_id = $salle->salle_id;
  $opbysalle[$curr_salle_id]["legend"] = $salle->nom;
  
  $sql = "SELECT COUNT(operations.operation_id) AS total," .
    "\nDATE_FORMAT(plagesop.date, '$date_format') AS jour," .
    "\nDATE_FORMAT(plagesop.date, '%d') AS orderitem," .
    "\nsallesbloc.nom AS nom" .
    "\nFROM operations" .
    "\nINNER JOIN sallesbloc" .
    "\nON operations.salle_id = sallesbloc.salle_id" .
    "\nINNER JOIN plagesop" .
    "\nON operations.plageop_id = plagesop.plageop_id" .
    "\nINNER JOIN users_mediboard" .
    "\nON operations.chir_id = users_mediboard.user_id" .
    "\nWHERE plagesop.date BETWEEN '$startx' AND '$endx'" .
    "\nAND operations.annulee = '0'";
  if($prat_id)
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  if($discipline_id)
    $sql .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  if($codes_ccam)
    $sql .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
  $sql .= "\nAND sallesbloc.salle_id = '$curr_salle_id'" .
    "\nGROUP BY jour" .
    "\nORDER BY orderitem";
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["jour"]) {        
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
$title = "Nombre d'interventions par salle - ".mbTransformTime(null, $startx, "%m/%Y");;
$subtitle = "- $total opérations -";
if($prat_id) {
  $subtitle .= " Dr $pratSel->_view -";
}
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}
if($codes_ccam) {
  $subtitle .= " CCAM : $codes_ccam -";
}

$options = array( "width" => 370,
									"height" => 180,
									"size" => $size,
									"title" => $title,
									"subtitle" => $subtitle,
									"sizeFontTitle" => 10,
									"margin" => array(20+$size*10,75+$size*10,30+$size*10,50+$size*10),
									"posLegend" => array(0.02, 0.02, "right", "top"), 
									"sizeFontAxis" => 7,
									"labelAngle" => 50,
									"textTickInterval" => 2,
									"posXAbsDelta" => 15,
									"posYAbsDelta" => -15,
									"dataAccBar" => $opbysalle,
									"datax" => $datax2,
									"graphAccLegend" => $opbysalle,);
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addAccBarPlot($options);
$graph->render("out",$options);

?>