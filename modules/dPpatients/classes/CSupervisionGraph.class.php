<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * A supervision graph
 */
class CSupervisionGraph extends CSupervisionTimedEntity {
  var $supervision_graph_id;

  var $height;

  /**
   * @var CSupervisionGraphAxis[]
   */
  var $_ref_axes;

  var $_graph_data = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph";
    $spec->key   = "supervision_graph_id";
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["height"] = "num notNull min|20 default|200";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["axes"] = "CSupervisionGraphAxis supervision_graph_id";
    $backProps["pack_links"] = "CSupervisionGraphToPack graph_id";
    return $backProps;
  }

  /**
   * @return CSupervisionGraphAxis[]
   */
  function loadRefsAxes(){
    return $this->_ref_axes = $this->loadBackRefs("axes");
  }

  /**
   * @param array $results
   *
   * @return float
   */
  static protected function _getMin($results) {
    return min(CMbArray::pluck($results, 1));
  }

  /**
   * @param array $results
   *
   * @return float
   */
  static protected function _getMax($results) {
    return max(CMbArray::pluck($results, 1));
  }

  /**
   * @param array  $results
   * @param string $time_min
   * @param string $time_max
   *
   * @return array
   */
  function buildGraph($results, $time_min, $time_max) {
    $graph = array(
      "yaxes"  => array(),
      "xaxes"  => array(array(
        "mode"     => "time",
        "position" => "bottom", 
        "min"      => $time_min, 
        "max"      => $time_max,
        "ticks"    => 24, // FIXME
      )),
      "series" => array(),
      "title"  => $this->title,
    );
    
    $_axes = $this->loadRefsAxes();

    $yaxis_i = 1;
    foreach ($_axes as $_axis) {
      $graph_yaxis = $_axis->getAxisForFlot(count($graph["yaxes"]));
    
      $_series = $_axis->loadRefsSeries();

      if ($_axis->display == "bandwidth") {
        // first series is the base
        $first_data = null;

        foreach ($_series as $_serie) {
          $_series_data = $_serie->initSeriesData($yaxis_i);

          if (!isset($results[$_serie->value_type_id][$_serie->value_unit_id])) {
            continue;
          }

          if (!$first_data) {
            $first_data = $_series_data;
            $first_data["label"] = $_axis->title;
            $first_data["data"] = $results[$_serie->value_type_id][$_serie->value_unit_id];

            if ($graph_yaxis["min"] !== null) {
              $graph_yaxis["min"] = min($graph_yaxis["min"], self::_getMin($first_data["data"]));
            }

            if ($graph_yaxis["max"] !== null) {
              $graph_yaxis["max"] = max($graph_yaxis["max"], self::_getMax($first_data["data"]));
            }
          }
          else {
            $new_data = $results[$_serie->value_type_id][$_serie->value_unit_id];
            foreach ($new_data as $_i => $_d) {
              $first_data["data"][$_i][] = $_d[1];
            }

            if ($graph_yaxis["min"] !== null) {
              $graph_yaxis["min"] = min($graph_yaxis["min"], self::_getMin($new_data));
            }

            if ($graph_yaxis["max"] !== null) {
              $graph_yaxis["max"] = max($graph_yaxis["max"], self::_getMax($new_data));
            }
          }
        }

        $graph["series"][] = $first_data;
      }
      else {
        foreach ($_series as $_serie) {
          $_series_data = $_serie->initSeriesData($yaxis_i);

          if (!isset($results[$_serie->value_type_id][$_serie->value_unit_id])) {
            continue;
          }

          $_series_data["data"] = $results[$_serie->value_type_id][$_serie->value_unit_id];

          if ($graph_yaxis["min"] !== null) {
            $graph_yaxis["min"] = min($graph_yaxis["min"], self::_getMin($_series_data["data"]));
          }

          if ($graph_yaxis["max"] !== null) {
            $graph_yaxis["max"] = max($graph_yaxis["max"], self::_getMax($_series_data["data"]));
          }

          $graph["series"][] = $_series_data;
        }
      }

      $graph["yaxes"][] = $graph_yaxis;

      $yaxis_i++;
    }
    
    return $this->_graph_data = $graph;
  }

  /**
   * @param CMbObject $object
   *
   * @return self[]
   */
  static function getAllFor(CMbObject $object) {
    $graph = new self;

    $where = array(
      "owner_class" => "= '$object->_class'",
      "owner_id"    => "= '$object->_id'",
    );

    return $graph->loadList($where, "title");
  }

  static function includeFlot(){
    CJSLoader::$files = array(
      "lib/flot/jquery.min.js",
      "lib/flot/jquery.flot.min.js",
      "lib/flot/jquery.flot.symbol.min.js",
      "lib/flot/jquery.flot.crosshair.min.js",
      "lib/flot/jquery.flot.resize.min.js",
      "lib/flot/jquery.flot.stack.min.js",
      "lib/flot/jquery.flot.bandwidth.js",
      "modules/dPpatients/javascript/supervision_graph.js",
    );
    echo CJSLoader::loadFiles();
    CAppUI::js('$.noConflict()');
  }
}
