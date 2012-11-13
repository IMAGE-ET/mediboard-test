<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassMessage extends CMbObject {
  var $ex_class_message_id = null;
  
  var $ex_group_id   = null;
  var $subgroup_id   = null;
  var $type          = null;
  var $predicate_id  = null;
  
  var $title         = null;
  var $text          = null;
  
  var $coord_title_x = null;
  var $coord_title_y = null;
  var $coord_text_x  = null;
  var $coord_text_y  = null;
  
  var $coord_left    = null;
  var $coord_top     = null;
  var $coord_width   = null;
  var $coord_height  = null;
  
  var $_ref_ex_group = null;
  var $_ref_predicate = null;
  var $_ref_properties = null;

  var $_default_properties = null;
  var $_no_size = false;

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
