<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/** 
 * Structure d'un message HL7
 * 
 * Message
 * |- Segment              \n
 *   |- Field              |
 *     |- FieldItem        ~
 *       |- Component      ^
 *         |- Subcomponent &
 */

abstract class CHL7v2Entity extends CHL7v2 {
	protected static $_id = 0;
  protected $id      = null;
  var $spec_filename = null;
  var $specs         = null;
  var $data          = null;
	
	function __construct(){
		$this->id = self::$_id++;
	}
  
  function parse($data) {
    $this->data = $data;
  }
  
  function fill($items) {}
  
  function getDescription() {
    return CHL7v2XPath::queryTextNode($this->getSpecs(), "description");
  }
  
  function getFieldDatatype(SimpleXMLElement $spec_field) {    
    return CHL7v2XPath::queryTextNode($spec_field, "datatype");
  }
  
  function error($code, $data, $field = null) {    
    $this->getMessage()->error($code, $data, $field);
  }
  
  abstract function validate();
  
  /**
   * @return CHL7v2Message
   */
  abstract function getMessage();
}
