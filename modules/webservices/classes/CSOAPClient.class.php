<?php

/**
 * SOAPClient Factory
 *  
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * The factory for the SOAP Clients
 */
class CSOAPClient {
  
  /**
   * The type of the client
   * @var string
   */
  public $type_client;
  
  /**
   * The SOAP client
   * @var CMbSOAPClient | CNuSOAPClient
   */
  public $client;
  
  /**
   * The constructor
   * 
   * @param string $type The client type
   * 
   * @return self
   */
  function __construct($type = "CMbSOAPClient") {
    $this->type_client = $type;
  }

  /**
   * Test if the WSDL is reachable, and create the object SOAPClient
   *
   * @param string  $rooturl        The url of the WSDL
   * @param string  $login          The login
   * @param string  $password       The password
   * @param string  $type           Exchange type
   * @param array   $options        The options
   * @param boolean $loggable       Log the exchanges
   * @param string  $stream_context HTTP method (GET, POST, HEAD, PUT, ...)
   * @param string  $local_cert     Path of the certifacte
   * @param string  $passphrase     Pass phrase for the certificate
   *
   * @throws CMbException
   *
   * @return CMbSOAPClient | CNuSOAPClient
   */
  public function make(
      $rooturl, $login = null,
      $password = null, $type = null,
      $options = array(),
      $loggable = null,
      $stream_context = null,
      $local_cert = null,
      $passphrase = null
  ) {
    if (!url_exists($rooturl, $stream_context)) {
      throw new CMbException("CSourceSOAP-unreachable-source", $rooturl);
    }

    if (($login && $password) || (array_key_exists('login', $options) && array_key_exists('password', $options))) {
      if (preg_match('#\%u#', $rooturl)) {
        $rooturl = str_replace('%u', $login ? $login : $options['login'], $rooturl);
      }
    
      if (preg_match('#\%p#', $rooturl)) {
        $rooturl = str_replace('%p', $password ? $password : $options['password'], $rooturl);
      }
    }

    switch ($this->type_client) {
      case 'CNuSOAPClient' :
        $this->client = new CNuSOAPClient($rooturl, $type, $options, $loggable, $local_cert, $passphrase);
        break;
        
      default :
        $this->client = new CMbSOAPClient($rooturl, $type, $options, $loggable, $local_cert, $passphrase);
        break;
    }
    
    if (!$this->client) {
      throw new CMbException("CSourceSOAP-soapclient-impossible");
    }
    
    return $this->client;
  }

  /** Calls a SOAP function
   *
   * @param string $function_name   The name of the SOAP function to call
   * @param array  $arguments       An array of the arguments to pass to the function
   * @param array  $options         An associative array of options to pass to the client
   * @param mixed  $input_headers   An array of headers to be sent along with the SOAP request
   * @param array  &$output_headers If supplied, this array will be filled with the headers from the SOAP response
   *
   * @throws Exception|SoapFault
   *
   * @return mixed SOAP functions may return one, or multiple values
   */
  public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
    return $this->call($function_name, $arguments, $options, $input_headers, $output_headers);
  }

  /** Calls a SOAP function
   *
   * @param string $function_name   The name of the SOAP function to call
   * @param array  $arguments       An array of the arguments to pass to the function
   * @param array  $options         An associative array of options to pass to the client
   * @param mixed  $input_headers   An array of headers to be sent along with the SOAP request
   * @param array  &$output_headers If supplied, this array will be filled with the headers from the SOAP response
   *
   * @throws Exception|SoapFault
   *
   * @return mixed SOAP functions may return one, or multiple values
   */
  public function call($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
    $client = $this->client;
    
    if (!is_array($arguments)) {
      $arguments = array($arguments);
    }
    
    /* @todo Lors d'un appel d'une m�thode RPC le tableau $arguments contient un �lement vide array( [0] => )
     * posant probl�me lors de l'appel d'une m�thode du WSDL sans argument */
    if (isset($arguments[0]) && empty($arguments[0])) {
      $arguments = array();
    }

    if ($client->flatten && isset($arguments[0]) && !empty($arguments[0])) {
      $arguments = $arguments[0];
    }
    
    if (!$client->loggable) {
      try {
        return $client->call($function_name, $arguments, $options, $input_headers, $output_headers);
      } 
      catch(SoapFault $fault) {
        throw $fault;
      }
    }
    
    $output = null;
    
    $echange_soap = new CEchangeSOAP();
    
    $echange_soap->date_echange = CMbDT::dateTime();
    $echange_soap->emetteur     = CAppUI::conf("mb_id");
    $echange_soap->destinataire = $client->wsdl_url;
    $echange_soap->type         = $client->type_echange_soap;

    $url  = parse_url($client->wsdl_url);
    $path = explode("/", $url['path']);
    $echange_soap->web_service_name = end($path);
    
    $echange_soap->function_name = $function_name;
    
    // Truncate input and output before storing
    $arguments_serialize = array_map_recursive(array('CSOAPClient', "truncate"), $arguments);
    
    $echange_soap->input = serialize($arguments_serialize);
    
    $echange_soap->store();
    
    CApp::$chrono->stop();
    $chrono = new Chronometer();
    $chrono->start();

    try {
      $output = $client->call($function_name, $arguments, $options, $input_headers, $output_headers);
    } 
    catch(SoapFault $fault) {
      // trace
      if (CAppUI::conf("webservices trace")) {
        $client->getTrace($echange_soap);
      }
      
      $echange_soap->date_echange = CMbDT::dateTime();
      $echange_soap->output       = $fault->faultstring;
      $echange_soap->soapfault    = 1;
      $echange_soap->store();
      
      CApp::$chrono->start();
      
      throw $fault;
    }
    
    $chrono->stop();
    CApp::$chrono->start();
    $echange_soap->date_echange = CMbDT::dateTime();
    // trace
    if (CAppUI::conf("webservices trace")) {
      $client->getTrace($echange_soap);
    }
    
    // response time
    $echange_soap->response_time = $chrono->total;

    if ($echange_soap->soapfault != 1) {
      $echange_soap->output = serialize(array_map_recursive(array("CSOAPClient", "truncate"), $output));
    }
    $echange_soap->store();
    
    return $output;
  }

  /**
   * Truncate a string to a given maximum length
   *
   * @param string $string The string to truncate
   *
   * @return string The truncated string
   */
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
   * Return the list of the operation of the WSDL
   * 
   * @return array An array who contains all the operation of the WSDL
   */
  public function getFunctions() {
    return $this->client->__getFunctions();
  }
  
  /**
   * Returns an array of functions described in the WSDL for the Web service. 
   * 
   * @return array The array of SOAP function prototype
   */
  public function getTypes() {
    return $this->client->__getTypes();
  } 
  
  /**
   * Defines headers to be sent along with the SOAP requests
   * 
   * @param array $soapheaders The headers to be set
   * 
   * @return boolean True on success or False on failure 
   */
  public function setHeaders($soapheaders) {
    $this->client->setHeaders($soapheaders);
  }

  /**
   * Check service availability
   *
   * @throws CMbException
   *
   * @return void
   */
  public function checkServiceAvailability() {
  }
}
