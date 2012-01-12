<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$operation_id = CValue::get("operation_id");

$interv = new COperation;
$interv->load($operation_id);
$interv->loadRefPlageOp();

$result_sets = $interv->loadBackRefs("observation_result_sets");
$date = mbDate($interv->_datetime);

function getTS($time) {
  static $default_timezone;
  if (!$default_timezone) $default_timezone = date_default_timezone_get();
  
  date_default_timezone_set("UTC");
  $time = strtotime($time) * 1000; // in ms;
  date_default_timezone_set($default_timezone);
  
  return $time;
}

$data = array(
  // [value_type_id] => $values
);
$times = array();
$time_min = $interv->entree_salle;
$time_max = mbTime("+".mbMinutesRelative("00:00:00", $interv->temp_operation)." MINUTES", $interv->entree_salle);
$time_debut_op = getTS("$date $time_min");

$yaxes = array(
  array("used" => false, "position" => "left", "labelWidth" => 20, "color" => "red",   "symbol" => "circle",  "symbolChar" => "&#x25CB;"),
  array("used" => false, "position" => "left", "labelWidth" => 20, "color" => "green", "symbol" => "cross",   "symbolChar" => "x"),
  array("used" => false, "position" => "left", "labelWidth" => 20, "color" => "blue",  "symbol" => "diamond", "symbolChar" => "&#x25C7;"),
  array("used" => false, "position" => "left", "labelWidth" => 20, "color" => "purle", "symbol" => "square",  "symbolChar" => "m"),
);

foreach($result_sets as $_set) {
  $_time = getTS($_set->datetime);
  
  $times[] = $_time;
  $_time_iso = mbTime($_set->datetime);
  $time_min = min($_time_iso, $time_min);
  $time_max = max($_time_iso, $time_max);
  
  $_results = $_set->loadRefsResults();
  
  foreach($_results as $_result) {
    $_value_type_id = $_result->value_type_id;
    $_value_unit_id = $_result->unit_id;
    $_key = "$_value_type_id-$_value_unit_id";
    
    if (!isset($data[$_key])) {
      $_result->loadRefValueType();
      $_result->loadRefValueUnit();
      
      $yaxis_i = count($data);
      $yaxis = &$yaxes[$yaxis_i];
      
      $unit = $_result->_ref_value_unit->label;
      $label = $_result->_ref_value_type->label." ($unit)";
      $yaxis["used"]  = true;
      $yaxis["label"] = $label;
      $yaxis["unit"]  = $unit;
      
      $data[$_key] = array(
        "yaxis" => $yaxis_i+1,
        "label" => utf8_encode($label),
        "color" => $yaxis["color"],
        "unit"  => utf8_encode($unit),
        "data"  => array(),
        "points" => array("symbol" => $yaxis["symbol"], "lineWidth" => 1),
      );
    }
    
    $data[$_key]["data"][] = array($_time, $_result->value);
  }
}

$round_minutes = 10;
$round = $round_minutes * 60000;

$time_min = getTS("$date $time_min");
$time_max = getTS("$date $time_max");

$time_min = floor($time_min / $round) * $round;
$time_max = ceil($time_max / $round) * $round;

$xaxes = array(
  array("used" => true, "mode" => "time", "min" => $time_min, "max" => $time_max),
);

$interv->loadRefsAnesthPerops();

$gestes = array(
  "CAnesthPerop" => array(),
);

foreach($interv->_ref_anesth_perops as $_perop) {
  $_ts = getTS($_perop->datetime);
  
  $gestes["CAnesthPerop"][$_perop->_id] = array(
    "label" => $_perop->libelle,
    "alert" => $_perop->incident,
    "position" => 100 * ($_ts - $time_min) / ($time_max - $time_min),
  );
}

CJSLoader::$files = array(
  "lib/flot/jquery.min.js",
  "lib/flot/jquery.flot.min.js",
  "lib/flot/jquery.flot.symbol.min.js",
  "lib/flot/jquery.flot.crosshair.min.js",
  "lib/flot/jquery.flot.resize.min.js",
);
echo CJSLoader::loadFiles();
CAppUI::JS('$.noConflict()');

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("interv",      $interv);
$smarty->assign("result_sets", $result_sets);
$smarty->assign("yaxes",       $yaxes);
$smarty->assign("xaxes",       $xaxes);
$smarty->assign("gestes",      $gestes);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("data",        array_values($data));

$smarty->display("vw_surveillance_peranesth.tpl");
