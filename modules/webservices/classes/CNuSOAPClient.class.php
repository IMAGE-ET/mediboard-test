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

/**
 * The CNuSOAPClient class
 */
class CNuSOAPClient extends nusoap_client {
  var $wsdl_url          = null;
  var $type_echange_soap = null;
  var $soap_client_error = false;
  var $flatten           = null;
  var $loggable          = null;
  var $encoding          = null;
  
  /**
   * The constructor
   * 
   * @param string  $rooturl    The URL of the wsdl file
   * @param string  $type       The type of exchange
   * @param array   $options    An array of options
   * @param boolean $loggable   True if you want to log all the exchanges with the web service
   * @param string  $local_cert Path of the certifacte
   * @param string  $passphrase Pass phrase for the certificate
   */
  function __construct($rooturl, $type = null, $options = array(), $loggable = null, $local_cert = null, $passphrase = null) {
    $this->wsdl_url = $rooturl;

    if ($loggable) {
      $this->loggable = $loggable;
    }
    
    if ($type) {
      $this->type_echange_soap = $type;
    }
    
    if (!$html = file_get_contents($this->wsdl_url)) {
      $this->soap_client_error = true;
      throw new CMbException("CSourceSOAP-unable-to-parse-url", $this->wsdl_url);
    }

    if (strpos($html, "<?xml") === false) {
      $this->soap_client_error = true;
      throw new CMbException("CSourceSOAP-wsdl-invalid");
    }
    
    if (array_key_exists("encoding", $options)) {
      $encoding = $options["encoding"];
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

    parent::__construct($rooturl, true);
    $this->wsdl_url = $rooturl;
  }
  
  /**
   * This function perform a nusoap_client call, and log (or not) the results
   * @param function_name string The name of the operation
   * @param arguments array The arguments for the operation
   * @return string The result of the operation
   */
  public function call($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {      
    $output = parent::call($function_name, $arguments);
    
    if ($this->fault) {
      throw new SoapFault($this->faultcode, $this->faultstring, $this->fault->faultactor, $this->faultdetail);
    } 
    elseif ($this->getError()) {
      throw new SoapFault(-1, $this->getError());
    }
    
    return $output;
  }
  
  /**
   * Set the request and the response of the exchange and return it
   * 
   * @param CEchangeSOAP $exchange The exchangeSOAP
   * 
   * @return CEchangeSOAP
   */
  public function getTrace(CEchangeSOAP $exchange) {
    $exchange->trace                 = true;
    $exchange->last_request_headers   = $this->requestHeaders;
    $exchange->last_request           = $this->request;
    $exchange->last_response_headers  = $this->responseHeader;
    $exchange->last_response          = $this->response;
    
    return $exchange;
  }
  
  /**
   * Return the list of the operation of the WSDL
   * 
   * @return array An array who contains all the operation of the WSDL
   */
  public function __getFunctions() {
    $wsdl = new wsdl($this->wsdl);
    $operations = $wsdl->getOperations();
    $return = array();
    
    foreach ($operations as $_operation) {
      $output = $_operation['output']['parts'];
      $output_type = 'void';
      if (array_key_exists('return', $output)) {
        $output_type = end(explode(':', $output['return']));
      }
      $name = $_operation['name'];
      
      $input = array();
      
      foreach ($_operation['input']['parts'] as $_param => $_type) {
        $input[] = end(explode(':', $_type)) . ' $' . $_param;
      }
      $input = join(', ', $input);
      
      $return[] = "$output_type $name($input)";
    }
    mbTrace($return);
    return $return;
  }
  
  /**
   * Returns an array of functions described in the WSDL for the Web service. 
   * 
   * @return array The array of SOAP function prototype
   */
  public function __getTypes() {
    /* TODO Retourner les types */
    return array();
  } 
  
  /**
   * Defines headers to be sent along with the SOAP requests
   * 
   * @param array $soapheaders The headers to be set
   * 
   * @return boolean True on success or False on failure 
   */
  public function setHeaders($soapheaders) {
  } 
}
?>