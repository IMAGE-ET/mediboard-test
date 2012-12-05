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
  var $observation_result_id;
  
  var $observation_result_set_id;
  var $value_type_id; // OBX.3
  var $unit_id;       // OBX.6
  var $value;         // OBX.2
  var $method;        // OBX.17
  var $status;        // OBX.11
  
  var $_ref_context;
  var $_ref_value_type;
  var $_ref_value_unit;
  var $_ref_result_set;
  
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
   * @param bool $cache
   *
   * @return CObservationResultSet
   */
  function loadRefResultSet($cache = true) {
    return $this->_ref_result_set = $this->loadFwdRef("observation_result_set_id", $cache);
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
   * @return CObservationValueUnit
   */
  function loadRefValueUnit() {
    return $this->_ref_value_unit = CObservationValueUnit::get($this->unit_id);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->value." ".CObservationValueUnit::get($this->unit_id)->_view;
  }
}
