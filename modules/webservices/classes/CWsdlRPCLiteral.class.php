<?php

/**
 * Format RPC Literal
 *  
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CWSDLRPCLiteral 
 * Format RPC Literal
 */
class CWSDLRPCLiteral extends CWSDLRPC {
  function addTypes() {
    $definitions = $this->documentElement;
    $partie2 = $this->createComment("partie 2 : Types");
    $definitions->appendChild($partie2);
    $types = $this->addElement($definitions, "types", null, "http://schemas.xmlsoap.org/wsdl/");
    
    $xsd = $this->addElement($types, "xsd:schema", null, "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($xsd, "xmlns", "http://www.w3.org/2001/XMLSchema");
    $this->addAttribute($xsd, "targetNamespace", "urn:MediboardWSDL");
    
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function addMessage($functions, $returns = array()) {
    $definitions = $this->documentElement;
    $partie3 = $this->createComment("partie 3 : Message");
    $definitions->appendChild($partie3);
    
    foreach($functions as $nameFunction => $atts) {
      $message = $this->addElement($definitions, "message", null, "http://schemas.xmlsoap.org/wsdl/");
      $this->addAttribute($message, "name", $nameFunction."Request");
      
      foreach($atts as $attName => $attValue) {
        $part = $this->addElement($message, "part", null, "http://schemas.xmlsoap.org/wsdl/");
        $this->addAttribute($part, "name", $attName);
        $this->addAttribute($part, "type", "xsd:".$this->xsd[$attValue]);
      }
      
      $message = $this->addElement($definitions, "message", null, "http://schemas.xmlsoap.org/wsdl/");
      $this->addAttribute($message, "name", $nameFunction."Response");
      
      if (!empty($returns) && array_key_exists($nameFunction, $returns)) {
        foreach ($returns[$nameFunction] as $returnName => $returnValue) {
          $part = $this->addElement($message, "part", null, "http://schemas.xmlsoap.org/wsdl/");
          $this->addAttribute($part, "name", $returnName);
          $this->addAttribute($part, "type", "xsd:".$this->xsd[$returnValue]);
        }
      } 
      else {
        $part = $this->addElement($message, "part", null, "http://schemas.xmlsoap.org/wsdl/");
        $this->addAttribute($part, "name", "return");
        $this->addAttribute($part, "type", "xsd:".$this->xsd["string"]);
      }
    }
  }
  
  function addPortType($functions) {
    $definitions = $this->documentElement;
    $partie4 = $this->createComment("partie 4 : Port Type");
    $definitions->appendChild($partie4);
    
    $portType = $this->addElement($definitions, "portType", null, "http://schemas.xmlsoap.org/wsdl/");
    $this->addAttribute($portType, "name", "MediboardPort");
    
    foreach($functions as $nameFunction => $atts) {
      $partie5 = $this->createComment("partie 5 : Operation");
      $portType->appendChild($partie5);
      $operation = $this->addElement($portType, "operation", null, "http://schemas.xmlsoap.org/wsdl/");
      $this->addAttribute($operation, "name", $nameFunction);
      
      $input = $this->addElement($operation, "input", null, "http://schemas.xmlsoap.org/wsdl/");
      $this->addAttribute($input, "message", "typens:".$nameFunction."Request");
      
      $output = $this->addElement($operation, "output", null, "http://schemas.xmlsoap.org/wsdl/");
      $this->addAttribute($output, "message", "typens:".$nameFunction."Response");
    }
  }
  
  function addBinding($functions) {
    $definitions = $this->documentElement;
    $partie6 = $this->createComment("partie 6 : Binding");
    $definitions->appendChild($partie6);
    
    $binding = $this->addElement($definitions, "binding", null, "http://schemas.xmlsoap.org/wsdl/");
    $this->addAttribute($binding, "name", "MediboardBinding");
    $this->addAttribute($binding, "type", "typens:MediboardPort");
    
    $soap = $this->addElement($binding, "soap:binding", null, "http://schemas.xmlsoap.org/wsdl/soap/");
    $this->addAttribute($soap, "style", "rpc");
    $this->addAttribute($soap, "transport", "http://schemas.xmlsoap.org/soap/http");

    foreach($functions as $nameFunction => $atts) {
      $operation = $this->addElement($binding, "operation", null, "http://schemas.xmlsoap.org/wsdl/");
      
      $this->addAttribute($operation, "name", $nameFunction);
      
      $soapoperation = $this->addElement($operation, "soap:operation", null, "http://schemas.xmlsoap.org/wsdl/soap/");
      $this->addAttribute($soapoperation, "soapAction", $nameFunction);
      $this->addAttribute($soapoperation, "style", "document");
      
      $input = $this->addElement($operation, "input", null, "http://schemas.xmlsoap.org/wsdl/");
      
      $soapbody = $this->addElement($input, "soap:body", null, "http://schemas.xmlsoap.org/wsdl/soap/");
      $this->addAttribute($soapbody, "use", "literal");
      
      $output = $this->addElement($operation, "output", null, "http://schemas.xmlsoap.org/wsdl/");
      
      $soapbody = $this->addElement($output, "soap:body", null, "http://schemas.xmlsoap.org/wsdl/soap/");
      $this->addAttribute($soapbody, "use", "literal");
    }
  }
}
