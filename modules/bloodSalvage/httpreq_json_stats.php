<?php /* $Id: vw_stats.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global  $can;
$can->needsRead();

$possible_filters = array('chir_id', 'anesth_id', 'codes_ccam', 'code_asa');

$filters          = CValue::getOrSession('filters', array());
$months_count     = CValue::getOrSession('months_count', 12);
$months_relative  = CValue::getOrSession('months_relative', 0);
$comparison       = CValue::getOrSession('comparison', $possible_filters);
$comparison_left  = CValue::getOrSession('comparison_left');
$comparison_right = CValue::getOrSession('comparison_right');
$mode             = CValue::get('mode');

foreach ($possible_filters as $n) {
  if (!isset($filters[$n])) $filters[$n] = null;
}

// Dates
$dates = array();
for ($i = $months_count - 1; $i >= 0; --$i) {
  $mr = $months_relative+$i;
  $sample_end = mbTransformTime("-$mr MONTHS", mbDate(), "%Y-%m-31 23:59:59");
  $sample_start = mbTransformTime("-$mr MONTHS", mbDate(), "%Y-%m-01 00:00:00");
  $dates[$sample_start] = array(
    'start' => $sample_start,
    'end' => $sample_end,
  );
}

$ljoin = array(
  'operations' => 'blood_salvage.operation_id = operations.operation_id',
  'consultation_anesth' => 'operations.operation_id = consultation_anesth.operation_id',
  /*'consultation' => 'consultation_anesth.operation_id = consultation.consultation_id',
  'plageconsult' => 'consultation.plageconsult_id = plageconsult.plageconsult_id',*/
  'plagesop' => 'operations.plageop_id = plagesop.plageop_id',
  'sejour' => 'operations.sejour_id = sejour.sejour_id',
  'patients' => 'sejour.patient_id = patients.patient_id',
);

$where = array();
if ($filters['anesth_id']) {
  $where['operations.anesth_id'] = " = '{$filters['anesth_id']}'";
}

if ($filters['chir_id']) {
  $where['plagesop.chir_id'] = " = '{$filters['chir_id']}'";
}

if ($filters['code_asa']) {
  $where['consultation_anesth.ASA'] = " = '{$filters['code_asa']}'";
}

$bs = new CBloodSalvage();
$data = array();

// Par tranche d'age
$data['age'] = array(
  'options' => array(
    'title' => utf8_encode('Par tranche d\'âge')
  ),
  'series' => array()
);
$series = &$data['age']['series'];
$age_areas = array(0, 20, 40, 50, 60, 70, 80);
foreach($age_areas as $key => $age) {
	$limits = array($age, (isset($age_areas[$key+1]) ? $age_areas[$key+1] : null));
	$label = $limits[1] ? ("$limits[0] - ".($limits[1]-1)) : ">= $limits[0]";
	
	// Age calculation
  $where[] = "DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(patients.naissance)), '%Y') > ".$limits[0].
	           ($limits[1] != null ? (" AND DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(patients.naissance)), '%Y') <= ".$limits[1]) : '');
	$pos = end(array_keys($where));
	
	$series[$key] = array('data' => null, 'label' => "$label ans");
	$d = &$series[$key]['data'];
	
	$i = 0;
	foreach ($dates as $month => $date) {
	  $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";

		$count = $bs->countList($where, null, null, null, $ljoin);
		$d[$i] = array($i, intval($count));
	  $i++;
	}
	unset($where[$pos]);
}


// > 6h ou pas
$data['6h'] = array(
  'options' => array(
    'title' => utf8_encode('Durée règlementaire')
  ),
  'series' => array()
);
$series = &$data['6h']['series'];
$areas = array("< 6", ">= 6", "IS NULL");
foreach($areas as $key => $area) {
	$where[] = "HOUR(TIMEDIFF(blood_salvage.transfusion_end, blood_salvage.recuperation_start)) $area";
	$pos = end(array_keys($where));
	
	$series[$key] = array('data' => null, 'label' => (($area == 'IS NULL') ? 'Inconnu' : $area.'h'));
	$d = &$series[$key]['data'];
	
	$i = 0;
	foreach ($dates as $month => $date) {
	  $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
	  $count = $bs->countList($where, null, null, null, $ljoin);
	  $d[$i] = array($i, intval($count));
	  $i++;
	}
	unset($where[$pos]);
}

// H/F
$data['sexe'] = array(
  'options' => array(
    'title' => utf8_encode('Par sexe')
  ),
  'series' => array()
);
$series = &$data['sexe']['series'];
$areas = array("= 'm'", "= 'f'");
$areas_labels = array("= 'm'" => "Homme", "= 'f'" => "Femme");
foreach($areas as $key => $area) {
  $where[] = "patients.sexe $area";
  $pos = end(array_keys($where));
  
  $series[$key] = array('data' => null, 'label' => $areas_labels[$area]);
  $d = &$series[$key]['data'];
  
  $i = 0;
  foreach ($dates as $month => $date) {
    $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
    $count = $bs->countList($where, null, null, null, $ljoin);
    $d[$i] = array($i, intval($count));
    $i++;
  }
  unset($where[$pos]);
}

// Codes CCAM
if ($filters['codes_ccam']) {
  $list_codes_ccam = explode('|', $filters['codes_ccam']);
	
  $data['ccam'] = array(
    'options' => array(
      'title' => utf8_encode('Par code CCAM')
    ),
    'data' => array()
  );
	$series = &$data['ccam']['series'];
	foreach($list_codes_ccam as $key => $ccam) {
	  $where[] = "operations.codes_ccam LIKE '%$ccam%'";
	  $pos = end(array_keys($where));
	  
	  $series[$key] = array('data' => null, 'label' => $ccam);
	  $d = &$series[$key]['data'];
	  
	  $i = 0;
	  foreach ($dates as $month => $date) {
	    $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
	    $count = $bs->countList($where, null, null, null, $ljoin);
	    $d[$i] = array($i, intval($count));
	    $i++;
	  }
	  unset($where[$pos]);
	}
}

// Ticks
$i = 0;
$ticks = array();
foreach ($dates as $month => $date) {
  $ticks[$i] = array($i, mbTransformTime(null, $month, '%m/%y'));
  $i++;
}

foreach($data as &$_data) {
  $_data["options"] = CFlotrGraph::merge("bars", $_data["options"]);
  $_data["options"] = CFlotrGraph::merge($_data["options"], array(
    'xaxis' => array('ticks' => $ticks, 'labelsAngle' => 45),
    'bars' => array('stacked' => true),
  ));
}

CApp::json($data, "text/plain");
