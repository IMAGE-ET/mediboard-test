<?php

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Link between a CExObject and a CMbObject
 */
class CExLink extends CMbMetaObject {
  /**
   * @var integer Primary key
   */
  public $ex_link_id;

  public $ex_class_id;
  public $ex_object_id;
  public $level;
  public $group_id;

  /**
   * @var CExObject
   */
  public $_ref_ex_object;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "ex_link";
    $spec->key    = "ex_link_id";
    return $spec;  
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_id"]    = "ref notNull class|CMbObject meta|object_class cascade";
    $props["ex_class_id"]  = "ref notNull class|CExClass";
    $props["ex_object_id"] = "ref notNull class|CExObject";
    $props["group_id"]     = "ref notNull class|CGroups";
    $props["level"]        = "enum notNull list|object|ref1|ref2|add default|object";
    return $props;
  }

  /**
   * Load ExObject
   *
   * @param bool $cache Use local cache
   *
   * @return CExObject
   */
  function loadRefExObject($cache = true){
    if ($cache && $this->_ref_ex_object && $this->_ref_ex_object->_id) {
      return $this->_ref_ex_object;
    }

    $ex_object = new CExObject($this->ex_class_id);
    $ex_object->load($this->ex_object_id);

    if ($cache) {
      $this->_ref_ex_object = $ex_object;
    }

    return $ex_object;
  }

  /**
   * @see parent::loadFwdRef()
   *
   * Required because of the ex_object_id field
   */
  function loadFwdRef($field, $cached = false) {
    if ($field === "ex_object_id") {
      $ex_object = new CExObject($this->ex_class_id);
      $ex_object->load($this->ex_object_id);
      return $this->_fwd[$field] = $ex_object;
    }

    return parent::loadFwdRef($field, $cached);
  }

  /**
   * Mass loading of CExObjects
   *
   * @param self[] $list List of ExLinks
   *
   * @return void
   */
  static function massLoadExObjects(array $list) {
    $ex_object_id_by_ex_class = array();
    $ex_links_by_ex_class = array();

    foreach ($list as $_link) {
      $_ex_class_id = $_link->ex_class_id;

      $ex_object_id_by_ex_class[$_ex_class_id][] = $_link->ex_object_id;
      $ex_links_by_ex_class[$_ex_class_id][]     = $_link;
    }

    foreach ($ex_object_id_by_ex_class as $_ex_class_id => $_ex_object_ids) {
      $_ex_object = new CExObject($_ex_class_id);

      $where = array(
        "ex_object_id" => $_ex_object->getDS()->prepareIn($_ex_object_ids),
      );

      $_ex_objects = $_ex_object->loadList($where);

      /** @var CExLink $_link */
      foreach ($ex_links_by_ex_class[$_ex_class_id] as $_link) {
        $_link->_ref_ex_object = $_ex_objects[$_link->ex_object_id];
      }
    }
  }
}
