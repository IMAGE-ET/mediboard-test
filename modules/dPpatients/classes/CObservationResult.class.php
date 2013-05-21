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
 * Observation value, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationResult extends CMbObject {
  public $observation_result_id;

  public $observation_result_set_id;
  public $value_type_id; // OBX.3
  public $unit_id;       // OBX.6
  public $value;         // OBX.2
  public $method;        // OBX.17
  public $status;        // OBX.11

  public $_ref_context;

  /** @var CObservationValueType */
  public $_ref_value_type;

  /** @var CObservationValueUnit */
  public $_ref_value_unit;

  /** @var CObservationResultSet */
  public $_ref_result_set;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_result";
    $spec->key   = "observation_result_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
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
   * Load result set
   *
   * @param bool $cache Use object cache
   *
   * @return CObservationResultSet
   */
  function loadRefResultSet($cache = true) {
    return $this->_ref_result_set = $this->loadFwdRef("observation_result_set_id", $cache);
  }

  /**
   * Load value type
   *
   * @param bool $cache Use object cache
   *
   * @return CObservationValueType
   */
  function loadRefValueType($cache = true) {
    return $this->_ref_value_type = $this->loadFwdRef("value_type_id", $cache);
  }

  /**
   * Load value unit
   *
   * @return CObservationValueUnit
   */
  function loadRefValueUnit() {
    return $this->_ref_value_unit = CObservationValueUnit::get($this->unit_id);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->value." ".CObservationValueUnit::get($this->unit_id)->_view;
  }
}
