<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Segment");

class CHL7v2Field extends CHL7v2 {
	/**
	 * @var CHL7v2Segment
	 */
  var $owner_segment = null;
	
  var $name          = null;
  var $datatype      = null;
  var $description   = null;
  var $minOccurs     = null;
  var $maxOccurs     = null;
  var $items         = array();
  
  function __construct(CHL7v2Segment $segment, $spec) {
    $this->owner_segment = $segment;
    $this->name        = (string)$spec->name;
    $this->datatype    = (string)$spec->datatype;
    $this->description = (string)$spec->description;
    $this->minOccurs   = (string)$spec->attributes()->minOccurs;
    $this->maxOccurs   = (string)$spec->attributes()->maxOccurs;
  }
  
  function parse($data) {
    parent::parse($data);
		
		$specs = $this->getSpecs();
    $message = $this->getMessage();
		
		if ($this->minOccurs > 0 && $this->data === "") {
      throw new CHL7v2Exception(CHL7v2Exception::FIELD_EMPTY, $message->current_line+1, $this->name, $this->description, $message->getCurrentLine());
		}
		
		if ($this->owner_segment->name !== "MSH") {
			$items = explode($message->repetitionSeparator, $this->data);
		}
		else {
			$items = array($this->data);
		}
		
		if ($this->maxOccurs !== "unbounded" && count($items) > 1) {
      throw new CHL7v2Exception(CHL7v2Exception::TOO_MANY_FIELD_ITEMS, $message->current_line+1, $this->name, $this->description, $message->getCurrentLine());
    }
		
		$this->items = array();
		foreach($items as $_item) {
			$_item_obj = new CHL7v2FieldItem($this, $data);
			$this->items[] = $_item_obj;
		}
  }
  
  function validate() {
    $specs = $this->getSpecs();
				return;
		$parts = array();
		$i = 0;
		foreach($specs->elements->field as $_field) {
			if (!isset($this->parts[$i])) {
				CHL7v2::d($i, $this->owner_segment->name);
				break;
			}
      $parts[(string)$_field->name] = $this->parts[$i];
			$i++;
		}
		
		$this->parts = $parts;
    
    $this->getMinOccurs($specs);
  }
  
  function getSpecs(){
    return $this->getSchema(self::PREFIX_COMPOSITE_NAME, $this->datatype);
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
