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
$time_fin_op   = getTS("$date $time_max");

$default_yaxis = array("position" => "left", "labelWidth" => 20, "ticks" => 6, "reserveSpace" => true);
$graph_object = new CSupervisionGraph;
$graph_objects = $graph_object->loadList();

$graphs = array(); // will contain all the data

foreach($result_sets as $_set) {
  $_set->loadRefsResults();
  $times[] = getTS($_set->datetime);
}

foreach($graph_objects as $_go) {
  $_curr_graph = array(
    "yaxes"  => array(),
  );
  
  $_axes = $_go->loadRefsAxes();
  
  foreach(array_values($_axes) as $yaxis_i => $_axis) {
    /// AXIS DATA
    $_axis_data = $default_yaxis + array(
      "symbolChar" => $_axis->getSymbolChar(),
      "label"      => $_axis->title,
    );
    
    if (count($_curr_graph["yaxes"])) {
      $_axis_data["alignTicksWithAxis"] = 1;
    }
    
    if ($_axis->limit_low != null) {
      $_axis_data["min"] = floatval($_axis->limit_low);
    }
    
    if ($_axis->limit_high != null) {
      $_axis_data["max"] = floatval($_axis->limit_high);
    }
    
    $_curr_graph["yaxes"][] = $_axis_data;
    /// END AXIS DATA
  
    $_series = $_axis->loadRefsSeries();
    
    // TODO OPTIMISER!!!!!!!!!
    foreach($_series as $_serie) {
      $_series_data = array(
        "data"  => array(array(0, null)),
        "yaxis" => $yaxis_i+1,
        "label" => utf8_encode($_serie->title),
        "unit"  => utf8_encode($_serie->loadRefValueUnit()->label),
        "color" => "#$_serie->color",
        "shadowSize" => 0,
      );
      
      $_series_data["points"] = array("show" => false);
      $_series_data[$_axis->display] = array("show" => true);
      
      if ($_axis->show_points || $_axis->display == "points") {
        $_series_data["points"] = array("show" => true, "symbol" => $_axis->symbol, "lineWidth" => 1);
      }
      
      foreach($result_sets as $_set) {
        foreach($_set->_ref_results as $_result) {
          $_value_type_id = $_result->value_type_id;
          $_value_unit_id = $_result->unit_id;
          
          if ($_value_type_id != $_serie->value_type_id || 
              $_value_unit_id != $_serie->value_unit_id) {
            continue;
          }
          
          $_series_data["data"][] = array(getTS($_set->datetime), floatval($_result->value));
        }
      }

      $_curr_graph["series"][] = $_series_data;
    }
  }

  $graphs[] = $_curr_graph;
}

/*
mbTrace($graphs);

return;

$yaxes = array(
  $default_yaxis + array("color_" => "red",        "symbol" => "circle",   "symbolChar" => "&#x25CB;"),
  $default_yaxis + array("color_" => "green",      "symbol" => "cross",    "symbolChar" => "&#x2A2F;", "alignTicksWithAxis" => 1),
  $default_yaxis + array("color_" => "blue",       "symbol" => "diamond",  "symbolChar" => "&#x25C7;", "alignTicksWithAxis" => 1),
  $default_yaxis + array("color_" => "purple",     "symbol" => "square",   "symbolChar" => "&#x25A1;", "alignTicksWithAxis" => 1),
  $default_yaxis + array("color_" => "orange",     "symbol" => "triangle", "symbolChar" => "&#x25B3;", "alignTicksWithAxis" => 1),
  $default_yaxis + array("color_" => "black",      "symbol" => "circle",   "symbolChar" => "&#x25CB;", "alignTicksWithAxis" => 1),
  $default_yaxis + array("color_" => "lightblue",  "symbol" => "circle",   "symbolChar" => "&#x25CB;", "alignTicksWithAxis" => 1),
  $default_yaxis + array("color_" => "lightgreen", "symbol" => "circle",   "symbolChar" => "&#x25CB;", "alignTicksWithAxis" => 1),
);

foreach($result_sets as $_set) {
  $_time = getTS($_set->datetime);
  
  $times[] = $_time;
  $_time_iso = mbTime($_set->datetime);
  //$time_min = min($_time_iso, $time_min);
  //$time_max = max($_time_iso, $time_max);
  
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
        "color" => $yaxis["color_"],
        "unit"  => utf8_encode($unit),
        "data"  => array(),
        "points" => array("symbol" => $yaxis["symbol"], "lineWidth" => 1),
        "shadowSize" => 0,
      );
    }
    
    $data[$_key]["data"][] = array($_time, $_result->value);
  }
}*/

