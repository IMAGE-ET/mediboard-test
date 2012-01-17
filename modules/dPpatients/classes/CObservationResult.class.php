<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Observation value, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationResult extends CMbObject {
  var $observation_result_id = null;
  
  var $observation_result_set_id = null;
  var $value_type_id         = null; // OBX.3
  var $unit_id               = null; // OBX.6
  var $value                 = null; // OBX.2
  var $method                = null; // OBX.17
  var $status                = null; // OBX.11
  
  var $_ref_context          = null;
  var $_ref_value_type       = null;
  var $_ref_value_unit       = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_result";
    $spec->key   = "observation_result_id";
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["observation_result_set_id"] = "ref notNull class|CObservationResultSet";
    $props["value_type_id"]             = "ref notNull class|CObservationValueType";
    $props["unit_id"]                   = "ref notNull class|CObservationValueUnit";
    $props["value"]                     = "str notNull";
    $props["method"]                    = "str";
    $props["status"]                    = "enum list|C|D|F|I|N|O|P|R|S|U|W|X default|F";
    return $props;
  }
  
  /**
   * @return CObservationRequest
   */
  function loadRefResultSet($cache = true) {
    return $this->_ref_result_set = $this->loadFwdRef("observation_result_set_id", $cache);
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
    return $this->_ref_value_unit = CObservationValueUnit::get($this->unit_id);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->value." ".CObservationValueUnit::get($this->unit_id)->_view;
  }
}
