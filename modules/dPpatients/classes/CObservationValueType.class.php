<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Observation value type, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationValueType extends CObservationValueCodingSystem {
  var $observation_value_type_id = null;
  
  var $datatype                  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_value_type";
    $spec->key   = "observation_value_type_id";
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    // AD|CF|CK|CN|CP|CWE|CX|DT|DTM|ED|FT|MO|NM|PN|RP|SN|ST|TM|TN|TX|XAD|XCN|XON|XPN|XTN
    $props["datatype"] = "enum notNull list|NM|ST|TX";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult value_type_id";
    $backProps["supervison_graph_series"] = "CSupervisionGraphSeries value_type_id";
    return $backProps;
  }
}
