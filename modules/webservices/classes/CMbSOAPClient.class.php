<?php

/**
 * CMbSOAPClient
 *  
 * @category webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
if (!class_exists("SoapClient")) {
  return;
}

/**
 * The CMbSOAPClient class
 */
class CMbSOAPClient extends SoapClient {
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
    
    // Ajout des options personnalisÈes
    $options = array_merge($options, array("connexion_timeout" => CAppUI::conf("webservices connection_timeout")));
    if (CAppUI::conf("webservices trace")) {
      $options = array_merge($options, array("trace" => true));
    }
    if ($local_cert) {
      $options = array_merge($options, array("local_cert" => $local_cert));
    }
    if ($passphrase) {
      $options = array_merge($options, array("passphrase" => $passphrase));
    }

    parent::__construct($this->wsdl_url, $options);
  }
    
  public function call($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
    try {
      return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
    } 
    catch(SoapFault $fault) {
      throw $fault;
    }
  }
  
  /**
   * Set the request and the response of the exchange and return it
   * 
   * @param CEchangeSOAP $exchange The exchangeSOAP
   * 
   * @return null
   */
  public function getTrace(CEchangeSOAP $exchange) {
    $exchange->trace                 = true;
    $exchange->last_request_headers  = $this->__getLastRequestHeaders();
    $exchange->last_request          = $this->__getLastRequest();
    $exchange->last_response_headers = $this->__getLastResponseHeaders();
    $exchange->last_response         = $this->__getLastResponse();
  }
  
  /**
   * Defines headers to be sent along with the SOAP requests
   * 
   * @param array $soapheaders The headers to be set
   * 
   * @return boolean True on success or False on failure 
   */
  public function setHeaders($soapheaders) {
    $this->__setSoapHeaders($soapheaders);
  } 
}
?>