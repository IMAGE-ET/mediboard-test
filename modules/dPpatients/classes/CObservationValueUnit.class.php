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
 * Observation value unit, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationValueUnit extends CObservationValueCodingSystem {
  public $observation_value_unit_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "observation_value_unit";
    $spec->key   = "observation_value_unit_id";
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult unit_id";
    $backProps["supervison_graph_series"] = "CSupervisionGraphSeries value_unit_id";
    return $backProps;
  }

  /**
   * Get a unit by its ID
   *
   * @param int $unit_id The unit ID
   *
   * @return CObservationValueUnit
   */
  static function get($unit_id) {
    static $cache = array();
    
    if (isset($cache[$unit_id])) {
      return $cache[$unit_id];
    }
    
    $unit = new self;
    $unit->load($unit_id);
    
    return $cache[$unit_id] = $unit;
  }
}