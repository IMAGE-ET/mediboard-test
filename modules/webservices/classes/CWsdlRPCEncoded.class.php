<?php

/**
 * Format RPC Encoded
 *  
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CWSDLRPCEncoded
 * Format RPC Encoded
 */
class CWSDLRPCEncoded extends CWSDLRPC {
  function addMessage($functions, $returns = array()) {
    $definitions = $this->documentElement;
    $this->addComment($definitions, "Partie 3 : Messages");
    
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
      
      if (!empty($returns) && array_key_exists($nameFunction, $returns)) {
        foreach ($returns[$nameFunction] as $returnName => $returnValue) {
          $part = $this->addElement($message, "part");
          $this->addAttribute($part, "name", $returnName);
          $this->addAttribute($part, "type", "xsd:".$this->xsd[$returnValue]);
        }
      } 
      else {
        $part = $this->addElement($message, "part");
        $this->addAttribute($part, "name", "return");
        $this->addAttribute($part, "type", "xsd:".$this->xsd["string"]);
      }
    }
  }
  
  function addPortType($functions) {
    $definitions = $this->documentElement;
    $this->addComment($definitions, "Partie 4 : Port Type");
    
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
    $this->addComment($definitions, "Partie 6 : Binding");
    
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
}