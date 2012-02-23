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
  
  private $_socket_client = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_mllp';
    $spec->key   = 'source_mllp_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["port"] = "num default|7001";
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
		
    CAppUI::requireLibraryFile("phpsocket/SocketClient");
    
    $this->_socket_client = new SocketClient;
    $this->_socket_client->open($this->host, $this->port);
    
    return $this->_socket_client;
  }
  
  function getData($path = null) {
    return $this->_socket_client->recv();
  }
  
  function send($evenement_name = null){
    $this->_acquittement = trim(str_replace("\x1C", "", $this->getSocketClient()->sendandrecive(self::TRAILING.$this->_data.self::LEADING)));
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
}
