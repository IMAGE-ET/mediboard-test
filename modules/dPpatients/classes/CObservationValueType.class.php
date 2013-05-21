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
 * Observation value type, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationValueType extends CObservationValueCodingSystem {
  public $observation_value_type_id;
  
  public $datatype;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_value_type";
    $spec->key   = "observation_value_type_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    // AD|CF|CK|CN|CP|CWE|CX|DT|DTM|ED|FT|MO|NM|PN|RP|SN|ST|TM|TN|TX|XAD|XCN|XON|XPN|XTN
    $props["datatype"] = "enum notNull list|NM|ST|TX";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult value_type_id";
    $backProps["supervison_graph_series"] = "CSupervisionGraphSeries value_type_id";
    return $backProps;
  }
}
