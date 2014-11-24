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
 * A supervision instant data representation
 */
class CSupervisionInstantData extends CSupervisionTimedEntity {
  public $supervision_instant_data_id;

  public $value_type_id;
  public $value_unit_id;
  public $size;
  public $color;

  public $_ref_value_type;
  public $_ref_value_unit;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_instant_data";
    $spec->key   = "supervision_instant_data_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["value_type_id"] = "ref notNull class|CObservationValueType autocomplete|label dependsOn|coding_system";
    $props["value_unit_id"] = "ref notNull class|CObservationValueUnit autocomplete|label dependsOn|coding_system";
    $props["size"]          = "num notNull min|10 max|60";
    $props["color"]         = "color";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["pack_links"] = "CSupervisionGraphToPack graph_id";
    return $backProps;
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
    return $this->_ref_value_unit = CObservationValueUnit::get($this->value_unit_id);
  }

  /**
   * Get all the instant data for an object
   *
   * @param CMbObject $object The object to get timed data of
   *
   * @return self[]
   */
  static function getAllFor(CMbObject $object) {
    $graph = new self;

    $where = array(
      "owner_class" => "= '$object->_class'",
      "owner_id"    => "= '$object->_id'",
    );

    return $graph->loadList($where, "title");
  }
}
