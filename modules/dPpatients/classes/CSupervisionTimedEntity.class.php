<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * A supervision graph
 */
class CSupervisionTimedEntity extends CMbObject {
  var $owner_class;
  var $owner_id;

  var $title;
  var $disabled;

  /**
   * @var CMbObject
   */
  var $_ref_owner;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->uniques["title"] = array("owner_class", "owner_id", "title");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["owner_class"] = "enum notNull list|CGroups";
    $props["owner_id"]    = "ref notNull meta|owner_class class|CMbObject";
    $props["title"]       = "str notNull";
    $props["disabled"]    = "bool notNull default|1";
    return $props;
  }

  /**
   * @param bool $cache
   *
   * @return CGroups
   */
  function loadRefOwner($cache = true) {
    return $this->_ref_owner = $this->loadFwdRef("owner_id", $cache);
  }

  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = $this->title;
  }
}
