<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2SegmentGroup extends CHL7V2 {
  var $children = array();
	var $name;
	
	/**
	 * @var CHL7v2SegmentGroup
	 */
  var $parent = null;
    
  function __construct(CHL7v2SegmentGroup $parent) {
    $this->parent = $parent;
		$parent->appendChild($this);
  }
	
	static function getFirstSegmentHeader($spec, $header_name) {
		$xp = "*//segment[text()='$header_name']";
		$elements = $spec->xpath($xp);
		$first = reset($elements);
		
		if (!$first) return;
		
		$stack = array($first);
		$p = $first;
		while(($p = self::getParentElement($p)) && ($p != $spec)) {
			$stack[] = $p;
		}
		
		return array_reverse($stack);
	}
	
	static function getParentElement($spec) {
		return $spec->getParent();
	}
	
	function validate(){
		
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
  
  function getMessage() {
    return $this->parent->getMessage();
  }
  
  function appendChild($child){
    return $this->children[] = $child;
  }
}

?>