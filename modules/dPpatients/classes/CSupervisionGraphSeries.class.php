<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * A supervision graph Y axis
 */
class CSupervisionGraphSeries extends CMbObject {
  var $supervision_graph_series_id = null;
  
  var $supervision_graph_axis_id   = null;
  var $title                       = null;
  var $value_type_id               = null;
  var $value_unit_id               = null;
  var $color                       = null;
  var $integer_values              = null;

  /**
   * @var CObservationValueType
   */
  var $_ref_value_type             = null;

  /**
   * @var CObservationValueUnit
   */
  var $_ref_value_unit             = null;

  /**
   * @var CSupervisionGraphAxis
   */
  var $_ref_axis                   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_series";
    $spec->key   = "supervision_graph_series_id";
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["supervision_graph_axis_id"] = "ref notNull class|CSupervisionGraphAxis cascade";
    $props["title"]                = "str";
    $props["value_type_id"]        = "ref notNull class|CObservationValueType autocomplete|label|true";
    $props["value_unit_id"]        = "ref notNull class|CObservationValueUnit autocomplete|label|true";
    $props["color"]                = "str notNull length|6";
    $props["integer_values"]       = "bool notNull default|0";
    return $props;
  }
  
  function initSeriesData($yaxes_count){
    $axis = $this->loadRefAxis();
    $unit = $this->loadRefValueUnit()->label;
    
    $series_data = array(
      "data"       => array(array(0, null)),
      "yaxis"      => $yaxes_count,
      "label"      => utf8_encode($this->_view." ($unit)"),
      "unit"       => utf8_encode($unit),
      "color"      => "#$this->color",
      "shadowSize" => 0,
    );
    
    $series_data["points"] = array("show" => false);
    $series_data[$axis->display] = array("show" => true);

    if ($axis->display == "stack") {
      $series_data["bars"] = array(
        "show"      => true,
        "barWidth"  => 60*1000*30, // FIXME
        "lineWidth" => 0.5,
      );
      $series_data["stack"] = true; // It replaces the "stack" array with a boolean !!
    }

    if ($axis->display == "bandwidth") {
      $series_data["bandwidth"]["lineWidth"] = 10;
    }
    
    if ($axis->show_points || $axis->display == "points") {
      $series_data["points"] = array(
        "show"      => true,
        "symbol"    => $axis->symbol, 
        "lineWidth" => 1,
      );
    }
    
    return $series_data;
  }

  /**
   * @param bool $cache
   *
   * @return CSupervisionGraphAxis
   */
  function loadRefAxis($cache = true) {
    return $this->_ref_axis = $this->loadFwdRef("supervision_graph_axis_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CObservationValueType
   */
  function loadRefValueType($cache = true) {
    return $this->_ref_value_type = $this->loadFwdRef("value_type_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CObservationValueUnit
   */
  function loadRefValueUnit($cache = true) {
    return $this->_ref_value_unit = $this->loadFwdRef("value_unit_id", $cache);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $title = $this->title;
    
    if (!$title) {
      $title = $this->loadRefValueType()->label;
    }
    
    $this->_view = $title;
  }
  
  function getSampleData($times) {
    $axis  = $this->loadRefAxis();
    
    $low   = $axis->limit_low  != null ? $axis->limit_low  : 0;
    $high  = $axis->limit_high != null ? $axis->limit_high : 100;

    if ($axis->display == "stack") {
      $low /= 2;
      $high /= 2;
    }
    
    $diff  = $high - $low;
    $value = rand($low+$diff/4, $high-$diff/4);
    
    $data = array();
    foreach ($times as $_time) {
      $v = round($value, $this->integer_values ? 0 : 2);
      $data[] = array($_time, $v);
      $value += rand(-$diff, +$diff) / 10;
    }
    
    return $data;
  }
}
