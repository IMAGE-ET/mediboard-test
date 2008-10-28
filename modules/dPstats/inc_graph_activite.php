<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $g, $prat_id, $salle_id, $discipline_id, $debutact, $finact, $codes_ccam, $graph;

CAppUI::requireSystemClass("mbGraph");

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
  $nameMonth = mbTransformTime("+0 DAY", $i, "%m/%Y");
  $datax[] = $nameMonth; 
  $labels[] = "Voir le détails par jour pour le $nameMonth";
  $jscalls[] = "javascript:zoomGraphIntervention('$nameMonth');";
}

$where = array();
$where['stats'] = " = '1'";
if($salle_id) {
  $where['salle_id'] = " = '$salle_id'";
}

$salles = new CSalle();
$ds = $salles->_spec->ds;

$salles = $salles->loadGroupList($where, false);

$opbysalle = array();
$i=0;
foreach($salles as $salle) {
  $curr_salle_id = $salle->salle_id;
  $opbysalle[$curr_salle_id]["legend"] = $salle->_view;
  $opbysalle[$curr_salle_id]["total"] = 0;
  
  $sql = "SELECT COUNT(operations.operation_id) AS total,
    DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
    DATE_FORMAT(plagesop.date, '%Y%m') AS orderitem,
    sallesbloc.nom AS nom
    FROM operations
    INNER JOIN sallesbloc
    ON operations.salle_id = sallesbloc.salle_id
    INNER JOIN plagesop
    ON operations.plageop_id = plagesop.plageop_id
    INNER JOIN users_mediboard
    ON operations.chir_id = users_mediboard.user_id
    WHERE plagesop.date BETWEEN '$debutact' AND '$finact'
    AND operations.annulee = '0'";
  if($prat_id && !$pratSel->isFromType(array("Anesthésiste"))) {
    $sql .= "\nAND operations.chir_id = '$prat_id'";
  }
  if($prat_id && $pratSel->isFromType(array("Anesthésiste"))) {
    $sql .= "\nAND operations.anesth_id = '$prat_id'";
  }
  if($discipline_id) {
    $sql .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
  }
  if($codes_ccam) {
    $sql .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
  }
  $sql .= "\nAND sallesbloc.salle_id = '$curr_salle_id'
    GROUP BY mois
    ORDER BY orderitem";
  $result = $ds->loadlist($sql);
  foreach($datax as $x) {
    $f = true;
    foreach($result as $totaux) {
      if($x == $totaux["mois"]) {        
        $opbysalle[$curr_salle_id]["data"][] = $totaux["total"];
        $opbysalle[$curr_salle_id]["total"] += $totaux["total"];
        $total += $totaux["total"];
        $f = false;
      }
    }
    if($f) {
      $opbysalle[$curr_salle_id]["data"][] = 0;
    }
  }
  if(!$opbysalle[$curr_salle_id]["total"] && count($opbysalle) > 1) {
    unset($opbysalle[$curr_salle_id]);
  }
}

// Set up the title for the graph
if($prat_id && $pratSel->isFromType(array("Anesthésiste"))) {
  $title = "Nombre d'anesthésie par salle";
  $subtitle = "- $total anesthésies -";
} else {
  $title = "Nombre d'interventions par salle";
  $subtitle = "- $total interventions -";
}
if($prat_id) {
  $subtitle .= " Dr $pratSel->_view -";
}
if($discipline_id) {
  $subtitle .= " $disciplineSel->_view -";
}
if($codes_ccam) {
  $subtitle .= " CCAM : $codes_ccam -";
}

global $graph, $options;

$options = array( 
	"width" => 700,
	"height" => 300,
	"title" => $title,
	"subtitle" => $subtitle,
	"sizeFontTitle" => 10,
	"margin" => array(50,160,50,70),
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
	"nameHtmlImageMap" => "graph_interventions" 
);
				
$graph = new CMbGraph();
$graph->selectType("Graph",$options);
$graph->selectPalette($options);
$graph->setupAxis($options);
$graph->addAccBarPlot($options);

?>