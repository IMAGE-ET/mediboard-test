<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Observation value unit, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationValueUnit extends CObservationValueCodingSystem {
  var $observation_value_unit_id = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_value_unit";
    $spec->key   = "observation_value_unit_id";
    return $spec;
  }
}