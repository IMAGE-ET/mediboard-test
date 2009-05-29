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
	
  function __construct($rooturl, $type = null, $options = null) {
  	$this->wsdl = $rooturl;
  	
  	if ($type) {
  		$this->type_echange_soap = $type;
  	}
  	
    if (!$html = file_get_contents($this->wsdl)) {
    	trigger_error("Impossible d'analyser l'url : ".$this->wsdl, E_USER_ERROR);
    	return;
    }
    if (strpos($html, "<?xml") === false) {
      trigger_error("Erreur de connexion sur le service web. WSDL non accessible ou au mauvais format.", E_USER_ERROR);
      return;
    }
    
    parent::__construct($this->wsdl, $options ? $options : array());
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
  	$output = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
  	$echange_soap->output = serialize($output);
  	$echange_soap->store();
  	
  	return $output;
  }
  
  static public function make($rooturl, $login = null, $password = null, $type = null, $options = null) {
  	if ($login && $password) {
  		if (preg_match('#\%u#', $rooturl)) 
        $rooturl = str_replace('%u', $login, $rooturl);
    
	    if (preg_match('#\%p#', $rooturl)) 
	      $rooturl = str_replace('%p', $password, $rooturl);
  	}
    
    if (!$client = new CMbSOAPClient($rooturl, $type, $options)) {
      trigger_error("Instanciation du SoapClient impossible.");
    }
    return $client;
  }
}

?>