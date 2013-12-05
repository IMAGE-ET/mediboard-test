<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * A supervision graph
 */
class CSupervisionGraph extends CSupervisionTimedEntity {
  public $supervision_graph_id;

  public $height;

  /** @var CSupervisionGraphAxis[] */
  public $_ref_axes;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph";
    $spec->key   = "supervision_graph_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["height"] = "num notNull min|20 default|200";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["axes"] = "CSupervisionGraphAxis supervision_graph_id";
    $backProps["pack_links"] = "CSupervisionGraphToPack graph_id";
    return $backProps;
  }

  /**
   * Load axes
   *
   * @return CSupervisionGraphAxis[]
   */
  function loadRefsAxes(){
    return $this->_ref_axes = $this->loadBackRefs("axes");
  }

  /**
   * Get minimal value from a results set
   *
   * @param array $results The results as array of two values
   *
   * @return float
   */
  static protected function _getMin($results) {
    return min(CMbArray::pluck($results, 1));
  }

  /**
   * Get maximal value from a results set
   *
   * @param array $results The results as array of two values
   *
   * @return float
   */
  static protected function _getMax($results) {
    return max(CMbArray::pluck($results, 1));
  }

  /**
   * Build the graph data to be used by jQuery Flot
   *
   * @param array  $results  Results
   * @param string $time_min Minimal datetime
   * @param string $time_max Maximal datetime
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
        "ticks"    => 10, // FIXME
      )),
      "series" => array(),
      "title"  => $this->title,
    );
    
    $_axes = $this->loadRefsAxes();

    // Dummy axis
    $graph["yaxes"][] = array(
      "position"     => "left",
      "labelWidth"   => 1,
      "reserveSpace" => true,
      "label" => "",
    );

    $yaxis_i = 2;

    foreach ($_axes as $_axis) {
      $graph_yaxis = $_axis->getAxisForFlot(count($graph["yaxes"]));
    
      $_series = $_axis->loadRefsSeries();

      if ($_axis->display == "bandwidth") {
        // first series is the base
        $first_data = null;

        foreach ($_series as $_serie) {
          $_series_data = $_serie->initSeriesData($yaxis_i);
          $_unit_id = ($_serie->value_unit_id ? $_serie->value_unit_id : "none");

          if (!$graph_yaxis["color"]) {
            $graph_yaxis["color"] = "#$_serie->color";
          }

          if (!isset($results[$_serie->value_type_id][$_unit_id])) {
            continue;
          }

          if (!$first_data) {
            $first_data = $_series_data;
            $first_data['bandwidth'] = array(
              'show' => true,
            );
            $first_data["label"] = $_axis->title;
            $first_data["axis_id"] = $_axis->_id;
            $first_data["data"] = $results[$_serie->value_type_id][$_unit_id];

            if ($graph_yaxis["min"] !== null) {
              $graph_yaxis["min"] = min($graph_yaxis["min"], self::_getMin($first_data["data"]));
            }

            if ($graph_yaxis["max"] !== null) {
              $graph_yaxis["max"] = max($graph_yaxis["max"], self::_getMax($first_data["data"]));
            }
          }
          else {
            $new_data = $results[$_serie->value_type_id][$_unit_id];
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

        if ($first_data) {
          $graph["series"][] = $first_data;
        }
      }
      else {
        foreach ($_series as $_serie) {
          $_series_data = $_serie->initSeriesData($yaxis_i);
          $_unit_id = ($_serie->value_unit_id ? $_serie->value_unit_id : "none");

          if (!$graph_yaxis["color"]) {
            $graph_yaxis["color"] = "#$_serie->color";
          }

          if (!isset($results[$_serie->value_type_id][$_unit_id])) {
            continue;
          }

          $_series_data["data"] = $results[$_serie->value_type_id][$_unit_id];

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
   * Get all the graphs from an object
   *
   * @param CMbObject $object The object to get the graphs from
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
}
