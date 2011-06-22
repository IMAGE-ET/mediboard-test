<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien Ménager
*/

global $m;

$user = CMediusers::get();

if(!$user->isMedical() &&
   !CModule::getCanDo('soins')->read && 
   !CModule::getCanDo('dPurgences')->read && 
   !CModule::getCanDo('dPcabinet')->edit){
     CModule::getCanDo($m)->redirect();
}

$context_guid          = CValue::get('context_guid');
$selected_context_guid = CValue::get('selected_context_guid', $context_guid);
$patient_id            = CValue::get('patient_id');
$readonly              = CValue::get('readonly');
$selection             = CValue::get('selection');
$date_min              = CValue::get('date_min');
$date_max              = CValue::get('date_max');
$print                 = CValue::get('print');

if (!$selection || $selected_context_guid === 'all') {
  //$selection = CConstantesMedicales::$list_constantes;
  $conf_constantes = explode("|", CAppUI::conf("dPpatients CConstantesMedicales important_constantes"));
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, array_flip($conf_constantes));
}
else {
  $selection_flip = array_flip($selection);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, $selection_flip);
}

$constants_to_draw = ($print == 1 ? $selection : CConstantesMedicales::$list_constantes);

if ($selected_context_guid !== 'all')
  $context = CMbObject::loadFromGuid($selected_context_guid);
else
  $context = CMbObject::loadFromGuid($context_guid);
  
$context->loadRefs();

if ($context) {
  $patient = $context->_ref_patient;
}

if ($patient_id) {
  $patient = new CPatient;
  $patient->load($patient_id);
}

$latest_constantes = $patient->loadRefConstantesMedicales();
$patient->loadRefPhotoIdentite();

$where = array(
  "patient_id" => " = '$patient->_id'"
);

// Construction d'une constante médicale
$constantes = new CConstantesMedicales();
$constantes->patient_id = $patient->_id;
$constantes->loadRefPatient();

// Les constantes qui correspondent (dans le contexte ou non)
$list_constantes = $constantes->loadList($where, "datetime");

$list_contexts = array();
foreach($list_constantes as $const) {
  if ($const->context_class && $const->context_id) {
    $c = new $const->context_class;
    $c = $c->getCached($const->context_id);
    if ($c instanceof CConsultation && $c->sejour_id) continue; // Cas d'un RPU
    $c->loadRefsFwd();
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
  $where["context_class"] = " = '$context->_class_name'";
  $where["context_id"] = " = '$context->_id'";
  
  // Needed to know if we are in the right context
  $constantes->context_class = $context->_class_name;
  $constantes->context_id = $context->_id;
  $constantes->loadRefContext();
}

$whereOr = array();
foreach($constants_to_draw as $name => $params) {
  if ($name[0] === "_") continue;
  $whereOr[] = "$name IS NOT NULL ";
}
$where[] = implode(" OR ", $whereOr);

if ($date_min) {
  $where[] = "datetime >= '$date_min'";
}

if ($date_max) {
  $where[] = "datetime <= '$date_max'";
}

// Les constantes qui correspondent (dans le contexte cette fois)
$list_constantes = $constantes->loadList($where, "datetime");

$standard_struct = array(
  "series" => array(
    array(
      "data" => array(),
      //"options" => array()
    )
  )
);

// Petite fonction utilitaire de récupération des valeurs
function getValue($v) {
  return ($v === null) ? null : floatval($v);
}

function getMax($n, $array) {
  $max = $n;
  foreach ($array as $a)
    if (isset($a[1])) $max = max($n, $a[1], $max);
  return $max;
}

function getMin($n, $array) {
  $min = $n;
  foreach ($array as $a) 
    if (isset($a[1]))$min = min($n, $a[1], $min);
  return $min;
}

$dates     = array();
$hours     = array();
$comments  = array();
$const_ids = array();
$data      = array();
$graphs    = array();

foreach ($constants_to_draw as $name => $params) {
  if ($name[0] === "_") continue;
  
  $data[$name] = $standard_struct;
  
  if (isset($params["formfields"])) {
    $serie = &$data[$name]["series"];
    
    $serie = array();
    foreach($params["formfields"] as $_field) {
      $serie[] = array(
        "data" => array(),
        "label" => CAppUI::tr("CConstantesMedicales-$_field-court"),
      );
    }
  }
}

// Si le séjour a des constantes médicales
if ($list_constantes) {
  foreach ($list_constantes as $cst) {
  	$comment = utf8_encode($cst->comment);
    $dates[] = mbTransformTime($cst->datetime, null, '%d/%m/%y');
    $hours[] = mbTransformTime($cst->datetime, null, '%Hh%M');
    $comments[] = $comment;
    $const_ids[] = $cst->_id;
    $cst->loadLogs();
    
    foreach ($constants_to_draw as $name => $params) {
      if ($name[0] === "_") continue;
      
      $d = &$data[$name];

      $user_view = "";
      $log = $cst->loadLastLogForField($name);
      if (!$log->_id && $cst->_ref_last_log) {
        $log = $cst->_ref_last_log;
      }
      $log->loadRefsFwd();
      
      if ($log->_ref_user) {
        $user_view = utf8_encode($log->_ref_user->_view);
      }
    
      // We push the values
      if (isset($params["formfields"])) {
        $fields = $params["formfields"];
      }
      else {
        $fields = array($name);
      }
      
      $i = count($d["series"][0]["data"]);
      foreach($fields as $n => $_field) {
        //if ($cst->$_field !== null && $cst->$_field !== "") // We have to show empty points too!
          $d["series"][$n]["data"][] = array($i, getValue($cst->$_field), $user_view, $comment);
      }
     
      $graphs[] = "constantes-medicales-$name";
    }
  }
}

// Pour les tensions artérielles, changer les unités suivant la config
$unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");

foreach($data as $name => &$_data) {
  $params = CConstantesMedicales::$list_constantes[$name];
  
  if (in_array($name, array("ta", "ta_gauche", "ta_droit"))) {
    $params['unit'] = $unite_ta;
  }
  
  // And the options
  if (isset($params["standard"])) {
    $_data["standard"] = $params["standard"];
  }
  $_data["options"] = array(
    "title" => utf8_encode(CAppUI::tr("CConstantesMedicales-$name-desc").($params['unit'] ? " ({$params['unit']})" : "")),
    "yaxis" => array(
      "min" => getMin($params["min"], $_data["series"][0]["data"]), // min
      "max" => getMax($params["max"], $_data["series"][0]["data"]), // max
    )
  );
  
  if (isset($params["colors"])) {
    $_data["options"]["colors"] = $params["colors"];
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('readonly',      $readonly);
$smarty->assign('constantes',    $constantes);
$smarty->assign('context',       $context);
$smarty->assign('context_guid',  $context_guid);
$smarty->assign('list_contexts', $list_contexts);
$smarty->assign('all_contexts',  $selected_context_guid == 'all');
$smarty->assign('patient',       $patient);
$smarty->assign('data',          $data);
$smarty->assign('dates',         $dates);
$smarty->assign('hours',         $hours);
$smarty->assign('comments',      $comments);
$smarty->assign('const_ids',     $const_ids);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('selection',     $selection);
$smarty->assign('print',         $print);
$smarty->assign('graphs',        $graphs);
$smarty->display('inc_vw_constantes_medicales.tpl');

?>