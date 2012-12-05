<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * A supervision timed data representation
 */
class CSupervisionTimedData extends CSupervisionTimedEntity {
  var $supervision_timed_data_id;

  var $period;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_timed_data";
    $spec->key   = "supervision_timed_data_id";
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["period"] = "enum notNull list|1|5|10|15|20|30|60";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["pack_links"] = "CSupervisionGraphToPack graph_id";
    return $backProps;
  }

  /**
   * @param CMbObject $object
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
