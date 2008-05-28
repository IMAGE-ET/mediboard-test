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

if ($sejour->_id) {
  $sejour->loadRefPatient();
  $sejour->loadListConstantesMedicales();
}

$new_constantes = new CConstantesMedicales();
$new_constantes->context_class = $sejour->_class_name;
$new_constantes->context_id = $sejour->_id;
$new_constantes->patient_id = $sejour->_ref_patient->_id;

// Initialisation de la structure
$data = array(
  'ta' => array(
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
    'series' => array(
      array('data' => array()),
      array('data' => array()),
    ),
  ),
  'pouls' => array(
    'series' => array(
      array('data' => array()),
      array('data' => array()),
    ),
  ),
  'spo2' => array(
    'series' => array(
      array('data' => array()),
    ),
  ),
);

// Petite fonction utilitaire de récupération des valeurs
function getValue($v) {
  return ($v === null) ? null : floatval($v);
}

$dates = array();
$hours = array();
$i = 0;
foreach ($sejour->_list_constantes_medicales as $cst) {
  $dates[$i] = mbTranformTime($cst->datetime, null, '%d/%m/%y');
  $hours[$i] = mbTranformTime($cst->datetime, null, '%Hh%M');
  
  $data['ta']['series'][1]['data'][$i] = array($i, getValue($cst->_ta_systole));
  $data['ta']['series'][2]['data'][$i] = array($i, getValue($cst->_ta_diastole));

  $data['pouls']['series'][1]['data'][$i] = array($i, getValue($cst->pouls));

  $data['temperature']['series'][1]['data'][$i] = array($i, getValue($cst->temperature));
  
  $data['spo2']['series'][0]['data'][$i] = array($i, getValue($cst->spo2));
  $i++;
}

// Mise en place de la ligne de niveau normal pour chaque constante et de l'unité
$n = 100;
$data['ta']['title'] = htmlentities('Tension artérielle');
$data['ta']['unit'] = 'cmHg';
$data['ta']['series'][0]['data'] = array(array(0, 120), array($n, 120));
$data['ta']['series'][0]['points']['show'] = false;
$data['ta']['series'][0]['mouse']['track'] = false;

$data['pouls']['title'] = 'Pouls';
$data['pouls']['unit'] = 'puls./min';
$data['pouls']['series'][0]['data'] = array(array(0, 60), array($n, 60));
$data['pouls']['series'][0]['points']['show'] = false;
$data['pouls']['series'][0]['mouse']['track'] = false;

$data['temperature']['title'] = htmlentities('Température');
$data['temperature']['unit'] = htmlentities('°C');
$data['temperature']['series'][0]['data'] = array(array(0, 37.5), array($n, 37.5));
$data['temperature']['series'][0]['points']['show'] = false;
$data['temperature']['series'][0]['mouse']['track'] = false;

$data['spo2']['title'] = htmlentities('Spo2');
$data['spo2']['unit'] = htmlentities('%');

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