<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbXMLSchema extends CMbXMLDocument {
  function addSchemaPart($filePath) {
    $schemaPart = new DomDocument;
    $schemaPart->load($filePath);
    
    // Select all child elements of schemaPart XML
    // And pump them into main schema
    $xpath = new domXPath($schemaPart);
    foreach ($xpath->query('/*/*') as $node) {
      $element = $this->importNode($node, true);
      $this->documentElement->appendChild($element);
    }
  }

  function importSchemaPackage($dirPath) {
    foreach (glob("$dirPath/*.xsd") as $fileName) {
      $this->addSchemaPart($fileName);
    }
  }
  
  function purgeIncludes() {
    $xpath = new domXPath($this);
    foreach ($xpath->query('/*/xsd:import | /*/xsd:include') as $node) {
      $node->parentNode->removeChild($node);
    }
  }
}

?>
