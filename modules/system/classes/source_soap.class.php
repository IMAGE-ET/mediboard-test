<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSourceSOAP extends CExchangeSource {
  // DB Table key
  var $source_soap_id = null;
  
  // DB Fields
  var $wsdl_mode        = null;
	var $evenement_name   = null;
	var $single_parameter = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_soap';
    $spec->key   = 'source_soap_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["wsdl_mode"]        = "bool default|1";
		$specs["evenement_name"]   = "str";
		$specs["single_parameter"] = "str";
    return $specs;
  }
  
  function send($evenement_name = null, $flatten = false) {
  	if (!$evenement_name) {
  		$evenement_name = $this->evenement_name;
  	}
		
		if (!$evenement_name) {
		  throw new CMbException("CSourceSOAP-no-evenement", $this->name);
		}
		
		$this->_client = CMbSOAPClient::make($this->host, $this->user, $this->password, $this->type_echange);
    if ($this->_client->soap_client_error) {
      throw new CMbException("CSourceSOAP-unreachable-source", $this->name);
    }
    // Applatissement du tableau $arguments qui contient un élement vide array([0] => ...) ?
    $this->_client->flatten = $flatten;
    
    if ($this->single_parameter) {
      $this->_data = array("$this->single_parameter" => $this->_data);
    }
    
    if (!$this->_data) {
      $this->_data = array();
    }

    $this->_acquittement = $this->_args_list ? 
        call_user_func_array(array($this->_client, $evenement_name), $this->_data) : 
        $this->_client->$evenement_name($this->_data);
    
    if (is_object($this->_acquittement)) {
      $acquittement = (array) $this->_acquittement;
      if (count($acquittement) == 1) {
        $this->_acquittement = reset($acquittement);
      } 
    }
    
		return true;
  }
  
  function receive() {    
    return $this->_acquittement;
  }
}
?>