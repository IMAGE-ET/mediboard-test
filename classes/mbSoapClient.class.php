<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CMbSOAPClient class
 */
class CMbSOAPClient extends SoapClient {
	var $wsdl              = null;
	var $type_echange_soap = null;
	var $soap_client_error = false;
	
  function __construct($rooturl, $type = null, $options = array()) {
  	$this->wsdl = $rooturl;
  	
  	if ($type) {
  		$this->type_echange_soap = $type;
  	}
  	
    if (!$html = file_get_contents($this->wsdl)) {
    	$this->soap_client_error = true;
    	//trigger_error("Impossible d'analyser l'url : ".$this->wsdl, E_USER_ERROR);
    	return;
    }
    if (strpos($html, "<?xml") === false) {
    	$this->soap_client_error = true;
      //trigger_error("Erreur de connexion sur le service web. WSDL non accessible ou au mauvais format.", E_USER_ERROR);
      return;
    }
    
    $options = array_merge($options, array("connexion_timeout" => CAppUI::conf("webservices connection_timeout")));
    
    parent::__construct($this->wsdl, $options);
  }
  
  public function __call($function_name, $arguments) {
    return $this->__soapCall($function_name, $arguments);   
  }
    
  public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
  	$echange_soap = new CEchangeSOAP();
  	$echange_soap->date_echange = mbDateTime();
  	$echange_soap->emetteur = CAppUI::conf("mb_id");
  	$echange_soap->destinataire = $this->wsdl;
  	$echange_soap->type = $this->type_echange_soap;

		$url = parse_url($this->wsdl);
		$path = explode("/",$url['path']);
  	$echange_soap->web_service_name = end($path);
  	
  	$echange_soap->function_name = $function_name;
  	$echange_soap->input = serialize($arguments);
  	try {
  	  $output = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
  	} catch(SoapFault $fault) {
  		$output = $echange_soap->output = $fault->faultstring;
  		$echange_soap->soapfault = 1;
      //trigger_error($fault->faultstring, E_USER_ERROR);
    }
    
    if ($echange_soap->soapfault != 1) {
    	$echange_soap->output = serialize($output);
    }

    $echange_soap->store();
  	
  	return $output;
  }
  
  static public function make($rooturl, $login = null, $password = null, $type = null, $options = array()) {
  	if (!url_exists($rooturl)) {
  		//trigger_error("Impossible d'établir la connexion avec le serveur : ".$rooturl, E_USER_ERROR);
  		return;
  	}

  	if (($login && $password) || (array_key_exists('login', $options) && array_key_exists('password', $options))) {

  		if (preg_match('#\%u#', $rooturl)) 
        $rooturl = str_replace('%u', $login ? $login : $options['login'], $rooturl);
    
	    if (preg_match('#\%p#', $rooturl)) 
	      $rooturl = str_replace('%p', $password ? $password : $options['password'], $rooturl);
  	}

    if (!$client = new CMbSOAPClient($rooturl, $type, $options)) {
      //trigger_error("Instanciation du SoapClient impossible.");
    }
    return $client;
  }
}

?>