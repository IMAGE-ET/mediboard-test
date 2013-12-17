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

$ticks = array();
$date = $date_min;
while ($date <= $date_max) {
  $ts = strtotime($date)*1000;
  $ticks[] = array($ts, CMbDT::transform(null, $date, "%d/%m"));
  $date = CMbDT::dateTime("+1 DAY", $date);
}

$date_min_ts = strtotime($date_min) * 1000;
$date_max_ts = strtotime($date_max) * 1000;

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
  "xaxis" => array("mode" => "time", "min" => $date_min_ts, "max" => $date_max_ts, "ticks" => $ticks),
  "yaxis" => array("min" => PHP_INT_MAX, "max" => -PHP_INT_MAX),
  "legend" => array("show" => true, "labelBoxWidth" => 10, "labelBoxHeight" => 5, "labelBoxMargin" => 2, "labelBoxBorderColor" => "transparent"),
);

// Global structure
$graphs_struct = array(
  "Constantes" => array(
    "temperature", "ta", "pouls",
  ),
  "Drains" => array(
    "drain_1", "drain_2", "drain_3",
  ),
  "Redons" => array(
    "redon", "redon_2", "redon_3", "redon_4", "redon_5", "redon_6", "redon_7", "redon_8",
  ),
);

$yaxis_margin_top = 10;
$yaxis_margin_bottom = 5;

$graphs = array();
foreach ($graphs_struct as $_name => $_fields) {
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

  foreach ($list as $_constante) {
    foreach ($_fields["series"] as $_i => $_field) {
      $_field_name = $_field["key"];
      $_value = $_constante->$_field_name;

      if ($_value == "") {
        continue;
      }

      $point = array(
        strtotime($_constante->datetime) * 1000,
      );

      if (isset(CConstantesMedicales::$list_constantes[$_field_name]['candles'])) {
        $formfields = CMbArray::get(CConstantesMedicales::$list_constantes[$_field_name], "formfields");

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
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_min", $date_min);
$smarty->display('inc_vw_constantes_medicales_widget.tpl');
