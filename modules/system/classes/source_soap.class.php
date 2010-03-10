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
  var $web_service_name = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_soap';
    $spec->key   = 'source_soap_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["wsdl_mode"] = "bool default|1";
    
    return $specs;
  }
  
  function send($evenement_name) {
    if (!$this->_client = CMbSOAPClient::make($this->host, $this->user, $this->password, $this->web_service_name)) {
      trigger_error("Impossible de joindre le destinataire : ".$this->url);
    }
    
    $this->_acquittement = $this->_args_list ? 
        call_user_func_array(array($this->_client, $evenement_name), $this->_data) : 
        $this->_client->$evenement_name($this->_data);
        
    if (null == $this->_acquittement) {
      trigger_error("Acquittement non reçu.");
    }
  }
  
  function receive() {    
    return $this->_acquittement;
  }
}
?>