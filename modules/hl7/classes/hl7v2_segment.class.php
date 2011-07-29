<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Segment extends CHL7V2 {   
  var $name        = null;
  var $fields      = array();
	
	/**
	 * @var CHL7v2Message
	 */
  var $owner_message = null;
    
  function __construct(CHL7v2Message $message) {
    $this->owner_message = $message;
  }
  
  function parse($data) {
    parent::parse($data);
		
    $message = $this->owner_message;

    $fields = explode($message->fieldSeparator, $this->data);
    $this->name = $fields[0];
    
    // valid characters
    if (preg_match("/[^a-z0-9]/i", $this->name) ) {
      throw new CHL7v2Exception($this->data, CHL7v2Exception::INVALID_SEGMENT_CHARACTERS);
    }
    
    // valid fields number, at least two
    if (count(array_filter($fields, "stringNotEmpty")) < 2) {
      throw new CHL7v2Exception($this->data, CHL7v2Exception::TOO_FEW_SEGMENT_FIELDS);
    }

    foreach ($fields as $_field) {
      try {
        $field = new CHL7v2Field($this);
        $field->parse($_field);
        
        $this->fields[] = $field;
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
    
    $message->segments[] = $this;
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
    return $this->owner_message;
  }
  
  function getVersion() {
    return $this->owner_message->getVersion();
  }
  
  function getSpecs(){
    return $this->getSchema(self::PREFIX_SEGMENT_NAME, $this->name);
  }
	
	/*
	function __toString(){
		$str = "<h3>$this->name</h3>";
		
		foreach($this->fields as $i => $field) {
			$i++; // begins at 1
			$str .= "&nbsp;&nbsp; - $i : $field<br />";
		}
		return $str;
	}*/
}

?>