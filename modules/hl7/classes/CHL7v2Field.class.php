<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Segment");

class CHL7v2Field extends CHL7v2Entity {
  /**
   * @var CHL7v2Segment
   */
  var $owner_segment = null;
  
  var $name          = null;
  var $datatype      = null;
  var $description   = null;
  var $required      = null;
  var $unbounded     = null;
  var $items         = array();
  
  private $_ts_fixed = false;
  
  function __construct(CHL7v2Segment $segment, $spec) {
    $this->owner_segment = $segment;
    $this->name        = (string)$spec->name;
    $this->datatype    = (string)$spec->datatype;
    $this->description = (string)$spec->description;
    $this->required    = $spec->isRequired();
    $this->unbounded   = $spec->isUnbounded();
  }
  
  function parse($data) {
    parent::parse($data);
    
    $specs = $this->getSpecs();
    $message = $this->getMessage();
    
    if ($this->required && $this->data === "") {
      throw new CHL7v2Exception(CHL7v2Exception::FIELD_EMPTY, $message->current_line+1, $this->name, $this->description, $message->getCurrentLine());
    }
    
    $items = CHL7v2::split($message->repetitionSeparator, $this->data, $this->name === "MSH.2");
    
    if (!$this->unbounded && count($items) > 1) {
      //throw new CHL7v2Exception(CHL7v2Exception::TOO_MANY_FIELD_ITEMS, $message->current_line+1, $this->name, $this->description, $message->getCurrentLine());
    }
    
    $this->items = array();
    foreach($items as $_item) {
      $this->items[] = new CHL7v2FieldItem($this, $_item);
    }
    
    $this->validate();
  }
  
  function validate() {
    foreach($this->items as $item) {
      $item->validate();
    }
  }
  
  function getSpecs(){
    $specs = $this->getSchema(self::PREFIX_COMPOSITE_NAME, $this->datatype);
    
    // The timestamp case, where Time contains TimeStamp data
    if (!$this->_ts_fixed && $this->datatype === "TS") {
      $specs->elements->field[0]->datatype = "TimeStamp";
    }
    
    $this->_ts_fixed = true;
    
    return $specs;
  }
  
  function getVersion(){
    return $this->owner_segment->getVersion();
  }
  
  function getValue(){
    return $this->items;
  }
  
  /**
   * @return CHL7v2Message
   */
  function getMessage(){
    return $this->owner_segment->getMessage();
  }
}
