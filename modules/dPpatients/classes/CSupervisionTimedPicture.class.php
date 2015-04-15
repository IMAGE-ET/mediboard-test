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
class CSupervisionTimedPicture extends CSupervisionTimedEntity {
  const PICTURES_ROOT = "modules/dPpatients/images/supervision";

  public $supervision_timed_picture_id;
  public $in_doc_template;

  public $value_type_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_timed_picture";
    $spec->key   = "supervision_timed_picture_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["value_type_id"]   = "ref notNull class|CObservationValueType autocomplete|label dependsOn|datatype";
    $props["in_doc_template"] = "bool notNull default|0";
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

  function loadTimedPictures($results, $time_min, $time_max) {
    $type_id = $this->value_type_id;
    if (!isset($results[$type_id]["none"])) {
      return $this->_graph_data = array();
    }

    $data = $results[$type_id]["none"];

    foreach ($data as $_i => $_d) {
      $data[$_i]["position"] = $this->getPosition($_d["datetime"], $time_min, $time_max);

      if ($_d["file_id"]) {
        $file = new CFile();
        $file->load($_d["file_id"]);
        $data[$_i]["file"] = $file;
      }
    }

    return $this->_graph_data = $data;
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
