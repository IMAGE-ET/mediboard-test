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
    parent::__construct($parent);
    
    $this->parent = $parent;
  }
  
  function path($path) {
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
      if (!array_key_exists($i, $fields)) {
        break;
      }
      
      $_data = $fields[$i++];
      
      $field = new CHL7v2Field($this, $_spec);
      $field->parse($_data);
      
      $this->fields[] = $field;
    }
  }
  
  function fill($fields) {
    if (!$this->name) return;
    
    $specs = $this->getSpecs();
    $message = $this->getMessage();
    
    if ($this->name === "MSH") {
      // Encoding characters without the field separator
      $fields[1] = substr($message->encoding_characters(), 1); 
      
      // Message type
      $fields[8] = $message->name;
      
      // Version Id
      $fields[11] = $message->version;
    }
    
    $i = 0;
    foreach($specs->elements->field as $_spec){
      if (!array_key_exists($i, $fields)) {
        break;
      }
      
      $_data = $fields[$i++];
      
      $field = new CHL7v2Field($this, $_spec);
      $field->fill($_data);
      
      $this->fields[] = $field;
    }
  }
  
  function validate() {
    foreach($this->fields as $field) {
      $field->validate();
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
  
  /**
   * @param string             $name
   * @param CHL7v2SegmentGroup $parent
   * @return CHL7v2Segment
   */
  static function create($name, CHL7v2SegmentGroup $parent) {
    $class = "CHL7v2Segment$name";
    
    if (class_exists($class)) {
      $segment = new $class($parent);
    }
    else {
      $segment = new self($parent);
    }
    
    $segment->name = $name;
    
    return $segment;
  }
  
  function __toString(){
    $sep = $this->getMessage()->fieldSeparator;
    $name = $this->name;
    
    if (CHL7v2Message::$decorateToString) {
      $sep = "<span class='fs'>$sep</span>";
      $name = "<strong>$name</strong>";
    }
    
    $fields = $this->fields;
    
    if ($this->name === "MSH") {
      array_shift($fields);
    }
    
    $str = $name.$sep.implode($sep, $fields);
    
    if (CHL7v2Message::$decorateToString) {
      $str = "<div class='segment' id='segment-$this->id' data-title='$this->description'>$str</div>";
    }
    
    return $str;
  }
  
  function build(CHL7v2Event $event, $name = null) {
    if (!$event->msg_codes) {
      throw new CHL7v2Exception(CHL7v2Exception::MSH_CODE_MISSING);
    }
    
    // This segment has the following fields
    if ($name) {
      $this->name = $name;
    }
    
    $this->getMessage()->appendChild($this);
  }
  
  function getAssigningAuthority($name = "mediboard") {
    switch ($name) {
      case "mediboard" :
        return array(
          "Mediboard",
          "1.2.250.1.2.3.4",
          "OpenXtrem"
        );
        break;
      case "INS-C" :
        return array(
          null,
          "1.2.250.1.213.1.4.2",
          "ISO"
        );
        break;
      case "ADELI" :
        return array(
          null,
          "1.2.250.1.71.4.2.1",
          "ISO"
        );
        break;
      case "RPPS" :
        return array(
          null,
          "1.2.250.1.71.4.2.1",
          "ISO"
        );
        break;  
      default :
        throw new CHL7v2Exception(CHL7v2Exception::NO_AUTHORITY);
        break;
    }
  }
}

?>