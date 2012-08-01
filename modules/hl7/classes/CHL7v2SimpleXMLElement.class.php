<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * A custom version of the CMbSimpleXMLElement used when reading an XML document with simpleXML
 */
class CHL7v2SimpleXMLElement extends CMbSimpleXMLElement {
  /**
   * Get the valid elements to handle
   * 
   * @return array A list of the node names to handle
   */
  function getValidPatterns(){
    return array("segment", "group");
  }
  
  /**
   * Reset the current element's counters
   * 
   * @return void
   */
  function reset(){
    $this->attributes()->mbOccurences = 0;
    $this->attributes()->mbOpen = 0;
    //$this->attributes()->mbEmpty = 0;
  }
  
  /**
   * Tells whether the current element is required
   * 
   * @return boolean Required or not
   */
  function isRequired(){
    return (string)$this->attributes()->minOccurs !== "0";
  }
  
  /**
   * Tells whether the current element is unbounded (can occur more than once)
   * 
   * @return boolean Unbounded or not
   */
  function isUnbounded(){
    return (string)$this->attributes()->maxOccurs === "unbounded";
  }
  
  /**
   * Tells whether the current element is forbidden (should never appear)
   * 
   * @return boolean Forbidden or not
   */
  function isForbidden(){
    return (string)$this->attributes()->forbidden === "true";
  }
  
  /**
   * Obligé de mettre les prop de cadinalité et d'ouverture en attributs sinon ca créé des enfants
   * 
   * @return void
   */
  private function init(){
    if (!isset($this->attributes()->mbOccurences)) {
      $this->addAttribute("mbOccurences", 0);
    }
      
    if (!isset($this->attributes()->mbOpen)) {
      $this->addAttribute("mbOpen", 0);
    }
      
    if (!isset($this->attributes()->mbEmpty)) {
      $this->addAttribute("mbEmpty", 0);
    }
  }
  
  /**
   * Found cardinality
   * 
   * @return integer The number of occurences of the current node
   */
  function getOccurences(){
    return (int)$this->attributes()->mbOccurences;
  }
  
  /**
   * If the current group is "open"
   * 
   * @return boolean Open or not
   */
  function isOpen(){
    return (string)$this->attributes()->mbOpen == 1;
  }
  
  /**
   * If the group is "used"
   * 
   * @return boolean Used or not
   */
  function isUsed(){
    return $this->getOccurences() > 0;
  }
  
  /**
   * If the group is "empty"
   * 
   * @return boolean Empty or not
   */
  function isEmpty(){
    return (string)$this->attributes()->mbEmpty !== "0";
  }
  
  /** 
   * Marks the element as "open" and all its children
   * 
   * @return void
   */
  function markOpen(){
    $this->init();
    $this->attributes()->mbOpen = 1;
    
    $parent = $this->getParent();
    
    if ($parent && $parent->getName() !== "message") {
      $parent->markOpen();
    }
  }
  
  /** 
   * Marks the current element as "used"
   * 
   * @return void
   */
  function markUsed(){
    $this->init();
    $this->markOpen();
    $this->attributes()->mbOccurences = $this->getOccurences()+1;
  }
  
  /** 
   * Marks the current element as NOT "empty", and all its ancestors
   * 
   * @return void
   */
  function markNotEmpty(){
    $this->init();
    $this->attributes()->mbEmpty = 0;
    
    $parent = $this->getParent();
    
    if ($parent && $parent->getName() !== "message") {
      $parent->markNotEmpty();
    }
  }
  
  /** 
   * Marks the current element as "empty"
   * 
   * @return void
   */
  function markEmpty(){
    $this->init();
    $this->attributes()->mbEmpty = 1;
  }
  
  /**
   * Get the segment's header content as a string
   * 
   * @return string The segment's header content as a string
   */
  function getSegmentHeader(){
    if ($this->getName() === "segment") {
      return (string)$this;
    }
  }
  
  /**
   * Get the element's items
   * 
   * @return array The array of items
   */
  function getItems(){
    $items = array();
    foreach ($this->elements->field as $field) {
      $items[] = $field;
    }
    return $items;
  }
  
  /**
   * Get a quick view of the current element
   * 
   * @return string The view
   */
  function state(){
    return "[occ:".$this->getOccurences().", ".
            "open:".($this->isOpen()?"true":"false").", ".
            "empty:".($this->isEmpty()?"true":"false")."]";
  }
}
