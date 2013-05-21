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
 * A supervision timed data representation
 */
class CSupervisionTimedData extends CSupervisionTimedEntity {
  public $supervision_timed_data_id;

  public $period;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_timed_data";
    $spec->key   = "supervision_timed_data_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["period"] = "enum notNull list|1|5|10|15|20|30|60";
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
   * Get all the timed data for an object
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
