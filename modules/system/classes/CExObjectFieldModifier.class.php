<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExObjectFieldModifier extends CMbObject {
  var $ex_object_field_modifier_id = null;
  
  var $ex_object_id = null;
  var $ex_class_field_id = null;
	
  var $type  = null;
  var $value = null;
  
  var $_ref_ex_class_field = null;
  var $_ref_ex_object = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_object_field_modifier";
    $spec->key   = "ex_object_field_modifier_id";
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_object_id"] = "ref notNull class|CExObject cascade";
    $props["ex_class_field_id"] = "ref notNull class|CExClassField cascade";
    $props["value"]  = "str notNull";
    $props["type"]   = "enum list|intensity|side";
    return $props;
  }
  
  function getKey(){
    $field = $this->loadRefExClassField();
    $class = $field->loadRefExClass();
    return "CExObject_{$class->_id}-{$field->name}";
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    global $locales;
    $key = $this->getKey();
    $locales[$key] = $this->std;
    $locales["{$key}_$this->desc"] = $this->desc;
    $locales["{$key}_$this->court"] = $this->court;
    
    $this->_view = $this->std;
  }
	
	function fillIfEmpty($str) {
    if (!$this->_id) {
      $this->std = $this->desc = $this->court = $str;
			$this->updateFormFields();
      $this->std = $this->desc = $this->court = "";
    }
	}
  
  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }
}
