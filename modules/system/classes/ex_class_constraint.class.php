<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassConstraint extends CMbObject {
  var $ex_class_constraint_id = null;
  
  var $ex_class_id   = null;
  var $field         = null;
  var $operator      = null;
  var $value         = null;
  
  var $_ref_ex_class = null;
  
  var $_locale = null;
  var $_locale_desc = null;
  var $_locale_court = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_constraint";
    $spec->key   = "ex_class_constraint_id";
    $spec->uniques["constraint"] = array("ex_class_id", "field");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref notNull class|CExClass";
    $props["field"]       = "str notNull canonical";
    $props["operator"]    = "enum notNull list|=|!=|>|>=|<|<=|startsWith|endsWith|contains default|=";
    $props["value"]       = "str notNull";
    
    $props["_locale"]     = "str";
    $props["_locale_desc"]  = "str";
    $props["_locale_court"] = "str";
    return $props;
  }
	
	function loadTargetObject(){
		$this->loadRefExClass();
		$this->completeField("field", "value");
		
		$ref_object = new $this->_ref_ex_class->host_class;
		
		if (!$this->_id) {
			return $this->_ref_target_object = new CMbObject;
		}
		
	  $spec = $ref_object->_specs[$this->field];
	  
	  
	  if ($spec instanceof CRefSpec) {
	    $object = new $spec->class;
	    $object->load($this->value);
	    $this->_ref_target_object = $object;
	  }
		else {
	    // empty object
	    $this->_ref_target_object = new CMbObject;
		}
		
		return $this->_ref_target_object;
	}
  
  function checkConstraint(CMbObject $object) {
    $this->completeField("field", "value");
    $object->completeField($this->field);
    $value = $object->{$this->field};
    $cons = $this->value;
    
    // =|!=|>|>=|<|<=|startsWith|endsWith|contains default|=
    switch ($this->operator) {
      default:
      case "=": 
        if ($value == $cons) return true;
        break;
        
      case "!=": 
        if ($value != $cons) return true;
        break;
        
      case ">": 
        if ($value > $cons) return true;
        break;
        
      case ">=": 
        if ($value >= $cons) return true;
        break;
        
      case "<": 
        if ($value < $cons) return true;
        break;
        
      case "<=": 
        if ($value <= $cons) return true;
        break;
        
      case "startsWith": 
        if (strpos($value, $cons) === 0) return true;
        break;
        
      case "endsWith": 
        if (substr($value, -strlen($cons)) == $cons) return true;
        break;
        
      case "contains": 
        if (strpos($value, $cons) !== false) return true;
        break;
    }
    
    return false;
  }
  
  function loadRefExClass(){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id");
  }
}
