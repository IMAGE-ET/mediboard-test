<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSourceMLLP extends CExchangeSource {
  /**
   * Start of an MLLP message
   */
  const TRAILING = "\x0B";     // \v Vertical Tab (VT, decimal 11)
  
  /**
   * End of an MLLP message
   */
  const LEADING  = "\x1C\x0D"; // File separator (FS, decimal 28), \r Carriage return (CR, decimal 13)
  
  var $source_mllp_id     = null;
  var $port               = null;
  
  var $ssl_enabled        = null;
  var $ssl_certificate    = null;
  var $ssl_passphrase     = null;
  
  private $_socket_client = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_mllp';
    $spec->key   = 'source_mllp_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["port"]            = "num default|7001";
    $specs["ssl_enabled"]     = "bool notNull default|0";
    $specs["ssl_certificate"] = "str";
    $specs["ssl_passphrase"]  = "str";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->port;
  }
  
  /**
   * @return SocketClient
   */
  function getSocketClient(){
    if ($this->_socket_client) return $this->_socket_client;
    
    $address = "$this->host:$this->port";
    $context = stream_context_create();
    
    if ($this->ssl_enabled && $this->ssl_certificate && is_readable($this->ssl_certificate)) {
      $address = "tcp://$address";
      
      stream_context_set_option($context, 'ssl', 'local_cert', $this->ssl_certificate); 
      
      if ($this->ssl_passphrase) {
        stream_context_set_option($context, 'ssl', 'passphrase', $this->ssl_passphrase); 
      }
    }
    
    $this->_socket_client = stream_socket_client($address, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
    stream_set_blocking($this->_socket_client, 0);
    
    return $this->_socket_client;
  }
  
  function recv(){
    $servers = array($this->_socket_client);
    
    while (@stream_select($servers, $write = null, $except = null, 5) === false);
    
    $data = "";
    $data = stream_get_contents($this->_socket_client);
    
    return $data;
  }
  
  function getData($path = null) {
    return $this->recv();
  }
  
  function send($evenement_name = null){
    $data = self::TRAILING.$this->_data.self::LEADING;
    
    fwrite($this->_socket_client, $data, strlen($data));

    $acq = $this->recv();
    
    $this->_acquittement = trim(str_replace("\x1C", "", $acq));
  }
  
  function isReachableSource() {
    return true;
    
    try {
      $this->getSocketClient();
    } 
    catch (Exception $e) {
      $this->_reachable = 0;
      $this->_message   = $e->getMessage();
      return false;
    }
        
    return true;
  }
  
  function isAuthentificate() {
    return $this->isReachableSource();
  }
  
  function getResponseTime() {
    $this->_response_time = url_response_time($this->host, $this->port);
  }
}
