<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassFieldSubgroup extends CMbObject {
  var $ex_class_field_subgroup_id = null;
  
  var $parent_class = null;
  var $parent_id    = null;
  var $title        = null;
  var $predicate_id = null;
  
  var $coord_left   = null;
  var $coord_top    = null;
  var $coord_width  = null;
  var $coord_height = null;

  /**
   * @var CExClassFieldSubgroup|CExClassFieldGroup
   */
  var $_ref_parent;

  /**
   * @var CExClassFieldPredicate
   */
  var $_ref_predicate;

  /**
   * @var CExClassFieldSubgroup[]
   */
  var $_ref_children_groups;

  /**
   * @var CExClassField[]
   */
  var $_ref_children_fields;

  /**
   * @var CExClassMessage[]
   */
  var $_ref_children_messages;

  /**
   * @var CExClassFieldProperty[]
   */
  var $_ref_properties;

  var $_default_properties = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_subgroup";
    $spec->key   = "ex_class_field_subgroup_id";
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["parent_class"] = "enum notNull list|CExClassFieldGroup|CExClassFieldSubgroup";
    $props["parent_id"]    = "ref notNull class|CMbObject meta|parent_class cascade";
    $props["title"]        = "str";
    $props["predicate_id"] = "ref class|CExClassFieldPredicate autocomplete|_view|true nullify";
    
    // Pixel positionned
    $props["coord_left"]   = "num";
    $props["coord_top"]    = "num";
    $props["coord_width"]  = "num min|1";
    $props["coord_height"] = "num min|1";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["children_groups"]   = "CExClassFieldSubgroup parent_id";
    $backProps["children_fields"]   = "CExClassField subgroup_id";
    $backProps["children_messages"] = "CExClassMessage subgroup_id";
    $backProps["identifiants"]      = "CIdSante400 object_id cascade";
    $backProps["properties"]        = "CExClassFieldProperty object_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = ($this->title != "" ? $this->title : "[Sans titre]");
  }

  /**
   * @param bool $cache
   *
   * @return CExClassFieldGroup|CExClassFieldSubgroup
   */
  function loadRefParent($cache = true){
    return $this->_ref_parent = $this->loadFwdRef("parent_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CExClassFieldPredicate
   */
  function loadRefPredicate($cache = true){
    return $this->_ref_predicate = $this->loadFwdRef("predicate_id", $cache);
  }

  /**
   * @param bool $recurse
   *
   * @return CExClassFieldSubgroup[]
   */
  function loadRefsChildrengroups($recurse = false){
    $this->_ref_children_groups = $this->loadBackRefs("children_groups", "ex_class_field_subgroup_id");

    if ($recurse) {
      $this->loadRefsChildrenFields();
      $this->loadRefsChildrenMessages();

      foreach ($this->_ref_children_groups as $_subgroup) {
        $_subgroup->loadRefsChildrengroups($recurse);
      }

      $this->loadRefsChildrenFields();
    }

    return $this->_ref_children_groups;
  }

  /**
   * @return CExClassFieldGroup
   */
  function getExGroup(){
    return $this->loadRefParent()->getExGroup();
  }

  /**
   * @param bool $cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true){
    return $this->getExGroup()->loadRefExClass($cache);
  }

  function loadRefsChildrenFields($cache = true){
    $group = $this->getExGroup();
    $fields = $group->loadRefsFields($cache);

    foreach ($fields as $_id => $_field) {
      if ($_field->subgroup_id != $this->_id) {
        unset($fields[$_id]);
      }
    }

    return $this->_ref_children_fields = $fields;
  }

  /**
   * @param bool $cache
   *
   * @return CExClassMessage[]
   */
  function loadRefsChildrenMessages($cache = true){
    $group = $this->getExGroup();
    $messages = $group->loadRefsMessages($cache);

    foreach ($messages as $_id => $_message) {
      if ($_message->subgroup_id != $this->_id) {
        unset($messages[$_id]);
      }
    }

    return $this->_ref_children_messages = $messages;
  }

  /**
   * @return CExClassFieldProperty[]
   */
  function loadRefProperties(){
    return $this->_ref_properties = $this->loadBackRefs("properties");
  }

  /**
   * @param bool $cache
   *
   * @return array
   */
  function getDefaultProperties($cache = true){
    if ($cache && $this->_default_properties !== null) {
      return $this->_default_properties;
    }

    return $this->_default_properties = CExClassFieldProperty::getDefaultPropertiesFor($this);
  }
}
