<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

function graphPatJourSalle($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $codeCCAM = '') {
  if (!$debut) $debut = mbDate("-1 YEAR");
	if (!$fin) $fin = mbDate();
	
	$prat = new CMediusers;
	$prat->load($prat_id);
	
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
	$series = array();
	$serie = array('data' => array());

	$query = "SELECT COUNT(operations.operation_id) AS total,
	  DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
	  DATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem
	  FROM operations
	  INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
	  LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
	  WHERE 
		  sallesbloc.stats = '1' AND 
			plagesop.date BETWEEN '$debut' AND '$fin' AND 
			operations.annulee = '0'";
		
  if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";

  if($salle_id) {
    $query .= "\nAND sallesbloc.salle_id = '$salle_id'";
  } elseif($bloc_id) {
    $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
  }
	
  $query .= "\nGROUP BY mois ORDER BY orderitem";

		$result = $prat->_spec->ds->loadlist($query);
		foreach($ticks as $i => $tick) {
		  $f = true;
		  foreach($result as $r) {
		    if($tick[1] == $r["mois"]) {
		    	$nbjours = mbWorkDaysInMonth($r["orderitem"]);
		      $serie['data'][] = array($i, $r["total"]/($nbjours*count($salles)));
		      $f = false;
		    }
		  }
		  if($f) $serie["data"][] = array(count($serie["data"]), 0);
		}

	$series[] = $serie;
	
	// Set up the title for the graph
	$title = "Patients / jour / salle";
	$subtitle = "";
	if($prat_id)  $subtitle .= " - Dr $pratSel->_view";
	if($salle_id) $subtitle .= " - $salleSel->nom";
	if($codeCCAM) $subtitle .= " - CCAM : $codeCCAM";

	$options = array(
		'title' => utf8_encode($title),
		'subtitle' => utf8_encode($subtitle),
		'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
		'yaxis' => array('autoscaleMargin' => 1, 'min' => 0),
		'lines' => array('show' => true, 'filled' => true, 'fillColor' => '#999'),
		'points' => array('show' => true),
		'HtmlText' => false,
		'legend' => array('show' => true, 'position' => 'nw'),
		'mouse' => array('track' => true, 'relative' => true, 'position' => 'ne'),
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