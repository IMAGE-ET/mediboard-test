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
class CSupervisionGraph extends CMbObject {
  var $supervision_graph_id = null;
  
  var $owner_class          = null;
  var $owner_id             = null;
  
  var $title                = null;
  var $disabled             = null;
  
  var $_ref_owner           = null;
  var $_ref_axes            = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph";
    $spec->key   = "supervision_graph_id";
    $spec->uniques["title"] = array("owner_class", "owner_id", "title");
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["owner_class"] = "enum notNull list|CGroups";
    $props["owner_id"]    = "ref notNull meta|owner_class class|CMbObject";
    $props["title"]       = "str notNull";
    $props["disabled"]    = "bool notNull default|1";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["axes"] = "CSupervisionGraphAxis supervision_graph_id";
    return $backProps;
  }
  
  /**
   * @return CMbObject
   */
  function loadRefOwner($cache = true) {
    return $this->_ref_owner = $this->loadFwdRef("owner_id", $cache);
  }
  
  function loadRefsAxes(){
    return $this->_ref_axes = $this->loadBackRefs("axes");
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->title;
  }
  
  static protected function _getMin($results) {
    return min(CMbArray::pluck($results, 1));
  }
  
  static protected function _getMax($results) {
    return max(CMbArray::pluck($results, 1));
  }
  
  function buildGraph($results, $time_min, $time_max) {
    $graph = array(
      "yaxes"  => array(),
      "xaxes"  => array(array(
        "mode"     => "time",
        "position" => "bottom", 
        "min"      => $time_min, 
        "max"      => $time_max,
      )),
      "series" => array(),
      "title"  => $this->title,
    );
    
    $_axes = $this->loadRefsAxes();
    
    foreach(array_values($_axes) as $yaxis_i => $_axis) {
      $graph_yaxis = $_axis->getAxisForFlot(count($graph["yaxes"]));
    
      $_series = $_axis->loadRefsSeries();
      
      foreach($_series as $_serie) {
        $_series_data = $_serie->initSeriesData($yaxis_i+1);
        
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
      
      $graph["yaxes"][] = $graph_yaxis;
    }
    
    return $graph;
  }
}
