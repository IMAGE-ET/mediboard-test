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
$sejour->loadRefs();
$sejour->_ref_patient->loadRefConstantesMedicales();

$patient =& $sejour->_ref_patient;
$patient->loadRefPhotoIdentite();

// Construction d'une constante médicale
$constantes = new CConstantesMedicales();

if ($sejour->_id) {
  $sejour->loadRefPatient();
  $constantes->patient_id = $sejour->_ref_patient->_id;
  $sejour->loadListConstantesMedicales();
}

$latest_constantes = CConstantesMedicales::getLatestFor($constantes->patient_id);

$constantes->context_class = $sejour->_class_name;
$constantes->context_id = $sejour->_id;

$standard_struct = array(
  'series' => array(
    array('data' => array()),
  ),
);

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
  'poids' => $standard_struct,
  'taille' => $standard_struct,
  'temperature' => $standard_struct,
  'pouls' => $standard_struct,
  'spo2' => $standard_struct,
  'score_sensibilite' => $standard_struct,
  'score_motricite' => $standard_struct,
  'score_sedation' => $standard_struct,
  'EVA' => $standard_struct,
  'frequence_respiratoire' => $standard_struct,
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
    $dates[$i] = mbTransformTime($cst->datetime, null, '%d/%m/%y');
    $hours[$i] = mbTransformTime($cst->datetime, null, '%Hh%M');
    $const_ids[$i] = $cst->_id;
    
    foreach ($data as $name => &$field) {
    	if ($name == 'ta') {
    		$field['series'][0]['data'][$i] = array($i, getValue($cst->_ta_systole));
    		$field['series'][1]['data'][$i] = array($i, getValue($cst->_ta_diastole));
    		continue;
    	}
    	foreach ($field['series'] as &$serie) {
    		$serie['data'][$i] = array($i, getValue($cst->$name));
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
$data['ta']['title'] = htmlentities('Tension artérielle');
$data['ta']['unit'] = 'cmHg';
$data['ta']['standard'] = 12;
$data['ta']['options']['yaxis'] = array(
  'min' => getMin(0,  $data['ta']['series'][0]['data']), // min
  'max' => getMax(30, $data['ta']['series'][0]['data']), // max
);

$data['pouls']['title'] = 'Pouls';
$data['pouls']['unit'] = 'puls./min';
$data['pouls']['standard'] = 60;
$data['pouls']['options']['yaxis'] = array(
  'min' => getMin(50,  $data['pouls']['series'][0]['data']), // min
  'max' => getMax(120, $data['pouls']['series'][0]['data']), // max
);

$data['poids']['title'] = 'Poids';
$data['poids']['unit'] = 'Kg';
$data['poids']['options']['yaxis'] = array(
  'min' => getMin(0,   $data['poids']['series'][0]['data']), // min
  'max' => getMax(150, $data['poids']['series'][0]['data']), // max
);

$data['taille']['title'] = 'Taille';
$data['taille']['unit'] = 'cm';
$data['taille']['options']['yaxis'] = array(
  'min' => getMin(0,   $data['taille']['series'][0]['data']), // min
  'max' => getMax(220, $data['taille']['series'][0]['data']), // max
);

$data['temperature']['title'] = htmlentities('Température');
$data['temperature']['unit'] = htmlentities('°C');
$data['temperature']['standard'] = 37.5;
$data['temperature']['options']['yaxis'] = array(
  'min' => getMin(36, $data['temperature']['series'][0]['data']), // min
  'max' => getMax(41, $data['temperature']['series'][0]['data']), // max
);

$data['spo2']['title'] = htmlentities('Spo2');
$data['spo2']['unit'] = htmlentities('%');
$data['spo2']['options']['yaxis'] = array(
  'min' => getMin(70,  $data['spo2']['series'][0]['data']), // min
  'max' => getMax(100, $data['spo2']['series'][0]['data']), // max
);

$data['score_sensibilite']['title'] = htmlentities('Score de sensibilité');
$data['score_sensibilite']['options']['yaxis'] = array(
  'min' => getMin(0, $data['score_sensibilite']['series'][0]['data']), // min
  'max' => getMax(5, $data['score_sensibilite']['series'][0]['data']), // max
);

$data['score_motricite']['title'] = htmlentities('Score de motricité');
$data['score_motricite']['options']['yaxis'] = array(
  'min' => getMin(0, $data['score_motricite']['series'][0]['data']), // min
  'max' => getMax(5, $data['score_motricite']['series'][0]['data']), // max
);

$data['EVA']['title'] = htmlentities('EVA');
$data['EVA']['options']['yaxis'] = array(
  'min' => getMin(0,  $data['EVA']['series'][0]['data']), // min
  'max' => getMax(10, $data['EVA']['series'][0]['data']), // max
);

$data['score_sedation']['title'] = htmlentities('Score de sédation');
$data['score_sedation']['options']['yaxis'] = array(
  'min' => getMin(70,  $data['score_sedation']['series'][0]['data']), // min
  'max' => getMax(100, $data['score_sedation']['series'][0]['data']), // max
);

$data['frequence_respiratoire']['title'] = htmlentities('Fréquence respiratoire');
$data['frequence_respiratoire']['options']['yaxis'] = array(
  'min' => getMin(70,  $data['frequence_respiratoire']['series'][0]['data']), // min
  'max' => getMax(100, $data['frequence_respiratoire']['series'][0]['data']), // max
);

// Tableau contenant le nom de tous les graphs
$graphs = array();
foreach ($data as $name => &$field) {
	$graphs[] = "constantes-medicales-$name";
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('constantes', $constantes);
$smarty->assign('sejour',     $sejour);
$smarty->assign('data',       $data);
$smarty->assign('dates',      $dates);
$smarty->assign('hours',      $hours);
$smarty->assign('const_ids',  $const_ids);
$smarty->assign('token',      time());
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('graphs', $graphs);
$smarty->display('inc_vw_constantes_medicales.tpl');

?>