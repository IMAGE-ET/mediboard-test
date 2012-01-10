<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Observation result set, based on the HL7 OBR message
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBR.html
 */
class CObservationResultSet extends CMbObject {
  var $observation_result_set_id = null;
  
  var $patient_id            = null;
  var $datetime              = null;
  var $context_class         = null;
  var $context_id            = null;
  
  var $_ref_context          = null;
  var $_ref_patient          = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_result_set";
    $spec->key   = "observation_result_set_id";
    $spec->measureable = true;
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["patient_id"]    = "ref notNull class|CPatient";
    $props["datetime"]      = "dateTime notNull";
    $props["context_class"] = "str notNull";
    $props["context_id"]    = "ref class|CMbObject meta|context_id";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult observation_result_set_id";
    return $backProps;
  }
  
  /**
   * @return CMbObject
   */
  function loadRefContext($cache = true) {
    return $this->_ref_context = $this->loadFwdRef("context_id", $cache);
  }
  
  /**
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    return $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }
}
