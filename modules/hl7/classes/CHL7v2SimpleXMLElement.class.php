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
    $this->attributes()->mbOccurences = 0;
    $this->attributes()->mbOpen = 0;
    $this->attributes()->mbUsed = 0;
  }
  
  function isRequired(){
    return (string)$this->attributes()->minOccurs !== "0";
  }
  
  function isUnbounded(){
    return (string)$this->attributes()->maxOccurs === "unbounded";
  }
  
  /**
   * Obligé de mettre les prop de cadinalité et d'ouverture en attributs sinon ca créé des enfants
   * 
   * @return void
   */
  private function init(){
    if (!isset($this->attributes()->mbOccurences))
      $this->addAttribute("mbOccurences", 0);
      
    if (!isset($this->attributes()->mbOpen))
      $this->addAttribute("mbOpen", 0);
      
    if (!isset($this->attributes()->mbUsed))
      $this->addAttribute("mbUsed", 0);
  }
  
  /**
   * Cardinalité relevée
   * 
   * @return int
   */
  function getOccurences(){
    return (int)$this->attributes()->mbOccurences;
  }
  
  /**
   * Si le groupe est ouvert
   * 
   * @return bool
   */
  function isOpen(){
    return (bool)$this->attributes()->mbOpen;
  }
  
  /**
   * Si le groupe est utilisé
   * 
   * @return bool
   */
  function isUsed(){
    return (bool)$this->attributes()->mbUsed;
  }
  
  /** 
   * Marque l'element comme ouvert, et tous ses parents
   */
  function markOpen(){
    $this->init();
    
    // @todo a mon avis ce n'est pas ici qu'il faut incrementer
    $this->attributes()->mbOccurences = $this->getOccurences()+1;
    $this->attributes()->mbOpen = 1;
    
    $parent = $this->getParent();
    
    if ($parent && $parent->getName() !== "message") {
      $parent->markOpen();
    }
  }
  
  /** 
   * Marque l'element comme ouvert, et tous ses parents
   */
  function markUsed(){
    $this->init();
    $this->attributes()->mbUsed = 1;
  }
  
  function getSegmentHeader(){
    if ($this->getName() === "segment") {
      return (string)$this;
    }
  }
  
  function getItems(){
    $items = array();
    foreach($this->elements->field as $field) {
      $items[] = $field;
    }
    return $items;
  }
}
