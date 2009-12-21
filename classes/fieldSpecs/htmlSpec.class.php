<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CHtmlSpec extends CMbFieldSpec {
  function getSpecType() {
    return "html";
  }
  
  function getDBSpec(){
    return "MEDIUMTEXT";
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    return $object->{$this->fieldName};
  }
  
  function checkProperty($object){
    $propValue = $object->{$this->fieldName};
    
    // Root node surrounding
    $source = utf8_encode("<div>$propValue</div>");
    
    // Entity purge
    $source = preg_replace("/&\w+;/i", "", $source);
    
    // Escape warnings, returns false if really invalid
    if (!@DOMDocument::loadXML($source)) {
      return "Le document HTML est mal formé, ou la requête n'a pas pu se terminer.";
    }
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = <<<EOD
<h1>Titre 1</h1>
<p>Paragraphe</p>
<ul>
  <li>Item 1</li>
  <li>Item 2</li>
  <li>Item 3</li>
</ul>
EOD;
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
}

?>