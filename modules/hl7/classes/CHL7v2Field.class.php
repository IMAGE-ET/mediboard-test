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
  static $typesBase = array(
    "Date",
    "DateTime",
    "Time",
    "Double", 
    "Integer",
    "String",
  );
  
	/**
	 * @var CHL7v2Segment
	 */
  var $owner_segment = null;
	
  var $datatype      = null;
  var $description   = null;
  var $items         = array();
  
  var $_is_base_type = null;
  
  function __construct(CHL7v2Segment $segment, $spec) {
    $this->owner_segment = $segment;
    $this->datatype    = (string)$spec->datatype;
    $this->description = (string)$spec->description;
  }
  
  function parse($data) {
    parent::parse($data);
		
		$specs = $this->getSpecs();
		
		$message = $this->owner_segment->getMessage();
    $items = explode($message->repetitionSeparator, $this->data);
		
		$this->items = array();
		foreach($items as $_item) {
			$_item_obj = new CHL7v2FieldItem;
      $_item_obj->data = $_item;
      $_item_obj->components = ($_item !== "" ? explode($message->componentSeparator, $_item) : array());
			
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
				mbTrace($i, $this->owner_segment->name);
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
  
  function isBaseType() {
    return $this->_is_base_type = isset(self::$typesBase[$this->datatype]);
  }
	
	function getVersion(){
		return $this->owner_segment->getVersion();
	}
	
	function getValue(){
		return $this->items;
	}
}
