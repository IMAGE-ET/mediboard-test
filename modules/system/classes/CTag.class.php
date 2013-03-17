<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CTag extends CMbObject {
  public $tag_id;
  
  public $parent_id;
  public $object_class;
  public $name;
  public $color;
  
  public $_ref_parent;
  public $_ref_items;
  
  public $_deepness;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "tag";
    $spec->key   = "tag_id";
    $spec->uniques["name"] = array("parent_id", "object_class", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["parent_id"]    = "ref class|CTag autocomplete|name dependsOn|object_class";
    $props["object_class"] = "str class";
    $props["name"]         = "str notNull seekable";
    $props["color"]        = "str maxLength|20";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["children"] = "CTag parent_id";
    $backProps["items"]    = "CTagItem tag_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $parent = $this->loadRefParent();
    $this->_view = ($parent->_id ? "$parent->_view &raquo; " : "").$this->name;
  }

  /**
   * @return CTagItem[]
   */
  function loadRefItems(){
    return $this->_ref_items = $this->loadBackRefs("items");
  }

  /**
   * @return self[]
   */
  function loadRefChildren(){
    return $this->_ref_children = $this->loadBackRefs("children");
  }

  /**
   * @return int
   */
  function countChildren(){
    return $this->countBackRefs("children");
  }

  /**
   * @return self
   */
  function loadRefParent(){
    return $this->_ref_parent = $this->loadFwdRef("parent_id");
  }
  
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if (!$this->parent_id) {
      return;
    }
    
    $tag = $this;
    while ($tag->parent_id) {
      $parent = $tag->loadRefParent();
      if ($parent->_id == $this->_id) {
        return "Récurcivité détectée, un des ancêtres du tag est lui-même";
      }
      $tag = $parent;
    }
  }
  
  function getObjects($keywords = ""){
    if (!$keywords) {
      $items = $this->loadRefItems();
    }
    else {
      $where = array(
        "tag_id"       => "= '$this->_id'",
        "object_class" => "= 'object_class'",
      );
      $item = new CTagItem;
      $items = $item->seek($keywords, $where, 10000);
    }
    
    CMbArray::invoke($items, "loadTargetObject");
    return CMbArray::pluck($items, "_ref_object");
  }
  
  function getAutocompleteList($keywords, $where = null, $limit = null) {
    $list = array();
    
    if ($keywords === "%" || $keywords == "") {
      $tree = self::getTree($this->object_class);
      self::appendItemsRecursive($list, $tree);
      
      foreach ($list as $_tag) {
        $_tag->_view = $_tag->name;
      }
    }
    else {
      $list = parent::getAutocompleteList($keywords, $where, $limit);
    }
    
    return $list;
  }

  /**
   * @param self[] $list
   * @param array  $tree
   */
  private static function appendItemsRecursive(&$list, $tree) {
    if ($tree["parent"]) {
      $list[] = $tree["parent"];
    }
    
    foreach ($tree["children"] as $_child) {
      self::appendItemsRecursive($list, $_child);
    }
  }

  /**
   * @param int $d
   *
   * @return int
   */
  function getDeepness($d = 0){
    if ($this->parent_id) {
      $d++;
      $d = $this->loadRefParent()->getDeepness($d);
    }

    return $this->_deepness = $d;
  }

  /**
   * @param string $object_class
   * @param CTag   $parent
   * @param array  $tree
   *
   * @return array
   */
  static function getTree($object_class, CTag $parent = null, &$tree = array()) {
    $tag = new self;
    $where = array(
      "object_class" => "= '$object_class'",
      "parent_id"    => (($parent && $parent->_id) ? "= '{$parent->_id}'" : "IS NULL"),
    );
    
    $tree["parent"] = $parent;
    $tree["children"] = array();
    
    $tags = $tag->loadList($where, "name");
    
    foreach ($tags as $_tag) {
      $_tag->getDeepness();
      self::getTree($object_class, $_tag, $sub_tree);
      $tree["children"][] = $sub_tree;
    }
    
    return $tree;
  }
}
