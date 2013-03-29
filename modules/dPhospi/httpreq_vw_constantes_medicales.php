<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien Ménager
*/

global $m;

$user = CMediusers::get();

if (
    !$user->isMedical() &&
    !CModule::getCanDo('soins')->read &&
    !CModule::getCanDo('dPurgences')->read &&
    !CModule::getCanDo('dPcabinet')->edit
) {
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
$paginate              = CValue::get('paginate', 0);
$start                 = CValue::get('start', 0);
$count                 = CValue::get('count', 50);
$simple_view           = CValue::get('simple_view', 0);
$host_guid             = CValue::get('host_guid');

if (!$start) {
  $start = 0;
}

if ($paginate) {
  $limit = "$start,$count";
}
else {
  $limit = $count;
}

$current_context = null;
if ($context_guid) {
  $current_context = CMbObject::loadFromGuid($context_guid);
}

//mbTrace($current_context->_view, "CONTEXT");

if (!$selection || $selected_context_guid === 'all') {
  /** @var CGroups|CService|CRPU $host */

  // On cherche le meilleur "hebergement" des constantes, pour charger les configurations adequat
  if ($host_guid) {
    $host = CMbObject::loadFromGuid($host_guid);
  }
  else {
    $host = CConstantesMedicales::guessHost($current_context);
  }

  $important_constantes = CConstantesMedicales::getHostConfig("important_constantes", $host);
  $conf_constantes = explode("|", $important_constantes);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, array_flip($conf_constantes));
}
else {
  $selection_flip = array_flip($selection);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, $selection_flip);
}

$constants_to_draw = ($print == 1 ? $selection : CConstantesMedicales::$list_constantes);

/** @var CMbObject|CPatient|CSejour $context */
if ($selected_context_guid !== 'all') {
  $context = CMbObject::loadFromGuid($selected_context_guid);
}
else {
  $context = CMbObject::loadFromGuid($context_guid);
}
  
$context->loadRefs();

