<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Component");

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
  
  function getPath($separator = "."){
    return $this->parent->getPath($separator);
  }
}