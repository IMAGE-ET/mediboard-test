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
  var $source_soap_id   = null;
  
  // DB Fields
  var $wsdl_mode        = null;
  var $evenement_name   = null;
  var $single_parameter = null;
  var $encoding         = null;
  var $stream_context   = null;
  var $type_soap        = null;
  var $local_cert       = null;
  var $passphrase       = null;
  
  var $_headerbody      = array();
   
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
    $specs["encoding"]         = "enum list|UTF-8|ISO-8859-1|ISO-8859-15 default|UTF-8";
    $specs["stream_context"]   = "str";
    $specs["type_soap"]        = "enum list|CMbSOAPClient|CNuSOAPClient default|CMbSOAPClient notNull";
    $specs["local_cert"]       = "str";
    $specs["passphrase"]       = "password revealable";
    
    return $specs;
  }
  
  function __call($function, $arguments) { 
    $this->setData(reset($arguments));
    $this->send($function);
  }
  
  function setHeaders($namespace, $name, $data, $mustUnderstand = false, $actor = null) {
    if ($actor) {
      $this->_headerbody[] = new SoapHeader($namespace, $name, $data, $mustUnderstand, $actor);
    }
    else {
      $this->_headerbody[] = new SoapHeader($namespace, $name, $data);
    }
  }
 
  function send($evenement_name = null, $flatten = false) {
    if (!$this->_id) {
      throw new CMbException("CSourceSOAP-no-source", $this->name);
    }
    
    if (!$evenement_name) {
      $evenement_name = $this->evenement_name;
    }
    
    if (!$evenement_name) {
      throw new CMbException("CSourceSOAP-no-evenement", $this->name);
    }   
    
    if ($this->single_parameter) {
      $this->_data = array("$this->single_parameter" => $this->_data);
    }
    
    if (!$this->_data) {
      $this->_data = array();
    }
    
    $options = array(
      "encoding" => $this->encoding
    );
    
    $soap_client = new CSOAPClient($this->type_soap);
    $soap_client->make(
      $this->host, $this->user, $this->password, $this->type_echange, $options, null, 
      $this->stream_context, $this->local_cert, $this->passphrase
    );
    
    if ($soap_client->client->soap_client_error) {
      throw new CMbException("CSourceSOAP-unreachable-source", $this->name);
    }
    
    // Applatissement du tableau $arguments qui contient un élément vide array([0] => ...) ?
    $soap_client->client->flatten  = $flatten;
    
    // Définit un ent-ête à utiliser dans les requêtes ?
    if ($this->_headerbody) {
      $soap_client->setHeaders($this->_headerbody);
    }
   
    // Aucun log à produire ? 
    $soap_client->client->loggable = $this->loggable;

    $this->_acquittement = $soap_client->call($evenement_name, $this->_data);

    if (is_object($this->_acquittement)) {
      $acquittement = (array) $this->_acquittement;
      if (count($acquittement) == 1) {
        $this->_acquittement = reset($acquittement);
      } 
    }
    
    return true;
  }
  
  function isReachableSource() {
    if (!url_exists($this->host)) {
      $this->_reachable = 0;
      $this->_message   = CAppUI::tr("CSourceSOAP-unreachable-source", $this->host);
      return false;
    }
    return true;
  }
  
  function isAuthentificate() {
    $options = array(
      "encoding" => $this->encoding
    );
    
    try {
      $soap_client = new CSOAPClient($this->type_soap);
      $soap_client->make($this->host, $this->user, $this->password, $this->type_echange, $options);
    } catch (Exception $e) {
      $this->_reachable = 1;
      $this->_message   = $e->getMessage();
      return false;
    }
    return true;
  }
  
  function getResponseTime() {
    $this->_response_time = url_response_time($this->host, 80);
  }
}
?>