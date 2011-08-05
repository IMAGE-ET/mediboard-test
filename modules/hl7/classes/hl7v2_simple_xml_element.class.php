<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2SimpleXMLElement extends CMbSimpleXMLElement {
  function getValidPatterns(){
    return array("segment", "group");
  }
	
  function reset(){
    $this->attributes()->mb_occurences = 0;
    $this->attributes()->used = 0;
  }
  
  function isRequired(){
    return (string)$this->attributes()->minOccurs !== "0";
  }
  
  function isUnbounded(){
    return (string)$this->attributes()->maxOccurs === "unbounded";
  }
	
	private function init(){
    if (!isset($this->attributes()->mb_occurences))
      $this->addAttribute("mb_occurences", 0);
			
    if (!isset($this->attributes()->mb_used))
      $this->addAttribute("mb_used", 0);
	}
  
  function getOccurences(){
    return (int)$this->attributes()->mb_occurences;
  }
  
  function isUsed(){
    return (bool)$this->attributes()->mb_used;
  }
  
  function markOpen(){
  	$this->init();
		
  	$this->attributes()->mb_occurences = $this->getOccurences()+1;
		
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
