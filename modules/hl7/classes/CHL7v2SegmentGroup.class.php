<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Entity");

class CHL7v2SegmentGroup extends CHL7v2Entity {
  var $children = array();
  var $name;
  
  /**
   * @var CHL7v2SegmentGroup
   */
  var $parent = null;
    
  function __construct(CHL7v2SegmentGroup $parent) {
    parent::__construct($parent);
    
    $this->parent = $parent;
    $this->name = implode(" ", $this->getSpecs()->xpath("//segment"));
    $parent->appendChild($this);
  }
  
  function validate() {
    foreach($this->children as $child) {
      $child->validate();
    }
  }
  
  function getVersion(){
    return $this->parent->getVersion();
  }
  
  function getSpecs(){
    return $this->parent->getSpecs();
  }
  
  function getParent() {
    return $this->parent;
  }
  
  /**
   * @return CHL7v2Message
   */
  function getMessage() {
    return $this->parent->getMessage();
  }
  
  function appendChild($child){
    return $this->children[] = $child;
  }
  
  function __toString(){
    return implode((CHL7v2Message::$decorateToString ? "" : $this->getMessage()->segmentTerminator), $this->children);
  }
}

?>