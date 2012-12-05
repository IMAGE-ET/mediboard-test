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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["observation_results"] = "CObservationResult unit_id";
    $backProps["supervison_graph_series"] = "CSupervisionGraphSeries value_unit_id";
    return $backProps;
  }

  /**
   * @param int $unit_id
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