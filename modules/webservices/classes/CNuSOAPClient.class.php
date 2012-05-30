<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage webservices
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

CAppUI::requireLibraryFile("NuSOAP/nusoap", false);

if (!class_exists("nusoap_client", false)) {
  return;
}

class CNuSOAPClient extends nusoap_client {
  var $type_echange_soap  = null;
  var $loggable           = null;
  
  /**
   * 
   * @param wsdl string The URL of the wsdl file
   * @param type_echange string The type of exchange
   * @param encoding string The type of encoding
   * @param loggable boolean True if you want to log all the exchanges with the web service
   * @param flatten boolean
   */
  function __construct($wsdl, $type_echange  = null, $encoding = null, $loggable = null, $local_cert = null, $passphrase = null) {
    
    if ($loggable) {
      $this->loggable = $loggable;
    }
    
    if ($type_echange) {
      $this->type_echange_soap = $type_echange;
    }
    
    if ($encoding) {
      $this->soap_defencoding = $encoding;
      if ($encoding == "UTF-8") {
        $this->decode_utf8 = false;
      }
    }
    
    if ($local_cert) {
      $this->certRequest = array(
        "sslcertfile" => $local_cert
      );
    }
    
    if ($passphrase) {
      $this->certRequest = array(
        "passphrase" => $passphrase
      );
    }
    
    parent::__construct($wsdl, true);
  }
  
  /**
   * This function perform a nusoap_client call, and log (or not) the results
   * @param function_name string The name of the operation
   * @param arguments array The arguments for the operation
   * @return string The result of the operation
   */
  public function call($function_name, $arguments) {
    global $phpChrono; 

    if (isset($arguments[0]) && empty($arguments[0])) {
      $arguments = array();
    }
    
    if (!$this->loggable) {
      return parent::call($function_name, $arguments);
      
      // Traitement des Erreurs :
      if ($this->fault) {
        throw new CMbException($this->faultstring);
      } elseif ($this->getError()) {
        throw new CMbException($this->getError());
      }
    } else {
      $echange_soap = new CEchangeSOAP();
      
      $echange_soap->emetteur     = CAppUI::conf("mb_id");
      $echange_soap->destinataire = $this->endpoint;
      $echange_soap->type         = $this->type_echange_soap;
      
      $url = parse_url($this->endpoint);
      $path = explode("/", $url['path']);
      $echange_soap->web_service_name = end($path);
      
      $echange_soap->function_name = $function_name;
      
      $phpChrono->stop();
      $chrono = new Chronometer();
      $chrono->start();

      $output = parent::call($function_name, $arguments);

      // Gestion des erreurs :
      if ($this->fault || $this->getError()) {
        if (CAppUI::conf("webservices trace")) {
          $echange_soap->trace                  = true;
          $echange_soap->last_request_headers   = $this->requestHeaders;
          $echange_soap->last_request           = $this->request;
          $echange_soap->last_response_headers  = $this->responseHeader;
          $echange_soap->last_response          = $this->response;
        }
        
        if ($this->fault) {
          $errorString = $this->faultstring;
        } elseif($this->getError()) {
          $errorString = $this->getError();
        }
        
        $echange_soap->date_echange = mbDateTime();
        $echange_soap->output       = $errorString;
        $echange_soap->soapfault    = 1;
        $echange_soap->store();
        
        $phpChrono->start();
        
        throw new CMbException($errorString);
      }
      
      $chrono->stop();
      $phpChrono->start();
      $echange_soap->date_echange = mbDateTime();
      
      if (CAppUI::conf("webservices trace")) {
        $echange_soap->trace                  = true;
        $echange_soap->last_request_headers   = $this->requestHeaders;
        $echange_soap->last_request           = $this->request;
        $echange_soap->last_response_headers  = $this->responseHeader;
        $echange_soap->last_response          = $this->response;
      }
      
      $echange_soap->response_time = $chrono->total;
      
      $echange_soap->input = serialize(array_map_recursive(array($this, "truncate"), $arguments));
      
      if ($echange_soap->soapfault != 1) {
        $echange_soap->output = serialize(array_map_recursive(array($this, "truncate"), $output));
      }
      $echange_soap->store();
      
      return $output;
    }
  }
  
  static public function truncate($string) {
    if (!is_string($string)) {
      return $string;
    }

    // Truncate
    $max = 1024;    
    $result = CMbString::truncate($string, $max);
    
    // Indicate true size
    $length = strlen($string);
    if ($length > 1024) {
      $result .= " [$length bytes]";
    }
    
    return $result;
  }
  
  /**
   * Test if the wsdl is reachable, valid, and create a CNuSOAPClient
   * @param wsdl string The URL of the wsdl file
   * @param type_echange string The type of exchange
   * @param encoding string The type of encoding
   * @param loggable boolean True if you want to log all the exchanges with the web service
   * @param login string The login for accessing the web service
   * @param password string The password for accessing the web service
   * @param local_cert string local_cert must be in PEM format
   * @param passphrase  string Pass Phrase (password) of private key
   * @return CNuSOAPClient
   */
  static public function make($wsdl, $type_echange = null, $encoding = null, $loggable = null, $login = null, $password = null, $local_cert = null, $passphrase = null) {
    if (!url_exists($wsdl)) {
      throw new CMbException("CSourceSOAP-unreachable-source", $wsdl);
    }
    
    if (!$html = file_get_contents($wsdl)) {
      throw new CMbException("CSourceSOAP-unable-to-parse-url", $wsdl);
    }
    
    if (strpos($html, "<?xml") === false) {
      throw new CMbException("CSourceSOAP-wsdl-invalid");
    }
    
    $client = new CNuSOAPClient($wsdl, $type_echange, $encoding, $loggable, $local_cert, $passphrase);
    
    // Gestion des mots de passe
    if ($login && $password) {
      $this->setCredentials($login, $password);
    }
    
    if ($client->getError()) {
      throw new CMbException("CSourceSOAP-unreachable-source", $this->name);
    }
    
    return $client;
  }
}
?>