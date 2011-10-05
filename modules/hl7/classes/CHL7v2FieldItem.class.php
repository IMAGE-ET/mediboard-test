<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2FieldItem extends CHL7v2Component {
  /**
   * @var CHL7v2Field
   */
  var $parent = null;
  
  function __construct(CHL7v2Field $field, CHL7v2SimpleXMLElement $specs, $self_pos) {
    $message = $field->getMessage();
    
    $separators = array(
      // sub parts separator                 self type        sub part separator class
      array($message->componentSeparator,    "field-item",    "cs"), 
      array($message->subcomponentSeparator, "component",     "scs"),
      array(null,                            "sub-component", null),
    );
    
    parent::__construct($field, $specs, $self_pos, $separators);
  }
  
  function getField(){
    return $this->parent;
  }
  
  function _toXML(DOMNode $node) {
    $doc = $node->ownerDocument;
    $field = $this->getField();
    $new_node = $doc->createElement($field->name);
    
    parent::_toXML($new_node);
    
    $node->appendChild($new_node);
  }
  
  function getPath($separator = ".", $with_name = false){
    $path = $this->parent->getPath($separator, $with_name);
    $path[count($path)-1] = $path[count($path)-1]."[".($this->self_pos+1)."]";
    return $path;
  }
}