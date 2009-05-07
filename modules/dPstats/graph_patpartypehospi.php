<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function graphPatParTypeHospi($debut = null, $fin = null, $prat_id = 0, $service_id = 0, $type_adm = 0, $discipline_id = 0) {
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
	$listHospis = array();
	foreach($sejour->_specs["type"]->_locales as $key => $type){
	  if((($key == "comp" || $key == "ambu") && $type_adm == 1) || 
		   ($type_adm == $key) || 
			 ($type_adm == null)){
	    $listHospis[$key] = utf8_encode($type);
	  }
	}
	
	$total = 0;
	$series = array();
	foreach($listHospis as $key => $type) {
	  $serie = array(
		  'label' => $type,
			'data' => array()
		);
		
	  $query = "SELECT COUNT(sejour.sejour_id) AS total, sejour.type,
	    DATE_FORMAT(sejour.entree_prevue, '%m/%Y') AS mois,
	    DATE_FORMAT(sejour.entree_prevue, '%Y%m') AS orderitem
	    FROM sejour
	    INNER JOIN users_mediboard ON sejour.praticien_id = users_mediboard.user_id
	    WHERE 
			  sejour.entree_prevue BETWEEN '$debut 00:00:00' AND '$fin 23:59:59' AND 
				sejour.group_id = '".CGroups::loadCurrent()->_id."' AND 
				sejour.type = '$key' AND 
				sejour.annule = '0'";
				
	  if($prat_id)       $query .= "\nAND sejour.praticien_id = '$prat_id'";
	  if($discipline_id) $query .= "\nAND users_mediboard.discipline_id = '$discipline_id'";
		
	  $query .= "\nGROUP BY mois ORDER BY orderitem";
	
	  $result = $sejour->_spec->ds->loadlist($query);
	  foreach($ticks as $i => $tick) {
	    $f = true;
	    foreach($result as $r) {
	      if($tick[1] == $r["mois"]) {
	        $serie["data"][] = array($i, $r["total"]);
          $serie_total["data"][$i][1] += $r["total"];
	        $total += $r["total"];
	        $f = false;
	      }
	    }
	    if($f) $serie["data"][] = array(count($serie["data"]), 0);
	  }
		$series[] = $serie;
	}
  
  $series[] = $serie_total;
	
	$subtitle = "$total patients";
	if($prat_id)       $subtitle .= " - Dr $prat->_view";
	if($discipline_id) $subtitle .= " - $discipline->_view";
	
	$options = array(
		'title' => utf8_encode("Nombre d'admissions par type d'hospitalisation"),
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