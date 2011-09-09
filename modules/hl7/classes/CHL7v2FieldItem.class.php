<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2FieldItem {
  /**
   * @var CHL7v2Field
   */
  var $field = null;
  var $data = null;
  var $components = array();
  var $specs = null;
	
	/**
	 * @var CHL7v2DataType
	 */
  var $composite_specs = null;
  
  function __construct(CHL7v2Field $field) {
    $this->field = $field;
    $this->specs = $field->getSpecs();
  }
  
	/**
	 * @param integer $i [optional]
	 * @return bool
	 */
  function hasSubComponents($i = null){
    $spec = $this->getCompositeSpecs();
    
    if (isset($i)) {
      $spec = $spec->components[$i];
    }
    
    return $spec instanceof CHL7v2DataTypeComposite;
  }
  
	/**
	 * Parse a field item into components
	 * 
	 * @param string $data
	 * @return void
	 */
  function parse($data) {
    $this->data = $data;
    
    $message = $this->getMessage();
    $keep_original = $this->field->keep();
		
		$components = $data;
		
		if ($this->hasSubComponents()) {
			$components = CHL7v2::split($message->componentSeparator, $data, $keep_original);
			$components = array_pad($components, count($this->getCompositeSpecs()->components), null);
			
	    foreach($components as $i => &$component) {
	    	// If this component has sub components
	    	if ($this->hasSubComponents($i)) {
	        $sub_components = CHL7v2::split($message->subcomponentSeparator, $component, $keep_original);
	        //$sub_components = array_pad($sub_components, count($this->getCompositeSpecs()->components[$i]->components), null);
					
		      /*if (!$keep_original) {
		        $sub_components = array_map(array($message, "unescape"), $sub_components);
		      }*/
				}
				
				// Scalar type (NM, ST, ID, etc)
				else {
					$sub_components = $message->unescape($component);
				}
	      
	      $component = $sub_components;
	    }
    }
    
		//mbTrace($components);
    $this->components = $components;
  }
  
	/**
	 * Fill a field item with data
	 * 
	 * @param array $components
	 * @return void
	 */
  function fill($components) {
    if ($this->hasSubComponents()) {
      if (!is_array($components)) {
	      $components = array($components);
	    }
      
      foreach($components as $i => &$component) {
        if ($this->hasSubComponents($i)) {
          $component = array($component);
        }
      }
    }
    
    $this->components = $this->getCompositeSpecs()->toHL7($components, $this->field);
  }
  
  /**
   * @return CHL7v2DataType
   */
  function getCompositeSpecs(){
  	if ($this->composite_specs) {
  		return $this->composite_specs;
  	}
		
    return $this->composite_specs = CHL7v2DataType::load($this->field->datatype, $this->field->getVersion());
  }
  
	/**
	 * Validate data in the field
	 * @return bool
	 */
  function validate(){
    $field = $this->field;
    $specs = $this->getCompositeSpecs();
    
    if (!$specs->validate($this->components, $field)) {
      //$field->error(CHL7v2Exception::INVALID_DATA_FORMAT, var_export($this->components, true), $field);
      return false;
    }
		
		return true;
  }
  
  function getValue() {
    return $this->getCompositeSpecs()->toMB($this->components, $this->field);
  }
  
  function getMessage(){
    return $this->field->getMessage();
  }
  
  function __toString(){
		if ($this->hasSubComponents()) {
	    $message = $this->getMessage();
	    $keep_original = $this->field->keep();
      $comp = array();
			
	    foreach($this->components as $i => $sub_compoments) {
	    	if ($this->hasSubComponents($i)) {
		      if (!$keep_original) {
		        $sub_compoments = array_map(array($message, "escape"), $sub_compoments);
		      }
		      
		      $comp[] = implode($message->subcomponentSeparator, $sub_compoments);
				}
				else {
					$comp[] = $sub_compoments;
				}
	    }
    
      return implode($message->componentSeparator, $comp);
		}
		else {
			return "$this->components";
		}
  }
}