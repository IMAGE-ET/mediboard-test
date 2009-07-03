<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPraticienDiscipline($debut = null, $fin = null, $prat_id = 0, $salle_id = 0, $bloc_id = 0, $discipline_id = 0, $codeCCAM = '') {
	if (!$debut) $debut = mbDate("-1 YEAR");
	if (!$fin) $fin = mbDate();
	
	$salle = new CSalle;
	$salle->load($salle_id);
	
	$discipline = new CDiscipline;
	$discipline->load($discipline_id);
	
	$ticks = array();
  $serie_total = array(
    'label' => 'Total',
    'data' => array(),
    'markers' => array('show' => true),
    'bars' => array('show' => false)
  );
	for($i = $debut; $i <= $fin; $i = mbDate("+1 MONTH", $i)) {
	  $ticks[] = array(count($ticks), mbTransformTime("+0 DAY", $i, "%m/%Y"));
    $serie_total['data'][] = array(count($serie_total['data']), 0);
	}
	
	$user = new CMediusers;
	$where = array();
	if($discipline_id)
	  $where["discipline_id"] = " = '$discipline_id'";
		
	$users = $user->loadList($where);
	
	$total = 0;
	$series = array();
	foreach($users as $user) {
		$serie = array(
			'data' => array(),
		  'label' => utf8_encode($user->_view)
	  );
			
	  $query = "SELECT COUNT(operations.operation_id) AS total,
	    DATE_FORMAT(plagesop.date, '%m/%Y') AS mois,
	    DATE_FORMAT(plagesop.date, '%Y%m') AS orderitem,
	    users_mediboard.user_id
	    FROM plagesop
	    INNER JOIN sallesbloc ON plagesop.salle_id = sallesbloc.salle_id
	    INNER JOIN operations ON operations.plageop_id = plagesop.plageop_id
	    INNER JOIN users_mediboard ON plagesop.chir_id = users_mediboard.user_id
	    WHERE 
			  plagesop.date BETWEEN '$debut' AND '$fin' AND 
			  operations.annulee = '0' AND 
				users_mediboard.user_id = '$user->_id'";
				
	  if($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
	  if($codeCCAM)      $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
		
	  if($salle_id) {
	    $query .= "\nAND sallesbloc.salle_id = '$salle_id'";
	  } elseif($bloc_id) {
	    $query .= "\nAND sallesbloc.bloc_id = '$bloc_id'";
	  }
		
	  $query .= "\nGROUP BY mois ORDER BY orderitem";
	
		$result = $user->_spec->ds->loadlist($query);
		foreach($ticks as $i => $tick) {
		  $f = true;
		  foreach($result as $r) {
		    if($tick[1] == $r["mois"]) {
		      $serie['data'][] = array($i, $r["total"]);
          $serie_total["data"][$i][1] += $r["total"];
		      $f = false;
		    }
		  }
		  if($f) $serie["data"][] = array(count($serie["data"]), 0);
		}
		
		$series[] = $serie;
	}
  
  $series[] = $serie_total;

	$title = "Nombre d'interventions par praticien";
	$subtitle = "$total opérations";
	if($discipline_id) $subtitle .= " - $discipline->_view";
	if($codeCCAM)      $subtitle .= " - CCAM : $codeCCAM";
	
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
      'toolbarSelectAll' => utf8_encode('Sélectionner tout le tableau')
	  )
	);
	
	return array('series' => $series, 'options' => $options);
}