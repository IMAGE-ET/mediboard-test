<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien Ménager
*/

global $AppUI, $can, $m;

$user = new CMediusers();
$user->load($AppUI->user_id);

if(!$user->isPraticien()) {
  $can->needsRead();
}

$sejour_id = mbGetValueFromGet('sejour_id', 0);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadListConstantesMedicales();

$new_constantes = new CConstantesMedicales();
$new_constantes->context_class = $sejour->_class_name;
$new_constantes->context_id = $sejour->_id;
$new_constantes->patient_id = $sejour->_ref_patient->_id;

$data = array(
  'ta' => array(
    'title' => 'Tension artérielle',
    'series' => array(
      array('data' => array()),
      array(
        'data' => array(),
        'label' => 'Systole',
      ),
      array(
        'data' => array(),
        'label' => 'Diastole',
      ),
    ),
  ),
  'temperature' => array(
    'title' => 'Température',
    'series' => array(
      array('data' => array()),
      array('data' => array()),
    ),
  ),
  'pouls' => array(
    'title' => 'Pouls',
    'series' => array(
      array('data' => array()),
      array('data' => array()),
    ),
  ),
);

function getValue($v) {
  if ($v === null) {
    return null;
  } else {
    return floatval($v);
  }
}

$dates = array();
$hours = array();
$i = 0;
foreach ($sejour->_list_constantes_medicales as $cst) {
  $dates[$i] = mbTranformTime($cst->datetime, null, '%d/%m/%y');
  $hours[$i] = mbTranformTime($cst->datetime, null, '%H:%M');
  
  $data['ta']['series'][1]['data'][$i] = array($i, getValue($cst->_ta_systole));
  $data['ta']['series'][2]['data'][$i] = array($i, getValue($cst->_ta_diastole));

  $data['pouls']['series'][1]['data'][$i] = array($i, getValue($cst->pouls));

  $data['temperature']['series'][1]['data'][$i] = array($i, getValue($cst->temperature));
  $i++;
}

$n = count($dates)-1;
$data['ta']['series'][0]['data'] = array(array(0, 120), array($n, 120));
$data['ta']['series'][0]['points']['show'] = false;
$data['ta']['series'][0]['mouse']['track'] = false;

$data['pouls']['series'][0]['data'] = array(array(0, 60), array($n, 60));
$data['pouls']['series'][0]['points']['show'] = false;
$data['pouls']['series'][0]['mouse']['track'] = false;

$data['temperature']['series'][0]['data'] = array(array(0, 37.5), array($n, 37.5));
$data['temperature']['series'][0]['points']['show'] = false;
$data['temperature']['series'][0]['mouse']['track'] = false;

/*mbTrace(json_encode($data['ta']));
mbTrace(json_encode($data['pouls']));
mbTrace(json_encode($data['temperature']));

mbTrace(json_encode($dates));*/

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('new_constantes', $new_constantes);
$smarty->assign('sejour',         $sejour);
$smarty->assign('data',           $data);
$smarty->assign('dates',          $dates);
$smarty->assign('hours',          $hours);
$smarty->assign('token',          time());

$smarty->display('inc_vw_constantes_medicales.tpl');

?>