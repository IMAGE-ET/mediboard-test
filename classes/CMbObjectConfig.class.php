<?php

/**
 * Object Config
 *  
 * @category IHE
 * @subpackage classes
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

/**
 * Object config
 */
class CMbObjectConfig extends CMbObject {
  /**
   * Load object config
   *
   * @return CMbObjectConfig
   */
  function loadRefObject() {
    return $this->loadFwdRef("object_id");
  }
  
  /**
   * Export object config
   *
   * @return CMbXMLDocument
   */
  function exportXMLConfigValues(){
    $doc = new CMbXMLDocument();
    $root = $doc->addElement($doc, $this->_class);
    
    foreach ($this->getConfigValues() as $key => $value) {
      $node = $doc->addElement($root, "entry");
      $node->setAttribute("config", $key);
      $node->setAttribute("value", $value);
    }
    
    return $doc;
  }
  
  /**
   * Import object config
   *
   * @return void
   */
  function importXMLConfigValues(){
  }
}
  