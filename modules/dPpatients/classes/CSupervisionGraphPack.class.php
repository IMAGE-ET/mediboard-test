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
class CSupervisionGraphPack extends CMbObject {
  public $supervision_graph_pack_id;

  public $owner_class;
  public $owner_id;

  public $title;
  public $disabled;

  /** @var CSupervisionGraphToPack[] */
  public $_ref_graph_links;

  /** @var CGroups|CFunctions */
  public $_ref_owner;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_pack";
    $spec->key   = "supervision_graph_pack_id";
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
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["graph_links"] = "CSupervisionGraphToPack pack_id";
    return $backProps;
  }

  /**
   * Load graph links
   *
   * @return CSupervisionGraphToPack[]
   */
  function loadRefsGraphLinks(){
    return $this->_ref_graph_links = $this->loadBackRefs("graph_links", "rank, supervision_graph_to_pack_id");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->title;
  }

  /**
   * Load owner
   *
   * @return CGroups|CFunctions
   */
  function loadRefOwner(){
    return $this->_ref_owner = $this->loadFwdRef("owner_id");
  }

  /**
   * Get all pakcs from an object
   *
   * @param CMbObject $object The object to get the packs of
   *
   * @return self[]
   */
  static function getAllFor(CMbObject $object) {
    $pack = new self;

    $where = array(
      "owner_class" => "= '$object->_class'",
      "owner_id"    => "= '$object->_id'",
    );

    return $pack->loadList($where, "title");
  }
}
