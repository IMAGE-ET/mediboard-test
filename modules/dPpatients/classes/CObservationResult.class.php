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
  var $value_type_id         = null;
  var $unit_id               = null;
  var $value                 = null;
  
  var $_ref_context          = null;
  var $_ref_value_type       = null;
  
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
    return $props;
  }
  
  /**
   * @return CObservationRequest
   */
  function loadRefResultSet($cache = true) {
    return $this->_ref_result_set = $this->loadFwdRef("observation_result_set_id", $cache);
  }
}
