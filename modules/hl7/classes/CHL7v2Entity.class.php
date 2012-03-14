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
  
  /**
   * @var CHL7v2SimpleXMLElement
   */
  var $specs         = null;
  var $data          = null;
  
  function __construct(){
    $this->id = self::$_id++;
  }
  
  function getId(){
    return $this->id;
  }
  
  function parse($data) {
    $this->data = $data;
  }
  
  function fill($items) {}
  
  /**
   * Appends an error object in the errors array
   * 
   * @param integer      The code of the error
   * @param string       Additional info about the error
   * @param CHL7v2Entity The entity where the error occured
   * @param integer      The error level : CHL7v2Error::E_ERROR or CHL7v2Error::E_WARNING
   */
  function error($code, $data, $entity = null, $level = CHL7v2Error::E_ERROR) {    
    $this->getMessage()->error($code, $data, $entity, $level);
  }
  
  abstract function validate();
  
  /**
   * @return CHL7v2Message
   */
  abstract function getMessage();
  
  /**
   * @return CHL7v2Segment
   */
  abstract function getSegment();
  
  /**
   * @return array
   */
  abstract function getPath($separator = ".", $with_name = false);
  
  /**
   * @return string
   */
  function getPathString($glue = "/", $separator = ".", $with_name = true) {
    return implode($glue, $this->getPath($separator, $with_name));
  }
  
  function getEncoding() {
    return $this->getMessage()->getEncoding();
  }
  
  abstract function _toXML(DOMNode $node, $hl7_datatypes, $encoding);
}
