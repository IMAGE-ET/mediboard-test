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

class CExClassMessage extends CMbObject {
  public $ex_class_message_id;
  
  public $ex_group_id;
  public $subgroup_id;
  public $type;
  public $predicate_id;
  
  public $title;
  public $text;
  
  public $coord_title_x;
  public $coord_title_y;
  public $coord_text_x;
  public $coord_text_y;
  
  public $coord_left;
  public $coord_top;
  public $coord_width;
  public $coord_height;
  
  public $_ref_ex_group;
  public $_ref_predicate;
  public $_ref_properties;

  public $_default_properties;
  public $_no_size = false;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_message";
    $spec->key   = "ex_class_message_id";
    $spec->uniques["coord"] = array("ex_group_id", "coord_title_x", "coord_title_y", "coord_text_x", "coord_text_y");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_group_id"] = "ref notNull class|CExClassFieldGroup cascade";
    $props["subgroup_id"] = "ref class|CExClassFieldSubgroup nullify";
    $props["type"]        = "enum list|title|info|warning|error";
    $props["predicate_id"]= "ref class|CExClassFieldPredicate autocomplete|_view|true nullify";
    
    $props["title"]       = "str";
    $props["text"]        = "text notNull";
    
    $props["coord_title_x"] = "num min|0 max|100";
    $props["coord_title_y"] = "num min|0 max|100";
    $props["coord_text_x"] = "num min|0 max|100";
    $props["coord_text_y"] = "num min|0 max|100";
    
    // Pixel positionned
    $props["coord_left"]   = "num";
    $props["coord_top"]    = "num";
    $props["coord_width"]  = "num min|1";
    $props["coord_height"] = "num min|1";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["properties"] = "CExClassFieldProperty object_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = ($this->title ? $this->title : CMbString::truncate($this->text, 30));
    $this->_no_size = true;
  }

  /**
   * @param bool $cache
   *
   * @return CExClassFieldGroup
   */
  function loadRefExGroup($cache = true){
    return $this->_ref_ex_group = $this->loadFwdRef("ex_group_id", $cache);
  }

  /**
   * @param bool $cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true){
    return $this->loadRefExGroup($cache)->loadRefExClass($cache);
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
