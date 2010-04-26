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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_soap';
    $spec->key   = 'source_soap_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["wsdl_mode"] = "bool default|1";
		$specs["evenement_name"] = "str";
    return $specs;
  }
  
  function send($evenement_name = null) {
  	if (!$evenement_name) {
  		$evenement_name = $this->evenement_name;
  	}
		
		if (!$evenement_name) {
			CAppUI::stepAjax("Aucune méthode définie pour l'appel SOAP.", UI_MSG_ERROR);
		}
		
		$this->_client = CMbSOAPClient::make($this->host, $this->user, $this->password, $this->type_echange);
    if ($this->_client->soap_client_error) {
      CAppUI::stepAjax("Impossible de joindre la source de donnée : '$this->name'", UI_MSG_ERROR);
    }
		
    $this->_acquittement = $this->_args_list ? 
        call_user_func_array(array($this->_client, $evenement_name), $this->_data) : 
        $this->_client->$evenement_name($this->_data);

    if (null == $this->_acquittement) {
    	CAppUI::stepAjax("Acquittement non reçu.", UI_MSG_ERROR);
    }
		
		return true;
  }
  
  function receive() {    
    return $this->_acquittement;
  }
}
?>