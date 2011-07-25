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
  var $owner_message = null;
    
  function __construct(CHL7v2Message $message) {
    $this->owner_message = $message;
  }
  
  function parseSegment($line) {
    $message = $this->owner_message;

    $fields = explode($message->fieldSeparator, $line);
    $this->name = $fields[0];
    
    // valid characters
    if (preg_match("/[^a-z0-9]/i", $this->name) ) {
      throw new CHL7v2Exception($line, CHL7v2Exception::INVALID_SEGMENT_CHARACTERS);
    }
    
    // valid fields number, at least two
    if (count(array_filter($fields, "stringNotEmpty")) < 2) {
      throw new CHL7v2Exception($line, CHL7v2Exception::TOO_FEW_SEGMENT_FIELDS);
    }

    foreach ($fields as $_field) {
      try {
        $field = new CHL7v2Field($this);
        $field->parseField($_field);
        
        $this->fields[] = $field;
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
    
    $message->segments[] = $this;
  }
  
  function validateSegment() {
    $this->loadSegmentSchema();   
    
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
        $_field->validateField();
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
    $version = explode(".", $this->fields[11]->value[0]);
        
    if ($version[1] != "x")
      $this->owner_message->version =  $version[1];
  }
  
  function loadSegmentSchema() {
    if (isset(self::$specs[$this->name])) {
      return;
    }
    
    $this->spec_hl7_dir  = self::LIB_HL7."/hl7v2_".$this->getMessage()->version."/";
    $this->spec_filename = self::PREFIX_SEGMENT_NAME.$this->name.".xml";
    
    $filename = $this->spec_hl7_dir.$this->spec_filename;
    if (!file_exists($filename)) {
      throw new CHL7v2Exception($filename, CHL7v2Exception::SPECS_FILE_MISSING);
    }

    self::$specs[$this->name] = simplexml_load_file($filename);
    
    //mbTrace(self::$specs[$this->name], "specs");
  }
  
  function getSpecs() {
    return self::$specs[$this->name];
  }
	
	function __toString(){
		return "&nbsp;&nbsp; - ".implode("\n&nbsp;&nbsp; - ", $this->fields);
	}
}

?>