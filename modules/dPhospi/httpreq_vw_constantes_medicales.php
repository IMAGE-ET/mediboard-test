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
  if(!CModule::getCanDo('soins')->read && !CModule::getCanDo('dPurgences')->read && !CModule::getCanDo('dPcabinet')->edit){
    CModule::getCanDo($m)->redirect();
  }
}

$context_guid = CValue::get('context_guid');
$selected_context_guid = CValue::get('selected_context_guid', $context_guid);
$patient_id = CValue::get('patient_id');
$readonly = CValue::get('readonly');

$context = null;
$patient = null;

if ($selected_context_guid !== 'all') {
  $context = CMbObject::loadFromGuid($selected_context_guid);
}
else {
  $context = CMbObject::loadFromGuid($context_guid);
}
$context->loadRefs();

if ($context) {
  $patient = $context->_ref_patient;
}

if ($patient_id) {
  $patient = new CPatient;
  $patient->load($patient_id);
}

$patient->loadRefConstantesMedicales();
$patient->loadRefPhotoIdentite();

// Construction d'une constante médicale
$constantes = new CConstantesMedicales();
$constantes->patient_id = $patient->_id;
$constantes->loadRefPatient();

// Les constantes qui correspondent (dans le contexte ou non)
$list_constantes = $constantes->loadMatchingList('datetime');

$list_contexts = array();
foreach($list_constantes as $const) {
  if ($const->context_class && $const->context_id) {
    $c = new $const->context_class;
    $c->load($const->context_id);
    $c->loadComplete();
    if ($c instanceof CConsultation && $c->sejour_id) continue; // Cas d'un RPU
    $list_contexts[$c->_guid] = $c;
  }
}

$current_context = CMbObject::loadFromGuid($context_guid);
$current_context->loadComplete();

// Cas d'un RPU
if ($current_context instanceof CConsultation && $current_context->sejour_id) {
  $current_context->loadRefSejour();
  $current_context = $current_context->_ref_sejour;
  $current_context->loadComplete();
  $context = $current_context;
  $context_guid = $current_context->_guid;
}
if (!isset($list_contexts[$current_context->_guid])){
  $list_contexts[$current_context->_guid] = $current_context;
}

if (!count($list_contexts)) {
  $list_contexts[] = $current_context;
}

if ($context && $selected_context_guid !== 'all') {
  $constantes->context_class = $context->_class_name;
  $constantes->context_id = $context->_id;
	$constantes->loadRefContext();
}

// Les constantes qui correspondent (dans le contexte cette fois)
$list_constantes = $constantes->loadMatchingList('datetime');

// La liste des derniers mesures
$latest_constantes = CConstantesMedicales::getLatestFor($patient->_id);

$standard_struct = array(
  'series' => array(
    array('data' => array()),
  ),
);

// Initialisation de la structure des données
$data = array();
foreach(CConstantesMedicales::$list_constantes as $cst) {
  $data[$cst] = $standard_struct;
}

$data['ta'] = array(
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
);

