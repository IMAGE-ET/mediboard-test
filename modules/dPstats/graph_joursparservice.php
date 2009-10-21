<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphJoursParService($debut = null, $fin = null, $prat_id = 0, $service_id = 0, $type_adm = 0, $discipline_id = 0, $type_data = "prevue") {
	if (!$debut) $debut = mbDate("-1 YEAR");
	if (!$fin) $fin = mbDate();
	
	$prat = new CMediusers;
	$prat->load($prat_id);
  
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
	
	$where = array();
	if($service_id) {
	  $where["service_id"] = "= '$service_id'";
	}
	$service = new CService;
	$services = $service->loadGroupList($where);
	
	$sejour = new CSejour;
	$listHospis = array(
	  1 => "Hospi complètes + ambu"
	) + $sejour->_specs["type"]->_locales;
	
	$total = 0;
	$series = array();
	foreach($services as $service) {
		$serie = array(
		  'data' => array(),
			'label' => utf8_encode($service->nom)
		);
		
		$curr_month = $debut;
		$result = array();
		while($curr_month <= $fin) {
		  $end_month = mbDate("+1 MONTH", $curr_month);
		  $end_month = mbDate("-1 DAY", $end_month);
		  
		  $query = "SELECT
                  SUM(DATEDIFF(LEAST(sejour.sortie_$type_data, '$end_month 23:59:59'), GREATEST(sejour.entree_$type_data, '$curr_month 00:00:00'))) AS total, 
                  DATE_FORMAT('$curr_month', '%m/%Y') AS mois,
                  DATE_FORMAT('$curr_month', '%Y%m') AS orderitem
                FROM sejour
	              INNER JOIN users_mediboard ON sejour.praticien_id = users_mediboard.user_id
	              LEFT JOIN affectation ON sejour.sejour_id = affectation.sejour_id
	              LEFT JOIN lit ON affectation.lit_id = lit.lit_id
	              LEFT JOIN chambre ON lit.chambre_id = chambre.chambre_id
	              LEFT JOIN service ON chambre.service_id = service.service_id
	              WHERE sejour.annule = '0'
                  AND (sejour.entree_$type_data BETWEEN '$curr_month 00:00:00' AND '$end_month 23:59:59' OR sejour.sortie_$type_data BETWEEN '$curr_month 00:00:00' AND '$end_month 23:59:59')
		              AND service.service_id = '$service->_id'";
				
	    if($prat_id)       $query .= "\nAND sejour.praticien_id = '$prat_id'";
	    if($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
		
      if($type_adm) {
	    if($type_adm == 1)
	      $query .= "\nAND (sejour.type = 'comp' OR sejour.type = 'ambu')";
	    else
	      $query .= "\nAND sejour.type = '$type_adm'";
	    }
	    $query .= "\nGROUP BY mois ORDER BY orderitem";
	    
	    $result_month = $sejour->_spec->ds->loadlist($query);
	    
	    foreach($result_month as $curr_result) {
	      $key = $curr_result["orderitem"].$service->_id;
	      if(!isset($result[$key])) {
	        $result[$key] = $curr_result;
	      } else {
	        $result[$key]["total"] += $curr_result["total"];
	      }
	    }

		  $curr_month = mbDate("+1 MONTH", $curr_month);
		}
	  foreach($ticks as $i => $tick) {
	    $f = true;
	    foreach($result as $r) {
	      if($tick[1] == $r["mois"]) {
	        $serie["data"][] = array($i, $r["total"]);
          $serie_total["data"][$i][1] += $r["total"];
	        $total += $r["total"];
	        $f = false;
          break;
	      }
	    }
	    if($f) $serie["data"][] = array(count($serie["data"]), 0);
	  }
		$series[] = $serie;
  }
  
  $series[] = $serie_total;
  
  $subtitle = "$total jours";
	if($prat_id)       $subtitle .= " - Dr $prat->_view";
	if($discipline_id) $subtitle .= " - $discipline->_view";
	if($type_adm)      $subtitle .= " - ".$listHospis[$type_adm];
	
	$options = array(
		'title' => utf8_encode("Nombre de journées par service - $type_data"),
		'subtitle' => utf8_encode($subtitle),
		'xaxis' => array('labelsAngle' => 45, 'ticks' => $ticks),
		'yaxis' => array('autoscaleMargin' => 1),
		'bars' => array('show' => true, 'stacked' => true, 'barWidth' => 0.8),
		'HtmlText' => false,
		'legend' => array('show' => true, 'position' => 'nw'),
		'grid' => array('verticalLines' => false),
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
	
	return array('series' => $series, 'options' => $options);
}
