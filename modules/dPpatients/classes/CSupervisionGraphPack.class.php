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
class CSupervisionGraphPack extends CMbObject {
  var $supervision_graph_pack_id;

  var $owner_class;
  var $owner_id;

  var $title;
  var $disabled;

  /**
   * @var CSupervisionGraphToPack[]
   */
  var $_ref_graph_links;

  /**
   * @var CGroups|CFunctions
   */
  var $_ref_owner;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_pack";
    $spec->key   = "supervision_graph_pack_id";
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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["graph_links"] = "CSupervisionGraphToPack pack_id";
    return $backProps;
  }

  /**
   * @return CSupervisionGraphToPack[]
   */
  function loadRefsGraphLinks(){
    return $this->_ref_graph_links = $this->loadBackRefs("graph_links", "rank, supervision_graph_to_pack_id");
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->title;
  }

  /**
   * @return CGroups|CFunctions
   */
  function loadRefOwner(){
    return $this->_ref_owner = $this->loadFwdRef("owner_id");
  }

  /**
   * @param CMbObject $object
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
