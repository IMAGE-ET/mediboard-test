<?php /** $Id$ **/
/**
 * @package    Mediboard
 * @subpackage dicom
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * A Dicom server listening with a socket of a port
 */
class CDicomServer extends CSocketBasedServer {
  
  /**
   * The module 
   * 
   * @var string
   */
  protected $module = "dicom";
  
  /**
   * The controller who will receive the messages
   * 
   * @var string
   */
  protected $controller = "do_receive_message";
  
  /**
   * Check if the message is complete
   * 
   * @param string $message The message
   * 
   * @return boolean
   */
  function isMessageFull($message) {
    $length = unpack("N", substr($message, 2, 4));
    if ($length == strlen($message) - 6) {
      return true;
    }
    return false;
  }
  
  /**
   * The open connection callback
   * 
   * @param integer $id   The client's ID
   * @param string  $addr The client's IP address
   * @param integer $port The client's port
   * 
   * @return boolean true
   */
  function onOpen($id, $addr, $port = null) {
    $post = array(
      "m"       => $this->module,
      "dosql"   => $this->controller,
      "port"    => $this->port,
      "message" => "TCP_Open",
      "client_addr" => $addr,
      "client_port" => $port,
      "suppressHeaders" => 1,
    );
    
    $url = $this->call_url."/index.php?login=$this->username:$this->password";
    $this->requestHttpPost($url, $post);
    
    parent::onOpen($id, $addr, $port);
  }
  
  /**
   * Connection cleanup callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function onCleanup($id) {
    $client = $this->clients[$id];
    
    $post = array(
      "m"       => $this->module,
      "dosql"   => $this->controller,
      "port"    => $this->port,
      "message" => "TCP_Closed",
      "client_addr" => $client["addr"],
      "client_port" => $client["port"],
      "suppressHeaders" => 1,
    );
    
    $url = $this->call_url."/index.php?login=$this->username:$this->password";
    $this->requestHttpPost($url, $post);
    
    parent::onCleanup($id);
  }
  
  /**
   * Format the acknowledgement
   * 
   * @param string  $ack     The acknowledgement
   * 
   * @param integer $conn_id The connection id
   * 
   * @return string
   */
  function formatAck($ack, $conn_id = null) {
    if (array_key_exists("action", $ack)) {
      if ($ack["action"] == "start_timer" && array_key_exists("timestamp", $ack)) {
        $this->server->startTimer($ack["timestamp"], $conn_id);
      }
      elseif ($action == "stop_timer") {
        $this->server->stopTimer($conn_id);
      }
    }  
    if (array_key_exists("response", $ack)) {
      return $ack["response"];
    }
    return "";
  }
  
  /**
   * A sample Dicom message
   *  
   * @return string
   */
  final static function sampleMessage() {
    return "";
  }
}
?> 