$data['injection'] = array(
  'series' => array(
    array(
      'data' => array(),
      'label' => 'Nb injections',
    ),
    array(
      'data' => array(),
      'label' => 'Nb essais',
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
if ($list_constantes) {
  foreach ($list_constantes as $cst) {
    $dates[$i] = mbTransformTime($cst->datetime, null, '%d/%m/%y');
    $hours[$i] = mbTransformTime($cst->datetime, null, '%Hh%M');
    $const_ids[$i] = $cst->_id;
    $cst->loadLogs();
    
    foreach ($data as $name => &$field) {
      $log = $cst->loadLastLogForField($name);
      if (!$log->_id) {
        $log = $cst->_ref_last_log;
        $log->loadRefsFwd();
      }
      $user_view = $log->_ref_user ? utf8_encode($log->_ref_user->_view) : "";
      
    	if ($name == 'ta') {
    		$field['series'][0]['data'][$i] = array($i, getValue($cst->_ta_systole), $user_view);
    		$field['series'][1]['data'][$i] = array($i, getValue($cst->_ta_diastole), $user_view);
    		continue;
    	}
			if ($name == 'injection') {
        $field['series'][0]['data'][$i] = array($i, getValue($cst->_inj), $user_view);
        $field['series'][1]['data'][$i] = array($i, getValue($cst->_inj_essai), $user_view);
        continue;
      }
    	foreach ($field['series'] as &$serie) {
    		$serie['data'][$i] = array($i, getValue($cst->$name), $user_view);
    	}
    }
    $i++;
  }
}

function getMax($n, $array) {
  $max = -PHP_INT_MAX;
  
  foreach ($array as $a) {
    if (isset($a[1])) {
      $max = max($n, $a[1], $max);
    }
  }
  return $max;
}

function getMin($n, $array) {
  $min = PHP_INT_MAX;
  
  foreach ($array as $a) {
    if (isset($a[1])) {
      $min = min($n, $a[1], $min);
    }
  }
  return $min;
}

// Mise en place de la ligne de niveau normal pour chaque constante et de l'unité
$data['ta']['standard'] = 12;
$data['ta']['options']['title'] = utf8_encode('Tension artérielle (cmHg)');
$data['ta']['options']['yaxis'] = array(
  'min' => getMin(5,  $data['ta']['series'][0]['data']), // min
  'max' => getMax(20, $data['ta']['series'][0]['data']), // max
);

$data['pouls']['standard'] = 60;
$data['pouls']['options']['title'] = utf8_encode('Pouls (puls./min)');
$data['pouls']['options']['yaxis'] = array(
  'min' => getMin(50,  $data['pouls']['series'][0]['data']), // min
  'max' => getMax(120, $data['pouls']['series'][0]['data']), // max
);

$data['poids']['options']['title'] = utf8_encode('Poids (Kg)');
$data['poids']['options']['yaxis'] = array(
  'min' => getMin(0,   $data['poids']['series'][0]['data']), // min
  'max' => getMax(150, $data['poids']['series'][0]['data']), // max
);

$data['taille']['options']['title'] = utf8_encode('Taille (cm)');
$data['taille']['options']['yaxis'] = array(
  'min' => getMin(0,   $data['taille']['series'][0]['data']), // min
  'max' => getMax(220, $data['taille']['series'][0]['data']), // max
);

$data['temperature']['standard'] = 37.5;
$data['temperature']['options']['title'] = utf8_encode('Température (°C)');
$data['temperature']['options']['yaxis'] = array(
  'min' => getMin(36, $data['temperature']['series'][0]['data']), // min
  'max' => getMax(41, $data['temperature']['series'][0]['data']), // max
);

$data['spo2']['options']['title'] = utf8_encode('Spo2 (%)');
$data['spo2']['options']['yaxis'] = array(
  'min' => getMin(70,  $data['spo2']['series'][0]['data']), // min
  'max' => getMax(100, $data['spo2']['series'][0]['data']), // max
);

$data['score_sensibilite']['options']['title'] = utf8_encode('Score de sensibilité');
$data['score_sensibilite']['options']['yaxis'] = array(
  'min' => getMin(0, $data['score_sensibilite']['series'][0]['data']), // min
  'max' => getMax(5, $data['score_sensibilite']['series'][0]['data']), // max
);

$data['score_motricite']['options']['title'] = utf8_encode('Score de motricité');
$data['score_motricite']['options']['yaxis'] = array(
  'min' => getMin(0, $data['score_motricite']['series'][0]['data']), // min
  'max' => getMax(5, $data['score_motricite']['series'][0]['data']), // max
);

$data['EVA']['options']['title'] = utf8_encode('EVA');
$data['EVA']['options']['yaxis'] = array(
  'min' => getMin(0,  $data['EVA']['series'][0]['data']), // min
  'max' => getMax(10, $data['EVA']['series'][0]['data']), // max
);

$data['score_sedation']['options']['title'] = utf8_encode('Score de sédation');
$data['score_sedation']['options']['yaxis'] = array(
  'min' => getMin(70,  $data['score_sedation']['series'][0]['data']), // min
  'max' => getMax(100, $data['score_sedation']['series'][0]['data']), // max
);

$data['frequence_respiratoire']['options']['title'] = utf8_encode('Fréquence respiratoire');
$data['frequence_respiratoire']['options']['yaxis'] = array(
  'min' => getMin(0,  $data['frequence_respiratoire']['series'][0]['data']), // min
  'max' => getMax(60, $data['frequence_respiratoire']['series'][0]['data']), // max
);

//$data['glycemie']['standard'] = 1;
$data['glycemie']['options']['title'] = utf8_encode('Glycémie (g/l)');
$data['glycemie']['options']['yaxis'] = array(
  'min' => getMin(0, $data['glycemie']['series'][0]['data']), // min
  'max' => getMax(4, $data['glycemie']['series'][0]['data']), // max
);

$data['diurese']['options']['title'] = utf8_encode('Diurèse (ml)');
$data['diurese']['options']['yaxis'] = array(
  'min' => getMin(0, $data['diurese']['series'][0]['data']), // min
  'max' => getMax(2000, $data['diurese']['series'][0]['data']), // max
);

$data['redon']['options']['title'] = utf8_encode('Redon (ml)');
$data['redon']['options']['yaxis'] = array(
  'min' => getMin(0, $data['redon']['series'][0]['data']), // min
  'max' => getMax(500, $data['redon']['series'][0]['data']), // max
);

$data['injection']['options']['title'] = utf8_encode('Nombre d\'injections');
$data['injection']['options']['yaxis'] = array(
  'min' => getMin(0,  $data['injection']['series'][0]['data']), // min
  'max' => getMax(10,  $data['injection']['series'][0]['data']), // min
);


// Tableau contenant le nom de tous les graphs
$graphs = array();
foreach ($data as $name => &$field) {
	$graphs[] = "constantes-medicales-$name";
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('readonly', $readonly);
$smarty->assign('constantes', $constantes);
$smarty->assign('context',    $context);
$smarty->assign('context_guid', $context_guid);
$smarty->assign('list_contexts', $list_contexts);
$smarty->assign('all_contexts',    $selected_context_guid == 'all');
$smarty->assign('patient',    $patient);
$smarty->assign('data',       $data);
$smarty->assign('dates',      $dates);
$smarty->assign('hours',      $hours);
$smarty->assign('const_ids',  $const_ids);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('graphs', $graphs);
$smarty->display('inc_vw_constantes_medicales.tpl');

?>