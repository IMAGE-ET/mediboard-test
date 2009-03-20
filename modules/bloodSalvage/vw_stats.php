<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global  $can;
$can->needsRead();

$filters         = mbGetValueFromGetOrSession('filters', array());
$months_count    = mbGetValueFromGetOrSession('months_count', 12);
$months_relative = mbGetValueFromGetOrSession('months_relative', 0);

$possible_filters = array('chir_id', 'anesth_id', 'codes_ccam', 'code_asa');
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

$where = array();
$ljoin = array();
$ljoin['operations'] = 'blood_salvage.operation_id = operations.operation_id';
$ljoin['consultation_anesth'] = 'operations.operation_id = consultation_anesth.operation_id';
/*$ljoin['consultation'] = 'consultation_anesth.operation_id = consultation.consultation_id';
$ljoin['plageconsult'] = 'consultation.plageconsult_id = plageconsult.plageconsult_id';*/
$ljoin['plagesop'] = 'operations.plageop_id = plagesop.plageop_id';
$ljoin['sejour'] = 'operations.sejour_id = sejour.sejour_id';
$ljoin['patients'] = 'sejour.patient_id = patients.patient_id';

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
$graphs = array();
$titles = array();

// Par tranche d'age
$graphs['age'] = array();
$titles['age'] = utf8_encode('Par tranche d\'âge');
$series = &$graphs['age'];
$age_areas = array(0, 20, 40, 50, 60, 70, 80);
foreach($age_areas as $key => $age) {
	$limits = array($age, (isset($age_areas[$key+1]) ? $age_areas[$key+1] : null));
	$label = $limits[1] ? ("$limits[0] - ".($limits[1]-1)) : ">= $limits[0]";
	
	// Age calculation
  $where[] = "DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(patients.naissance)), '%Y') > ".$limits[0].
	           ($limits[1] != null ? (" AND DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(patients.naissance)), '%Y') <= ".$limits[1]) : '');
	$pos = end(array_keys($where));
	
	$series[$key] = array('data' => null, 'label' => "$label ans");
	$data = &$series[$key]['data'];
	
	$i = 0;
	foreach ($dates as $month => $date) {
	  $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";

		$count = $bs->countList($where, null, null, null, $ljoin);
		$data[$i] = array($i, $count);
	  $i++;
	}
	unset($where[$pos]);
}


// > 6h ou pas
$graphs['6h'] = array();
$titles['6h'] = utf8_encode('Durée règlementaire');
$series = &$graphs['6h'];
$areas = array("< 6", ">= 6", "IS NULL");
foreach($areas as $key => $area) {
	$where[] = "TIME_FORMAT(blood_salvage.transfusion_end - blood_salvage.recuperation_start, '%k') $area";
	$pos = end(array_keys($where));
	
	$series[$key] = array('data' => null, 'label' => (($area == 'IS NULL') ? 'Inconnu' : $area.'h'));
	$data = &$series[$key]['data'];
	
	$i = 0;
	foreach ($dates as $month => $date) {
	  $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
	  $count = $bs->countList($where, null, null, null, $ljoin);
	  $data[$i] = array($i, $count);
	  $i++;
	}
	unset($where[$pos]);
}

// H/F
$graphs['sexe'] = array();
$titles['sexe'] = utf8_encode('Par sexe');
$series = &$graphs['sexe'];
$areas = array("= 'm'", "= 'f'");
$areas_labels = array("= 'm'" => "Homme", "= 'f'" => "Femme");
foreach($areas as $key => $area) {
  $where[] = "patients.sexe $area";
  $pos = end(array_keys($where));
  
  $series[$key] = array('data' => null, 'label' => $areas_labels[$area]);
  $data = &$series[$key]['data'];
  
  $i = 0;
  foreach ($dates as $month => $date) {
    $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
    $count = $bs->countList($where, null, null, null, $ljoin);
    $data[$i] = array($i, $count);
    $i++;
  }
  unset($where[$pos]);
}

// Codes CCAM
if ($filters['codes_ccam']) {
  $list_codes_ccam = explode('|', $filters['codes_ccam']);
	
	$graphs['ccam'] = array();
	$titles['ccam'] = utf8_encode('Par code CCAM');
	$series = &$graphs['ccam'];
	foreach($list_codes_ccam as $key => $ccam) {
	  $where[] = "operations.codes_ccam LIKE '%$ccam%'";
	  $pos = end(array_keys($where));
	  
	  $series[$key] = array('data' => null, 'label' => $ccam);
	  $data = &$series[$key]['data'];
	  
	  $i = 0;
	  foreach ($dates as $month => $date) {
	    $where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
	    $count = $bs->countList($where, null, null, null, $ljoin);
	    $data[$i] = array($i, $count);
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

$options = array(
  'xaxis' => array('ticks' => $ticks, 'labelsAngle' => 45)
);	

$smarty = new CSmartyDP();

// Filter
$smarty->assign('filters',         $filters);
$smarty->assign('months_relative', $months_relative);
$smarty->assign('months_count',    $months_count);

// Lists
$mediuser = new CMediusers();
$smarty->assign('list_anesth', $mediuser->loadListFromType(array('Anesthésiste')));
$smarty->assign('list_chir',   $mediuser->loadListFromType(array('Chirurgien')));
$smarty->assign('list_codes_asa', range(1, 5));

// Plot data
$smarty->assign('graphs',  $graphs);
$smarty->assign('titles',  $titles);
$smarty->assign('options', $options);

$smarty->display('vw_stats.tpl');
?>