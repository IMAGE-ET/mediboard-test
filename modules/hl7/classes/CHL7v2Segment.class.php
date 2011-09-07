<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Segment extends CHL7v2Entity {   
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
  
  function getFieldsCount() {
    return CHL7v2XPath::queryCountNode($this->getSpecs(), "elements/field");
  }
  
  function parse($data) {
    parent::parse($data);
    
    $message = $this->getMessage();

    $fields = CHL7v2::split($message->fieldSeparator, $this->data);
    $this->name = array_shift($fields);
    
    $specs = $this->getSpecs();
    
    // check the number of fields
    /*$fields_count = count($specs->elements->children());
    if (count($fields)+1 > $fields_count) {
      $this->error(CHL7v2Exception::TOO_MANY_FIELDS, $this->data);
    }*/
    
    // valid fields number, at least two
    if (count(array_filter($fields, "stringNotEmpty")) < 1) {
      $this->error(CHL7v2Exception::TOO_FEW_SEGMENT_FIELDS, $this->data);
    }
    
    $this->description = (string)$specs->description;
    
    if ($this->name === "MSH") {
      array_unshift($fields, $message->fieldSeparator);
    }
    
    $i = 0;
    foreach($specs->elements->field as $_spec){
      if (!isset($fields[$i])) {
        break;
      }
      
      $_data = $fields[$i++];
      
      $field = new CHL7v2Field($this, $_spec);
      $field->parse($_data);
      
      $this->fields[] = $field;
    }
  }
  
  function validate() {
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
  
  function __toString(){
    $sep = $this->getMessage()->fieldSeparator;
		$fields = $this->fields;
		
  	if ($this->name === "MSH") {
  		array_shift($fields);
  	}
		
    return $this->name.$sep.implode($sep, $fields);
  }
}

?>