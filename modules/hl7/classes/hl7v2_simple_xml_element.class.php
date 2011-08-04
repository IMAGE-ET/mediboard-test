<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2SimpleXMLElement extends CMbSimpleXMLElement {
  var $occurences = 0;
  var $open = false;
	
  function getValidPatterns(){
    return array("segment", "group");
  }
	
  function reset(){
    $this->occurences = 0;
    $this->open = false;
  }
  
  function isRequired(){
    return $this->attributes()->minOccurs !== "0";
  }
  
  function isUnbounded(){
    return $this->attributes()->maxOccurs === "unbounded";
  }
  
  function markOpen(){
  	$this->open = true;
    $parent = $this->getParent();
		
		if ($parent && $parent->getName() !== "message") {
			$parent->markOpen();
		}
  }
	
	function getSegmentHeader(){
		if ($this->getName() === "segment") {
			return (string)$this;
		}
	}
}
