<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CWsdlDocument extends CMbXMLDocument {
  private $xsd = array("string"=>"string", "bool"=>"boolean", "boolean"=>"boolean",
                       "int"=>"integer", "integer"=>"integer", "double"=>"double", "float"=>"float", "number"=>"float",
                       "resource"=>"anyType", "mixed"=>"anyType", "unknown_type"=>"anyType", "anyType"=>"anyType", "xml"=>"anyType");
             
  function __construct() {
    parent::__construct();
    $this->documentfilename = "tmp/document.wsdl";
    $entete = $this->createComment("WSDL Mediboard genere permettant de d'ecrire le service web.");
    $this->appendChild($entete);
    $partie1 = $this->createComment("partie 1 : Definitions");
    $this->appendChild($partie1);
    $definitions = $this->addElement($this, "definitions", null, "http://schemas.xmlsoap.org/wsdl/");
    $this->addNameSpaces($definitions);
  } 
 
  function addNameSpaces($elParent) {
    // Ajout des namespace
    $this->addAttribute($elParent, "name", "MediboardWSDL");
    $this->addAttribute($elParent, "targetNamespace", "urn:MediboardWSDL");
    $this->addAttribute($elParent, "xmlns:typens", "urn:MediboardWSDL");
    $this->addAttribute($elParent, "xmlns:xsd", "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($elParent, "xmlns:soap", "http://schemas.xmlsoap.org/wsdl/soap/");
    $this->addAttribute($elParent, "xmlns:soapenc", "http://schemas.xmlsoap.org/soap/encoding/");
    $this->addAttribute($elParent, "xmlns:wsdl", "http://schemas.xmlsoap.org/wsdl/");
  }
  
  function addTexte($elParent, $elName, $elValue, $elMaxSize = 100) {
    $elValue = substr($elValue, 0, $elMaxSize);
    return $this->addElement($elParent, $elName, $elValue);
  }
  
  function addTypes() {
    $definitions = $this->documentElement;
    $partie2 = $this->createComment("partie 2 : Types");
    $definitions->appendChild($partie2);
    $types = $this->addElement($definitions, "types");
    
    $xsd = $this->addElement($types, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($xsd, "xmlns", "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($xsd, "targetNamespace", "urn:MediboardWSDL");
    
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function addMessage($functions) {
    $definitions = $this->documentElement;
    $partie3 = $this->createComment("partie 3 : Message");
    $definitions->appendChild($partie3);
    
    foreach($functions as $nameFunction => $atts) {
      $message = $this->addElement($definitions, "message");
      $this->addAttribute($message, "name", $nameFunction."Request");
      
      foreach($atts as $attName => $attValue) {
        $part = $this->addElement($message, "part");
        $this->addAttribute($part, "name", $attName);
        $this->addAttribute($part, "type", "xsd:".$this->xsd[$attValue]);
      }
      
      $message = $this->addElement($definitions, "message");
      $this->addAttribute($message, "name", $nameFunction."Response");
      
      $part = $this->addElement($message, "part");
      $this->addAttribute($part, "name", "return");
      $this->addAttribute($part, "type", "xsd:".$this->xsd["string"]);
    }
  }
  
  function addPortType($functions) {
    $definitions = $this->documentElement;
    $partie4 = $this->createComment("partie 4 : Port Type");
    $definitions->appendChild($partie4);
    
    $portType = $this->addElement($definitions, "portType");
    $this->addAttribute($portType, "name", "MediboardPort");
    
    foreach($functions as $nameFunction => $atts) {
      $partie5 = $this->createComment("partie 5 : Operation");
      $portType->appendChild($partie5);
      $operation = $this->addElement($portType, "operation");
      $this->addAttribute($operation, "name", $nameFunction);
      
      $input = $this->addElement($operation, "input");
      $this->addAttribute($input, "message", "typens:".$nameFunction."Request");
      
      $output = $this->addElement($operation, "output");
      $this->addAttribute($output, "message", "typens:".$nameFunction."Response");
    }
  }
  
  function addBinding($functions) {
    $definitions = $this->documentElement;
    $partie6 = $this->createComment("partie 6 : Binding");
    $definitions->appendChild($partie6);
    
    $binding = $this->addElement($definitions, "binding");
    $this->addAttribute($binding, "name", "MediboardBinding");
    $this->addAttribute($binding, "type", "typens:MediboardPort");
    
    $soap = $this->addElement($binding, "soap:binding", null, "http://schemas.xmlsoap.org/wsdl/soap/");
    $this->addAttribute($soap, "style", "rpc");
    $this->addAttribute($soap, "transport", "http://schemas.xmlsoap.org/soap/http");

    foreach($functions as $nameFunction => $atts) {
      $operation = $this->addElement($binding, "operation");
      $this->addAttribute($operation, "name", $nameFunction);
      
      $soapoperation = $this->addElement($operation, "soap:operation", null, "http://schemas.xmlsoap.org/wsdl/soap/");
      $this->addAttribute($soapoperation, "soapAction", "MediboardAction");
      
      $input = $this->addElement($operation, "input");
      $this->addAttribute($input, "name", $nameFunction."Request");
      
      $soapbody = $this->addElement($input, "soap:body", null, "http://schemas.xmlsoap.org/wsdl/soap/");
      $this->addAttribute($soapbody, "use", "encoded");
      $this->addAttribute($soapbody, "namespace", "urn:MediboardWSDL");
      $this->addAttribute($soapbody, "encodingStyle", "http://schemas.xmlsoap.org/soap/encoding/");
      
      $output = $this->addElement($operation, "output");
      $this->addAttribute($output, "name", $nameFunction."Reponse");
      
      $soapbody = $this->addElement($output, "soap:body", null, "http://schemas.xmlsoap.org/wsdl/soap/");
      $this->addAttribute($soapbody, "use", "encoded");
      $this->addAttribute($soapbody, "namespace", "urn:MediboardWSDL");
      $this->addAttribute($soapbody, "encodingStyle", "http://schemas.xmlsoap.org/soap/encoding/");
    }
  }
  
  function addService($username, $password, $module, $tab) {
    $definitions = $this->documentElement;
    $partie7 = $this->createComment("partie 7 : Service");
    $definitions->appendChild($partie7);
    
    $service = $this->addElement($definitions, "service");
    $this->addAttribute($service, "name", "MediboardService");
    
    $this->addTexte($service, "documentation", "Documentation du WebService");
    
    $partie8 = $this->createComment("partie 8 : Port");
    $service->appendChild($partie8);
    $port = $this->addElement($service, "port");
    $this->addAttribute($port, "name", "MediboardPort");
    $this->addAttribute($port, "binding", "MediboardBinding");
    
    $soapaddress = $this->addElement($port, "soap:address", null, "http://schemas.xmlsoap.org/wsdl/soap/");
    $this->addAttribute($soapaddress, "location", CAppui::conf("base_url")."/index.php?login=1&username=$username&password=$password&m=$module&a=$tab&suppressHeaders=1");
  }
  
  function saveFileXML() {
    parent::saveXML();
  }
}

?>