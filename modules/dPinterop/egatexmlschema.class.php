<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Sébastien Fillonneau
*/


CAppUI::requireModuleClass("dPinterop", "mbxmldocument");
CAppUI::requireModuleClass("dPinterop", "mbxmlschema");

if (!class_exists("CMbXMLDocument") || !class_exists("CMbXMLSchema")) {
  return;
}

class CEGateXMLSchema extends CMbXMLSchema {  
  function __construct() {
    parent::__construct();
    
    $root = $this->addElement($this, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($root, "xmlns", "http://www.capio.com");
    $this->addAttribute($root, "targetNamespace", "http://www.capio.com");
    $this->addAttribute($root, "elementFormDefault", "qualified");
    $this->addAttribute($root, "attributeFormDefault", "unqualified");
  }
}

?>