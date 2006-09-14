<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

if (!class_exists("DOMDocument")) {
  trigger_error("sorry, DOMDocument is needed");
  return;
}

if (!class_exists("CMbXMLDocument")) {
  //trigger_error("sorry, DOMDocument is needed");
  return;
}

global $AppUI, $m;

class CHPrimXMLSchema extends CMbXMLDocument {
  function __construct() {
    parent::__construct();
    
    $root = $this->addElement($this, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($root, "xmlns", "http://www.hprim.org/hprimXML");
    $this->addAttribute($root, "xmlns:insee", "http://www.hprim.org/inseeXML");
    $this->addAttribute($root, "targetNamespace", "http://www.hprim.org/hprimXML");
    $this->addAttribute($root, "elementFormDefault", "qualified");
    $this->addAttribute($root, "attributeFormDefault", "unqualified");
  }
  
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

  function purgeImportedNamespaces() {
    $xpath = new domXPath($this);
    foreach ($xpath->query('//*[@type]') as $node) {
      $matches = null;
      if (preg_match("/insee:(.*)/", $node->getAttribute("type"), $matches)) {
        $node->setAttribute("type", $matches[1]);
      }
    }
  }
}

?>
