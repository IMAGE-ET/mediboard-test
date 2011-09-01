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
    $this->attributes()->mb_open = 0;
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
    if (!isset($this->attributes()->mb_occurences))
      $this->addAttribute("mb_occurences", 0);
      
    if (!isset($this->attributes()->mb_open))
      $this->addAttribute("mb_open", 0);
  }
  
  /**
   * Cardinalité relevée
   * 
   * @return int
   */
  function getOccurences(){
    return (int)$this->attributes()->mb_occurences;
  }
  
  /**
   * Si le groupe est ouvert
   * 
   * @return bool
   */
  function isOpen(){
    return (bool)$this->attributes()->mb_open;
  }
  
  /** 
   * Marque l'element comme ouvert, et tous ses parents
   */
  function markOpen(){
    $this->init();
    
    // @todo a mon avis ce n'est pas ici qu'il faut incrementer
    $this->attributes()->mb_occurences = $this->getOccurences()+1;
    $this->attributes()->mb_open = 1;
    
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
