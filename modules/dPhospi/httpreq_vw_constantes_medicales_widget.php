<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$context_guid = CValue::get('context_guid');
$context = CStoredObject::loadFromGuid($context_guid);

$date_min = CMbDT::dateTime("-6 DAYS", CMbDT::date());
$date_max = CMbDT::dateTime("+1 DAY", CMbDT::date());

$options = array(
  "shadowSize" => 0,
  "lines" => array("show" => true, "lineWidth" => 1),
  "points" => array("show" => true, "lineWidth" => 1, "radius" => 2.5),
  "candles" => array(
    "candleWidth" => 0.5,
    "upFillColor" => '#C0D800',
    "downFillColor" => '#C0D800',
    "lineWidth" => 1,
    "wickLineWidth" => 3,
  ),
  //"markers" => array("show" => true),
  "mouse" => array("track" => true, "position" => "nw", "relative" => true),
  "grid" => array("outlineWidth" => 1),
  "xaxis" => array("mode" => "time"),
  "yaxis" => array("min" => PHP_INT_MAX, "max" => -PHP_INT_MAX),
  "legend" => array("show" => true, "labelBoxWidth" => 10, "labelBoxHeight" => 5, "labelBoxMargin" => 2, "labelBoxBorderColor" => "transparent"),
);

// Displays only constants with rank 1
$ranks = CConstantesMedicales::getRanksFor('graph', CConstantesMedicales::guessHost($context));

$list_cste   = array();
$list_drains = array();
$list_redons = array();

foreach ($ranks as $_cste => $rank) {
  if ($rank != 1) {
    continue;
  }
  if (strpos($_cste, 'drain') !== false) {
    $list_drains[] = $_cste;
    continue;
  }
  if (strpos($_cste, 'redon') !== false) {
    $list_redons[] = $_cste;
    continue;
  }

  $list_cste[] = $_cste;
}

// Global structure
$graphs_struct = array(
  "Constantes" => $list_cste,
  "Drains"     => $list_drains,
  "Redons"     => $list_redons
);

$yaxis_margin_top = 10;
$yaxis_margin_bottom = 5;

$graphs = array();
foreach ($graphs_struct as $_name => $_fields) {
  if (count($_fields)) {
    $graphs[$_name] = array(
      "series" => array(),
      "options" => $options,
    );
    foreach ($_fields as $_field) {
      $unit = CConstantesMedicales::$list_constantes[$_field]["unit"];

      $graphs[$_name]["series"][] = array(
        "key"   => $_field,
        "label" => utf8_encode(CAppUI::tr("CConstantesMedicales-$_field-court")." ($unit)"),
        "unit"  => utf8_encode($unit),
        "data"  => array(),
      );
    }
  }
}

$constante = new CConstantesMedicales();

foreach ($graphs as $_name => $_fields) {
  $whereOr = array();
  foreach ($_fields["series"] as $_field) {
    $whereOr[] = $_field["key"]." IS NOT NULL";
  }

  $where = array(
    "context_class" => "= '$context->_class'",
    "context_id"    => "= '$context->_id'",
    //"datetime"      => "> '$date_min'",
    implode(" OR ", $whereOr)
  );

  /** @var CConstantesMedicales[] $list */
  $list = $constante->loadList($where, "datetime DESC", 100);

  if (!count($list)) {
    unset($graphs[$_name]);
    continue;
  }
  $ticks = array();
  foreach ($list as $_constante) {
    $ticks[] = array(strtotime($_constante->datetime)*1000, CMbDT::format($_constante->datetime, '%Hh%M') . "<br/>".CMbDT::transform(null, $_constante->datetime, "%d/%m"));
    foreach ($_fields["series"] as $_i => $_field) {
      $_field_name = $_field["key"];
      $_value = $_constante->$_field_name;

      if ($_value == "") {
        continue;
      }

      $point = array(
        strtotime($_constante->datetime) * 1000,
      );

      $formfields = CMbArray::get(CConstantesMedicales::$list_constantes[$_field_name], "formfields");
      if ($formfields) {
        list($first, $second) = $formfields;
        $point[1] = $_constante->$first;
        $point[2] = $_constante->$first;

        $point[3] = $_constante->$second;
        $point[4] = $_constante->$second;

        $graphs[$_name]["series"][$_i]["candles"]["show"] = true;
        $graphs[$_name]["series"][$_i]["points"]["show"]  = false;
        $graphs[$_name]["series"][$_i]["lines"]["show"]   = false;
      }
      else {
        $point[1] = $_value;
      }

      if ($point[1] > $graphs[$_name]["options"]["yaxis"]["max"]) {
        $graphs[$_name]["options"]["yaxis"]["max"] = $point[1]+$yaxis_margin_top;
      }

      if ($point[1] < $graphs[$_name]["options"]["yaxis"]["min"]) {
        $graphs[$_name]["options"]["yaxis"]["min"] = $point[1]-$yaxis_margin_bottom;
      }

      if (isset($point[3])) {
        if ($point[3] > $graphs[$_name]["options"]["yaxis"]["max"]) {
          $graphs[$_name]["options"]["yaxis"]["max"] = $point[3]+$yaxis_margin_top;
        }

        if ($point[3] < $graphs[$_name]["options"]["yaxis"]["min"]) {
          $graphs[$_name]["options"]["yaxis"]["min"] = $point[3]-$yaxis_margin_bottom;
        }
      }

      $graphs[$_name]["series"][$_i]["data"][] = $point;
    }
  }

  if (count($ticks)) {
    $graphs[$_name]["options"]["xaxis"]["ticks"] = $ticks;
    $graphs[$_name]["options"]["xaxis"]["max"] = $ticks[0][0];
    $min = end($ticks);
    $graphs[$_name]["options"]["xaxis"]["min"] = $min[0];
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->assign("date_min", $date_min);
$smarty->display('inc_vw_constantes_medicales_widget.tpl');
