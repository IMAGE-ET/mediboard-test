<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CTag extends CMbObject {
  var $tag_id       = null;
  
  var $parent_id    = null;
  var $object_class = null;
  var $name         = null;
  
  var $_ref_parent  = null;
  var $_ref_items   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "tag";
    $spec->key   = "tag_id";
    $spec->uniques["name"] = array("parent_id", "object_class", "name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["parent_id"]    = "ref class|CTag";
    $props["object_class"] = "str class";
    $props["name"]         = "str notNull";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["child_tags"] = "CTag parent_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
		$this->_view = $this->name;
  }
	
	static function getTree(CMbObject $object, CTag $parent = null, &$tree = array()) {
		$object_class = $object->_class_name;
		
		$tag = new self;
		$where = array(
		  "object_class" => "= '$object_class'",
      "parent_id"    => (($parent && $parent->_id) ? "= '{$parent->_id}'" : "IS NULL"),
		);
    
    $tree["parent"] = $parent;
    $tree["children"] = array();
		
		$tags = $tag->loadList($where, "name");
		
		foreach($tags as $_tag) {
			self::getTree($object, $_tag, $sub_tree);
			$tree["children"][] = $sub_tree;
		}
		
		return $tree;
	}
}