$round_minutes = 10;
$round = $round_minutes * 60000;

$time_min = getTS("$date $time_min");
$time_max = getTS("$date $time_max");

$time_min = floor($time_min / $round) * $round;
$time_max = ceil($time_max / $round) * $round;

$xaxes = array(
  array(
    "used" => true, 
    "mode" => "time",
    "position" => "top", 
    "min" => $time_min, 
    "max" => $time_max,
  ),
);

// Gestes, Medicaments, Perfusions peranesth
$gestes = array(
  "CAnesthPerop" => array(),
);

$interv->loadRefsAnesthPerops();

foreach($interv->_ref_anesth_perops as $_perop) {
  $_ts = getTS($_perop->datetime);
  
  $gestes["CAnesthPerop"][$_perop->_id] = array(
    "icon" => null,
    "label" => $_perop->libelle,
    "unit"  => null,
    "alert" => $_perop->incident,
    "datetime" => $_perop->datetime,
    "position" => 100 * ($_ts - $time_min) / ($time_max - $time_min),
    "object" => $_perop,
  );
}

$sejour = $interv->loadRefSejour();

$prescription = $sejour->loadRefPrescriptionSejour();

if($prescription->_id){
  $lines = $prescription->loadPeropLines(false);
  
  foreach($lines as $_guid => $_line_array) {
    $_line = $_line_array["object"];
    
    $_view = "";
    
    if ($_line instanceof CPrescriptionLineElement) {
      $_view = $_line->_view;
    }
    elseif ($_line instanceof CPrescriptionLineMix) {
      foreach ($_line->_ref_lines as $_mix_item) {
        $_view .= "$_mix_item->_ucd_view<br />";
      }
    }
    else {
      $_view = $_line->_ucd_view;
    }
    
    if (!isset($gestes[$_line->_class])) {
      $gestes[$_line->_class] = array();
    }
    
    /*
    foreach($_line_array["planifications"] as $_planifs) {
      foreach($_planifs as $_planif) {
        if ($_planif->_ref_object instanceof CPrescriptionLineMixItem) {
          $quantite = $_planif->_ref_object->_quantite_administration;
        }
        else {
          $quantite = $_planif->_ref_prise->_quantite_administrable;
        }
        
        if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
          $unite = $_planif->_ref_object->_ref_produit->libelle_unite_presentation;
        }
        else {
          $unite = $_line->_unite_prise;
        }

        $gestes[$_line->_class][] = array(
          "label" => "$quantite $unite",
          "alert" => false,
          "datetime" => $_planif->dateTime,
          "position" => 100 * (getTS($_planif->dateTime) - $time_min) / ($time_max - $time_min),
        );
      }
    }*/
   
    foreach ($_line_array["administrations"] as $_adms) {
      $_adms = CStoredObject::naturalSort($_adms, array("dateTime"));
      
      foreach ($_adms as $_adm) {
        $unite = "";
        if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
          $unite = $_adm->_ref_object->_ref_produit->libelle_unite_presentation;
        }
        
        $gestes[$_line->_class][] = array(
          "icon"  => $_line->_chapitre,
          "label" => $_view,
          "unit"  => "$_adm->quantite $unite",
          "alert" => false,
          "datetime" => $_adm->dateTime,
          "position" => 100 * (getTS($_adm->dateTime) - $time_min) / ($time_max - $time_min),
          "object"   => $_line,
        );
      }
    }
  }
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
$smarty->assign("graphs",      $graphs);
$smarty->assign("xaxes",       $xaxes);
$smarty->assign("gestes",      $gestes);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("time_fin_op",   $time_fin_op);

$smarty->display("vw_surveillance_peranesth.tpl");
