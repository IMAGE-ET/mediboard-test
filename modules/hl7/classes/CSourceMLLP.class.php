<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CSourceMLLP extends CSocketSource {
  /**
   * Start of an MLLP message
   */
  const TRAILING = "\x0B";     // \v Vertical Tab (VT, decimal 11)
  
  /**
   * End of an MLLP message
   */
  const LEADING  = "\x1C\x0D"; // File separator (FS, decimal 28), \r Carriage return (CR, decimal 13)
  
  public $source_mllp_id;
  public $port;
  
  public $ssl_enabled;
  public $ssl_certificate;
  public $ssl_passphrase;
  public $iv_passphrase;
  
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_mllp';
    $spec->key   = 'source_mllp_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["port"]            = "num default|7001";
    $specs["ssl_enabled"]     = "bool notNull default|0";
    $specs["ssl_certificate"] = "str";
    $specs["ssl_passphrase"]  = "password show|0 loggable|0";
    $specs["iv_passphrase"]   = "str show|0 loggable|0";

    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->port;
  }

  function updateEncryptedFields(){
    if ($this->ssl_passphrase === "") {
      $this->ssl_passphrase = null;
    }
    else {
      if (!empty($this->ssl_passphrase)) {
        $this->ssl_passphrase = $this->encryptString($this->ssl_passphrase, "iv_passphrase");
      }
    }
  }
  
  /**
   * @return SocketClient
   */
  function getSocketClient(){
    if ($this->_socket_client) {
      return $this->_socket_client;
    }

    $address = "$this->host:$this->port";
    $context = stream_context_create();
    
    if ($this->ssl_enabled && $this->ssl_certificate && is_readable($this->ssl_certificate)) {
      $address = "tls://$address";
      
      stream_context_set_option($context, 'ssl', 'local_cert', $this->ssl_certificate);

      if ($this->ssl_passphrase) {
        $ssl_passphrase = $this->getPassword($this->ssl_passphrase, "iv_passphrase");
        stream_context_set_option($context, 'ssl', 'passphrase', $ssl_passphrase);
      }
    }
    
    $this->_socket_client = stream_socket_client($address, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
    if (!$this->_socket_client) {
      throw new CMbException("CSourceMLLP-unreachable-source", $this->name);
    }

    stream_set_blocking($this->_socket_client, 0);
    
    return $this->_socket_client;
  }
  
  function recv(){
    $servers = array($this->getSocketClient());

    $data = "";
    do {
      while (@stream_select($servers, $write = null, $except = null, 5) === false);
      $buf = stream_get_contents($this->_socket_client);
      $data .= $buf;
    }
    while ($buf);
    
    return $data;
  }
  
  function getData($path = null) {
    return $this->recv();
  }
  
  function send($evenement_name = null){
    $data = self::TRAILING.$this->_data.self::LEADING;
    
    fwrite($this->getSocketClient(), $data, strlen($data));

    $acq = $this->recv();
    
    $this->_acquittement = trim(str_replace("\x1C", "", $acq));
  }
  
  function isReachableSource() {
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

  /**
   * @see parent::isSecured()
   */
  function isSecured() {
    return ($this->ssl_enabled && $this->ssl_certificate && is_readable($this->ssl_certificate));
  }

  /**
   * @see parent::getProtocol()
   */
  function getProtocol() {
    return 'tcp';
  }
}
