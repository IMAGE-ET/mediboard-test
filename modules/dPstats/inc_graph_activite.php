<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Sébastien Fillonneau
*/

function graphActivite($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = 0, $codes_ccam = '') {
	if (!$debut) $debut = mbDate("-1 YEAR");
	if (!$fin) $fin = mbDate();
	
	$prat = new CMediusers;
	$prat->load($prat_id);
	
	$discipline = new CDiscipline;
	$discipline->load($discipline_id);
	
	$salle = new CSalle;
	$salle->load($salle_id);
	
	$ticks = array();
	for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
	  $ticks[] = array(count($ticks), mbTransformTime("+0 DAY", $i, "%m/%Y"));
	}
	
	$where = array();
	$where['stats'] = " = '1'";
	if($salle_id) {
	  $where['salle_id'] = " = '$salle_id'";
	} elseif($bloc_id) {
	  $where['bloc_id'] = "= '$bloc_id'";
	}
	
	$salles = $salle->loadList($where);
	$ds = CSQLDataSource::get("std");
	
	$total = 0;
	$series = array();
	foreach($salles as $salle) {
	  $serie = array(
		  'label' => utf8_encode($salle->_view),
		  'data' => array(),
			'total' => 0
		);
	  
	  $query = "SELECT COUNT(operations.operation_id) AS total,
	    DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
	    DATE_FORMAT(plagesop.date, '%Y%m') AS orderitem,
	    sallesbloc.nom AS nom
	    FROM operations
	    INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
	    INNER JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
	    INNER JOIN users_mediboard ON operations.chir_id = users_mediboard.user_id
	    WHERE 
			  plagesop.date BETWEEN '$debut' AND '$fin' AND 
				operations.annulee = '0'";
				
	  if($prat_id && !$prat->isFromType(array("Anesthésiste"))) {
	    $query .= "\nAND operations.chir_id = '$prat_id'";
	  }
	  if($prat_id && $prat->isFromType(array("Anesthésiste"))) {
	    $query .= "\nAND operations.anesth_id = '$prat_id'";
	  }
	  if($discipline_id) {
	    $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
	  }
	  if($codes_ccam) {
	    $query .= "\nAND operations.codes_ccam LIKE '%$codes_ccam%'";
	  }
	  $query .= "\nAND sallesbloc.salle_id = '$salle->_id'";
	  $query .= "\nGROUP BY mois ORDER BY orderitem";
		
	  $result = $ds->loadlist($query);
	  foreach($ticks as $i => $tick) {
	    $f = true;
	    foreach($result as $totaux) {
	      if($tick[1] == $totaux["mois"]) {        
	        $serie["data"][] = array($i, $totaux["total"]);
	        $serie["total"] += $totaux["total"];
	        $total += $totaux["total"];
	        $f = false;
	      }
	    }
	    if($f) $serie["data"][] = array(count($serie["data"]), 0);
	  }
	  //if($serie["total"]) 
		$series[] = $serie;
	}
	
	// Set up the title for the graph
	if($prat_id && $prat->isFromType(array("Anesthésiste"))) {
	  $title = "Nombre d'anesthésie par salle";
	  $subtitle = "$total anesthésies";
	} else {
	  $title = "Nombre d'interventions par salle";
	  $subtitle = "$total interventions";
	}
	if($prat_id)       $subtitle .= " - Dr $prat->_view";
	if($discipline_id) $subtitle .= " - $discipline->_view";
	if($codes_ccam)    $subtitle .= " - CCAM : $codes_ccam";

	$options = array(
		'title' => utf8_encode($title),
		'subtitle' => utf8_encode($subtitle),
		'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
		'yaxis' => array('autoscaleMargin' => 1),
		'bars' => array('show' => true, 'stacked' => true, 'barWidth' => 0.8),
		'HtmlText' => false,
		'legend' => array('show' => true, 'position' => 'nw'),
		'grid' => array('verticalLines' => false),
		'spreadsheet' => array(
		  'show' => true,
			'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Données'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('Seléctionner tout le tableau')
	  )
	);
	
	return array('series' => $series, 'options' => $options);
}

?>