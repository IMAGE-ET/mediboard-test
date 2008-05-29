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

// Chargement du séjour
$sejour_id = mbGetValueFromGet('sejour_id', 0);
$sejour = new CSejour;
$sejour->load($sejour_id);

// Costruction d'une constante médicale
$constantes = new CConstantesMedicales();

if ($sejour->_id) {
  $sejour->loadRefPatient();
  $constantes->patient_id = $sejour->_ref_patient->_id;
  $sejour->loadListConstantesMedicales();
}

$constantes->context_class = $sejour->_class_name;
$constantes->context_id = $sejour->_id;

// Initialisation de la structure des données
$data = array(
  'ta' => array(
    'series' => array(
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
    ),
  ),
  'pouls' => array(
    'series' => array(
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
$const_ids = array();
$i = 0;

// Si le séjour a des constantes médicales
if ($sejour->_list_constantes_medicales) {
  foreach ($sejour->_list_constantes_medicales as $cst) {
    $dates[$i] = mbTranformTime($cst->datetime, null, '%d/%m/%y');
    $hours[$i] = mbTranformTime($cst->datetime, null, '%Hh%M');
    $const_ids[$i] = $cst->_id;
    
    $data['ta']['series'][0]['data'][$i] = array($i, getValue($cst->_ta_systole));
    $data['ta']['series'][1]['data'][$i] = array($i, getValue($cst->_ta_diastole));
  
    $data['pouls']['series'][0]['data'][$i] = array($i, getValue($cst->pouls));
  
    $data['temperature']['series'][0]['data'][$i] = array($i, getValue($cst->temperature));
    
    $data['spo2']['series'][0]['data'][$i] = array($i, getValue($cst->spo2));
    $i++;
  }
}

// Mise en place de la ligne de niveau normal pour chaque constante et de l'unité
$data['ta']['title'] = htmlentities('Tension artérielle');
$data['ta']['unit'] = 'cmHg';
$data['ta']['standard'] = 120;
$data['ta']['options']['yaxis'] = array('min' => 0, 'max' => 300);

$data['pouls']['title'] = 'Pouls';
$data['pouls']['unit'] = 'puls./min';
$data['pouls']['standard'] = 60;
$data['pouls']['options']['yaxis'] = array('min' => 40, 'max' => 160);

$data['temperature']['title'] = htmlentities('Température');
$data['temperature']['unit'] = htmlentities('°C');
$data['temperature']['standard'] = 37.5;
$data['temperature']['options']['yaxis'] = array('min' => 35, 'max' => 45);

$data['spo2']['title'] = htmlentities('Spo2');
$data['spo2']['unit'] = htmlentities('%');
$data['spo2']['options']['yaxis'] = array('min' => 0, 'max' => 100);

/*mbTrace(json_encode($data['ta']));
mbTrace(json_encode($data['pouls']));
mbTrace(json_encode($data['temperature']));

mbTrace(json_encode($dates));*/

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('constantes', $constantes);
$smarty->assign('sejour',     $sejour);
$smarty->assign('data',       $data);
$smarty->assign('dates',      $dates);
$smarty->assign('hours',      $hours);
$smarty->assign('const_ids',  $const_ids);
$smarty->assign('token',      time());

$smarty->display('inc_vw_constantes_medicales.tpl');

?>