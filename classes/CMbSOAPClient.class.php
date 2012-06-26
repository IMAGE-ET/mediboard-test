<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if (!class_exists("SoapClient")) {
  return;
}

/**
 * The CMbSOAPClient class
 */
class CMbSOAPClient extends SoapClient {
  var $wsdl              = null;
  var $type_echange_soap = null;
  var $soap_client_error = false;
  var $flatten           = null;
  var $loggable          = null;
  var $encoding          = null;
  
  function __construct($rooturl, $type = null, $options = array(), $loggable = null, $local_cert = null, $passphrase = null) {
    $this->wsdl = $rooturl;

    if ($loggable) {
      $this->loggable = $loggable;
    }
    
    if ($type) {
      $this->type_echange_soap = $type;
    }
    
    if (!$html = file_get_contents($this->wsdl)) {
      $this->soap_client_error = true;
      throw new CMbException("CSourceSOAP-unable-to-parse-url", $this->wsdl);
    }

    if (strpos($html, "<?xml") === false) {
      $this->soap_client_error = true;
      throw new CMbException("CSourceSOAP-wsdl-invalid");
    }
    
    // Ajout des options personnalisées
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

    parent::__construct($this->wsdl, $options);
  }
  
  public function __call($function_name, $arguments) {
    return $this->__soapCall($function_name, $arguments);   
  }
    
  public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
    global $phpChrono;

    /* @todo Lors d'un appel d'une méthode RPC le tableau $arguments contient un élement vide array( [0] => )
     * posant problème lors de l'appel d'une méthode du WSDL sans argument */
    if (isset($arguments[0]) && empty($arguments[0])) {
      $arguments = array();
    }

    if ($this->flatten && isset($arguments[0]) && !empty($arguments[0])) {
      $arguments = $arguments[0];
    }

    if (!$this->loggable) {
      try {
        return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
      } 
      catch(SoapFault $fault) {
        throw $fault;
      }
    }
    
    $output = null;
    
    $echange_soap = new CEchangeSOAP();
    
    $echange_soap->emetteur     = CAppUI::conf("mb_id");
    $echange_soap->destinataire = $this->wsdl;
    $echange_soap->type         = $this->type_echange_soap;

    $url  = parse_url($this->wsdl);
    $path = explode("/",$url['path']);
    $echange_soap->web_service_name = end($path);
    
    $echange_soap->function_name = $function_name;
    
    // Truncate input and output before storing
    $arguments_serialize = array_map_recursive(array("CMbSOAPClient", "truncate"), $arguments);
    
    $echange_soap->input = serialize($arguments_serialize);
    
    $echange_soap->store();
    
    $phpChrono->stop();
    $chrono = new Chronometer();
    $chrono->start();

    try {
      $output = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
    } 
    catch(SoapFault $fault) {
      // trace
      if (CAppUI::conf("webservices trace")) {
        $echange_soap->trace                 = true;
        $echange_soap->last_request_headers  = $this->__getLastRequestHeaders();
        $echange_soap->last_request          = $this->__getLastRequest();
        $echange_soap->last_response_headers = $this->__getLastResponseHeaders();
        $echange_soap->last_response         = $this->__getLastResponse();
      }
      
      $echange_soap->date_echange = mbDateTime();
      $echange_soap->output       = $fault->faultstring;
      $echange_soap->soapfault    = 1;
      $echange_soap->store();
      
      $phpChrono->start();
      
      throw $fault;
    }
    $chrono->stop();
    $phpChrono->start();
    $echange_soap->date_echange = mbDateTime();
    // trace
    if (CAppUI::conf("webservices trace")) {
      $echange_soap->trace                 = true;
      $echange_soap->last_request_headers  = $this->__getLastRequestHeaders();
      $echange_soap->last_request          = $this->__getLastRequest();
      $echange_soap->last_response_headers = $this->__getLastResponseHeaders();
      $echange_soap->last_response         = $this->__getLastResponse();
    }
    
    // response time
    $echange_soap->response_time = $chrono->total;

    if ($echange_soap->soapfault != 1) {
      $echange_soap->output = serialize(array_map_recursive(array("CMbSOAPClient", "truncate"), $output));
    }
    $echange_soap->store();
    
    return $output;
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
  
  static public function make($rooturl, $login = null, $password = null, $type = null, $options = array(), $loggable = null, $stream_context = null, $local_cert = null, $passphrase = null) {
    if (!url_exists($rooturl, $stream_context)) {
      throw new CMbException("CSourceSOAP-unreachable-source", $rooturl);
    }

    if (($login && $password) || (array_key_exists('login', $options) && array_key_exists('password', $options))) {

      if (preg_match('#\%u#', $rooturl)) 
        $rooturl = str_replace('%u', $login ? $login : $options['login'], $rooturl);
    
      if (preg_match('#\%p#', $rooturl)) 
        $rooturl = str_replace('%p', $password ? $password : $options['password'], $rooturl);
    }

    if (!$client = new CMbSOAPClient($rooturl, $type, $options, $loggable, $local_cert, $passphrase)) {
      throw new CMbException("CSourceSOAP-soapclient-impossible");
    }
    
    return $client;
  }
}

?>