if ($context) {
  if ($context instanceof CPatient) {
    $patient = $context;
  }
  else {
    $patient = $context->_ref_patient;
  }
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
$where_context = $where;
$where_context["context_class"] = "IS NOT NULL";
$where_context["context_id"] = "IS NOT NULL";

$query = new CRequest;
$query->addTable($constantes->_spec->table);
$query->addColumn("context_class");
$query->addColumn("context_id");
$query->addWhere($where_context);
$query->addGroup(array("context_class", "context_id"));

$query = $query->getRequest();
$list = $constantes->_spec->ds->loadList($query);
$list_contexts = array();

foreach ($list as $_context) {
  /** @var CMbObject $c */
  $c = new $_context["context_class"];
  $c = $c->getCached($_context["context_id"]);

  // Cas d'un RPU
  if ($c instanceof CConsultation && $c->sejour_id) {
    continue;
  }

  $c->loadRefsFwd();
  $list_contexts[$c->_guid] = $c;
}

if ($current_context instanceof CConsultation) {
  $current_context->loadComplete();
}

// Cas d'un RPU
if ($current_context instanceof CConsultation && $current_context->sejour_id) {
  $current_context->loadRefSejour();
  $current_context = $current_context->_ref_sejour;
  $current_context->loadComplete();
  $context = $current_context;
  $context_guid = $current_context->_guid;
}
if (!isset($list_contexts[$current_context->_guid])) {
  $current_context->loadRefsFwd();
  $list_contexts[$current_context->_guid] = $current_context;
}

if (!count($list_contexts)) {
  $list_contexts[] = $current_context;
}

if ($context && $selected_context_guid !== 'all') {
  $where["context_class"] = " = '$context->_class'";
  $where["context_id"] = " = '$context->_id'";
  
  // Needed to know if we are in the right context
  $constantes->context_class = $context->_class;
  $constantes->context_id = $context->_id;
  $constantes->loadRefContext();
}

$whereOr = array();
foreach ($constants_to_draw as $name => $params) {
  if ($name[0] === "_") {
    continue;
  }
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
$list_constantes = $constantes->loadList($where, "datetime DESC", $limit);
$total_constantes = $constantes->countList($where);

$constantes_medicales_grid = CConstantesMedicales::buildGrid($list_constantes, false);

$list_constantes = array_reverse($list_constantes, true);

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
  $orig_n = $n;
  
  if (substr($n, 0, 1) == "@") {
    $n = -10e6;
  }
  
  $max = $n;
  foreach ($array as $a) {
    if (isset($a[1])) {
      $max = max($n, $a[1], $max);
    }
  }
    
  if ($orig_n != $n) {
    $max += floatval(substr($orig_n, 1));
  }
  
  return $max;
}

function getMin($n, $array) {
  $orig_n = $n;
  
  if (substr($n, 0, 1) == "@") {
    $n = +10e6;
  }
  
  $min = $n;
  foreach ($array as $a) {
    if (isset($a[1])) {
      $min = min($n, $a[1], $min);
    }
  }
    
  if ($orig_n != $n) {
    $min += floatval(substr($orig_n, 1));
  }
  
  return $min;
}

$dates     = array();
$hours     = array();
$comments  = array();
$const_ids = array();
$data      = array();
$graphs    = array();

foreach ($constants_to_draw as $name => $params) {
  if ($name[0] === "_" && empty($params["plot"]) && empty($params["formula"])) {
    continue;
  }
  
  $data[$name] = $standard_struct;
  
  if (isset($params["formfields"]) && empty($params["candles"])) {
    $serie = &$data[$name]["series"];
    
    $serie = array();
    foreach ($params["formfields"] as $_field) {
      $serie[] = array(
        "data" => array(),
        "label" => CAppUI::tr("CConstantesMedicales-$_field-court"),
      );
    }
  }
}

$cumuls_day = array();

// Si le séjour a des constantes médicales
if ($list_constantes) {
  $reset_hours = array();
  foreach ($list_constantes as $cst) {
    $comment = utf8_encode($cst->comment);
    $dates[] = CMbDT::transform($cst->datetime, null, '%d/%m/%y');
    $hours[] = CMbDT::transform($cst->datetime, null, '%Hh%M');
    $comments[] = $comment;
    $const_ids[] = $cst->_id;
    $cst->loadLogs();
    
    foreach ($constants_to_draw as $name => $params) {
      if ($name[0] === "_" && empty($params["plot"]) && empty($params["formula"])) {
        continue;
      }
      
      $candles = isset($params["candles"]);
      
      $d = &$data[$name];

      $user_view = "";
      
      if ($cst->$name !== null && $name[0] !== "_") {
        $log = $cst->loadLastLogForField($name);
        if (!$log->_id && $cst->_ref_last_log) {
          $log = $cst->_ref_last_log;
        }
        
        $_user = $log->loadRefUser();
        
        if ($_user) {
          $user_view = utf8_encode($_user->_view);
        }
      }
      
      // normal plots
      if (empty($params["candles"])) {
        if (isset($params["formfields"])) {
          $fields = $params["formfields"];
        }
        else {
          $fields = array($name);
        }
        
        $i = count($d["series"][0]["data"]);
        foreach ($fields as $n => $_field) {
          $ya = $yb = $yc = $yd = null;
          
          $ya = getValue($cst->$_field);
            
          $d["series"][$n]["data"][] = array(
            $i, 
            $ya, $yb, $yc, $yd,
            $user_view, 
            $comment,
            utf8_encode($params['unit']),
          );
          
          if (isset($params["cumul_reset_config"])) {
            if (!isset($reset_hours[$name])) {
              $reset_hours[$name] = CConstantesMedicales::getResetHour($name);
            }
            $reset_hour = $reset_hours[$name];

            $day_24h = CMbDT::transform("-$reset_hour hours", $cst->datetime, '%d/%m/%y');
    
            if (!isset($cumuls_day[$name][$day_24h])) {
              $cumuls_day[$name][$day_24h] = array("n" => 0, "value" => null);
            }
            
            if (isset($params["formula"])) {
              $formula = $params["formula"];
              
              foreach ($formula as $_field => $_sign) {
                $_value = $cst->$_field;
                
                if ($_value !== null && $_value !== "") {
                  if ($_sign === "+") {
                    $cumuls_day[$name][$day_24h]["value"] += $_value;
                  }
                  else {
                    $cumuls_day[$name][$day_24h]["value"] -= $_value;
                  }
                }
              }
            }
            else {
              if ($ya !== null && $ya !== "") {
                $cumuls_day[$name][$day_24h]["value"] += $ya;
              }
            }
            
            $cumuls_day[$name][$day_24h]["n"]++;
          }
        }
      }
        
      // composite plots (TA)
      else {
        $fields = $params["formfields"];
        $first = true;
        
        $i = count($d["series"][0]["data"]);
        foreach ($fields as $n => $_field) {
          $ya = $yb = $yc = $yd = null;
          
          // first series : all values
          if ($first) {
            $ya = $yb = getValue($cst->{$fields[1]});
            $yc = $yd = getValue($cst->{$fields[0]});
            
            $d["series"][$n]["candles"]["show"] = true;
            $d["series"][$n]["markers"]["position"] = "cb";
          }
          
          // second series : only second value
          else {
            $ya = getValue($cst->$fields[0]);
            $d["series"][$n]["markers"]["position"] = "ct";
          }
          
          $d["series"][$n]["markers"]["show"] = true;
          $d["series"][$n]["lines"]["show"] = false;
          $d["series"][$n]["points"]["show"] = false;
            
          $d["series"][$n]["data"][] = array(
            $i, 
            $ya, $yb, $yc, $yd,
            $user_view, 
            $comment,
            utf8_encode($params['unit']),
          );
          
          $first = false;
        }
      }
     
      $graphs[] = "constantes-medicales-$name";
    }
  }
}

// Pour les tensions artérielles, changer les unités suivant la config
$unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");

foreach ($cumuls_day as $name => $days) {
  $_data = &$data[$name];
  $_params = CConstantesMedicales::$list_constantes[$name];
  
  $offset = 0;
  foreach ($days as $day => $values) {
    $_color = CConstantesMedicales::getColor($values["value"], $_params, "#4DA74D");
    
    $_data["series"][] = array(
      "data" => array(array(
        $offset-0.5, 
        $values["value"], 
        utf8_encode(CAppUI::tr("CConstantesMedicales-$name-desc")), 
        null,
        utf8_encode(CValue::read($_params, "unit")),
      )),
      "cumul" => $day,
      "lines" => array("show" => false),
      "points" => array("show" => false),
      "markers" => array(
        "show" => true,
        "position" => "rt",
      ),
      "bars" => array(
        "show" => true,
        "barWidth" => $values["n"],
        "centered" => false,
        "lineWidth" => 1,
      ),
      "color" => $_color,
      "mouse" => array(
        "relative" => false,
        "position" => "nw",
      ),
    );
    
    $offset += $values["n"];
  }
  
  $first = array_shift($_data["series"]);
  array_push($_data["series"], $first);
}

foreach ($data as $name => &$_data) {
  $params = CConstantesMedicales::$list_constantes[$name];
  
  // And the options
  if (isset($params["standard"])) {
    $_data["standard"] = $params["standard"];
  }
  
  $margin_ratio = 0;
  
  if (in_array($name, array("ta", "ta_gauche", "ta_droit"))) {
    $margin_ratio = 0.25;
  }
  
  // On cache les valeurs, qui sont a zero à cause du " " de la valeur de _diurese (pour forcer son affichage)
  if (isset($params["formula"])) {
    $_data["series"][count($_data["series"])-1]["hide"] = true;
  }
  
  $all_y_values = CMbArray::pluck($_data["series"], "data");
  $y_values = array();
  
  foreach ($all_y_values as $_values) {
    $y_values = array_merge($y_values, $_values);
  }
  
  $margin = abs($params["min"] - $params["max"]) * $margin_ratio;
  
  $margin_top = $margin_bottom = $margin;
  
  if (isset($params["cumul_reset_config"])) {
    $margin_top = getMax($params["max"], $y_values) * 0.2;
  }
  
  $_data["options"] = array(
    "title" => utf8_encode(CAppUI::tr("CConstantesMedicales-$name-desc").($params['unit'] ? " ({$params['unit']})" : "")),
    "yaxis" => array(
      "min" => getMin($params["min"], $y_values) - $margin_bottom, // min
      "max" => getMax($params["max"], $y_values) + $margin_top, // max
    )
  );
  
  if (isset($params["colors"])) {
    $_data["options"]["colors"] = $params["colors"];
  }
}

//mbTrace($data);

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
$smarty->assign('start',         $start);
$smarty->assign('count',         $count);
$smarty->assign('total_constantes', $total_constantes);
$smarty->assign('paginate',      $paginate);
$smarty->assign('constantes_medicales_grid', $constantes_medicales_grid);
$smarty->assign('simple_view',   $simple_view);
$smarty->display('inc_vw_constantes_medicales.tpl');
