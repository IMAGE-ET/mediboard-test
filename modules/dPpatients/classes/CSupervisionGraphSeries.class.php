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
  
  var $_ref_value_type             = null;
  var $_ref_value_unit             = null;
  
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
    $props["value_type_id"]        = "ref notNull class|CObservationValueType autocomplete|label";
    $props["value_unit_id"]        = "ref notNull class|CObservationValueUnit autocomplete|label";
    $props["color"]                = "str notNull length|6";
    return $props;
  }
  
  /**
   * @return CSupervisionGraphAxis
   */
  function loadRefAxis($cache = true) {
    return $this->_ref_axis = $this->loadFwdRef("supervision_graph_axis_id", $cache);
  }
  
  /**
   * @return CObservationValueType
   */
  function loadRefValueType($cache = true) {
    return $this->_ref_value_type = $this->loadFwdRef("value_type_id", $cache);
  }
  
  /**
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
}
