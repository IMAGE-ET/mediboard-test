<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage bloodSalvage
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

global  $can;
$can->needsAdmin();

$filters         = mbGetValueFromGetOrSession('filters', array());
$months_count    = mbGetValueFromGetOrSession('months_count', 12);
$months_relative = mbGetValueFromGetOrSession('months_relative', 0);

$possible_filters = array('chir_id', 'anesth_id', 'codes_ccam', 'code_asa');
foreach ($possible_filters as $n) {
	if (!isset($filters[$n])) $filters[$n] = null;
}

$data = array();

$dates = array();
$ticks = array();
$options = array();

// Dates
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

if ($filters['anesth_id']) {
	$where['operations.anesth_id'] = " = '{$filters['anesth_id']}'";
}

if ($filters['chir_id']) {
  $where['plagesop.chir_id'] = " = '{$filters['chir_id']}'";
}

if ($filters['codes_ccam']) {
	$list_codes_ccam = explode('|', $filters['codes_ccam']);
	$whereCode = array();
	foreach ($list_codes_ccam as $code) {
		$whereCode[] = "operations.codes_ccam LIKE '%$code%'";
	}
	$where[] = implode(' OR ', $whereCode);
}

if ($filters['code_asa']) {
  $where['consultation_anesth.ASA'] = " = '{$filters['code_asa']}'";
}

$bs = new CBloodSalvage();
$i = 0;
foreach ($dates as $month => $date) {
	$where['plagesop.date'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
	
	$count = $bs->countList($where, null, null, null, $ljoin);
	
	$ticks[$i] = array($i, mbTransformTime(null, $month, '%m/%y'));
	$data[$i] = array($i, $count);
	$i++;
}

$options['xaxis'] = array('ticks' => $ticks);
$series = array(array('data' => $data, 'label' => utf8_encode(CAppUI::tr('CBloodSalvage'))));

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
$smarty->assign('series',  $series);
$smarty->assign('options', $options);

$smarty->display('vw_stats.tpl');
?>