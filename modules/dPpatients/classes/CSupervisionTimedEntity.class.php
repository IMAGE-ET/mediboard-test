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
 * A supervision graph
 */
class CSupervisionTimedEntity extends CMbObject {
  public $owner_class;
  public $owner_id;

  public $title;
  public $disabled;

  /** @var CMbObject */
  public $_ref_owner;

  /** @var array */
  public $_graph_data = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->uniques["title"] = array("owner_class", "owner_id", "title");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["owner_class"] = "enum notNull list|CGroups";
    $props["owner_id"]    = "ref notNull meta|owner_class class|CMbObject";
    $props["title"]       = "str notNull";
    $props["disabled"]    = "bool notNull default|1";
    return $props;
  }

  /**
   * Load the owner entity
   *
   * @param bool $cache Use object cache
   *
   * @return CGroups
   */
  function loadRefOwner($cache = true) {
    return $this->_ref_owner = $this->loadFwdRef("owner_id", $cache);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = $this->title;
  }

  static function getPosition($datetime, $time_min, $time_max){
    return 100 * (CMbDate::toUTCTimestamp($datetime) - $time_min) / ($time_max - $time_min);
  }

  static function getWidth($datetime_start, $datetime_end, $time_min, $time_max){
    $delta = strtotime($datetime_end) - strtotime($datetime_start);
    return 100 * ($delta*1000) / ($time_max - $time_min);
  }
}
