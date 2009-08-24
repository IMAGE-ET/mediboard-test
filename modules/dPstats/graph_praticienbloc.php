<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPraticienBloc($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0) {
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
	}
	$salles = $salle->loadlist($where);

	$series = array();
  $total = 0;
	
  // First serie
	$serie = array(
	  'data' => array(),
		'label' => utf8_encode('Réservé')
	);
	$query = "SELECT SUM(TIME_TO_SEC(plagesop.fin) - TIME_TO_SEC(plagesop.debut)) AS total,
	  DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
	  DATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem
	  FROM plagesop
	  INNER JOIN sallesbloc ON plagesop.salle_id = sallesbloc.salle_id
	  WHERE 
		  sallesbloc.stats = '1' AND 
			plagesop.date BETWEEN '$debut' AND '$fin'";
		
  if($prat_id) $query .= "\nAND plagesop.chir_id = '$prat_id'";
	
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
	      $serie['data'][] = array($i, $r["total"]/(60*60));
        $total += $r["total"]/(60*60);
	      $f = false;
	    }
	  }
	  if($f) $serie["data"][] = array(count($serie["data"]), 0);
	}
	
	$series[] = $serie;
	
  // Second serie
	$serie = array(
	  'data' => array(),
		'label' => utf8_encode('Occupé')
	);
	
	$query = "SELECT SUM(TIME_TO_SEC(operations.sortie_salle) - TIME_TO_SEC(operations.entree_salle)) AS total,
	  DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
	  DATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem
	  FROM plagesop
	  INNER JOIN sallesbloc ON plagesop.salle_id = sallesbloc.salle_id
	  LEFT JOIN operations ON operations.plageop_id = plagesop.plageop_id
	  WHERE 
		  sallesbloc.stats = '1' AND 
			operations.annulee = '0' AND 
			plagesop.date BETWEEN '$debut' AND '$fin'";
	
  if($prat_id) $query .= "\nAND operations.chir_id = '$prat_id'";
	
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
	      $serie['data'][] = array($i, $r["total"]/(60*60));
        $total += $r["total"]/(60*60);
	      $f = false;
	    }
	  }
	  if($f) $serie["data"][] = array(count($serie["data"]), 0);
	}
	
	$series[] = $serie;

	// Set up the title for the graph
	$title = "Heures réservées / occupées par mois";
	$subtitle = "";
	if($prat_id)  $subtitle .= " - Dr $prat->_view";
	if($salle_id) $subtitle .= " - $salle->nom";

	$options = array(
		'title' => utf8_encode($title),
		'subtitle' => utf8_encode($subtitle),
		'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
		'yaxis' => array('autoscaleMargin' => 1, 'min' => 0),
		'lines' => array('show' => true),
		'points' => array('show' => true),
		'markers' => array('show' => true),
		'HtmlText' => false,
		'mouse' => array('track' => true, 'relative' => true, 'position' => 'ne'),
		'legend' => array('show' => true, 'position' => 'nw'),
		'grid' => array('verticalLines' => true),
		'spreadsheet' => array(
		  'show' => true,
      'csvFileSeparator' => ';',
      'decimalSeparator' => ',',
			'tabGraphLabel' => utf8_encode('Graphique'),
      'tabDataLabel' => utf8_encode('Données'),
      'toolbarDownload' => utf8_encode('Fichier CSV'),
      'toolbarSelectAll' => utf8_encode('Sélectionner tout le tableau')
	  )
	);
  if ($total == 0) $options['yaxis']['max'] = 1;
	
	return array('series' => $series, 'options' => $options);
}
