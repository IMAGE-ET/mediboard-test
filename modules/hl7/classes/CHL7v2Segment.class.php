<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Segment extends CHL7v2 {   
  var $name        = null;
  var $description = null;
  var $fields      = array();
	
	/**
	 * @var CHL7v2SegmentGroup
	 */
  var $parent = null;
    
  function __construct(CHL7v2SegmentGroup $parent) {
    $this->parent = $parent;
  }
	
	function path($path){
		$path = preg_split("/\s*,\s*/", $path);
		if (count($path) > 3) return false;
		
		$data = $this->fields;
		try {
	    $field = $data[$path[0]];
	    $item  = $field->items[$path[1]];
			
      return $item->components[$path[2]];
		}
		catch(Exception $e) {
			return false;
		}
	}
  
  function parse($data) {
    parent::parse($data);
		
    $message = $this->getMessage();

    $fields = explode($message->fieldSeparator, $this->data);
    $this->name = array_shift($fields);
		
    $specs = $this->getSpecs();
    $this->description = (string)$specs->description;
		
		if($this->name == "MSH") {
			array_unshift($fields, $message->fieldSeparator);
		}
    
    // valid characters
    if (preg_match("/[^a-z0-9]/i", $this->name) ) {
      throw new CHL7v2Exception($this->data, CHL7v2Exception::INVALID_SEGMENT_CHARACTERS);
    }
    
    // valid fields number, at least two
    if (count(array_filter($fields, "stringNotEmpty")) < 1) {
      throw new CHL7v2Exception($this->data, CHL7v2Exception::TOO_FEW_SEGMENT_FIELDS);
    }
		
		$i = 0; // don't read the 3 letters prefix
		foreach($specs->elements->field as $_spec){
			if (!isset($fields[$i])) {
				break;
			}
			
			$_data = $fields[$i++];
			
			try {
        $field = new CHL7v2Field($this, $_spec);
        $field->parse($_data);
				
				$this->fields[] = $field;
			} catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
		}
/*
    foreach ($fields as $_field) {
      try {
        $field = new CHL7v2Field($this, $_spec);
        $field->parse($_field);
        
        $this->fields[] = $field;
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }*/
  }
  
  function validate() {
    $specs = $this->getSpecs();   
    
    $specFields = $this->getFields();
    if (count($this->fields) > count($specFields)) {
      throw new CHL7v2Exception($this->name, CHL7v2Exception::TOO_MANY_FIELDS);
    }
    
    $count_field = 0;
    foreach ($specFields as $_spec_field) {
      if (isset($this->fields[$count_field])) {
        $this->fields[$count_field]->datatype = $this->getFieldDatatype($_spec_field);
        $this->fields[$count_field]->isBaseType();
      }
      $count_field++;
    }
    
    foreach ($this->fields as $_field) {
      try {
        $_field->validate();
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
  }
  
  function getMessage() {
    return $this->parent->getMessage();
  }
  
  function getVersion() {
    return $this->getMessage()->getVersion();
  }
  
  function getSpecs(){
    return $this->getSchema(self::PREFIX_SEGMENT_NAME, $this->name);
  }
}

?>