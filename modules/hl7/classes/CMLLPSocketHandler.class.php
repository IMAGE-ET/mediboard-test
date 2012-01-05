<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$dir = dirname(__FILE__)."/../../..";

$socket_server_class = "$dir/lib/phpsocket/SocketServer.php";

// Library not installed
if (!file_exists($socket_server_class)) {
  return;
}

require $socket_server_class;
require "$dir/includes/version.php";

class CMLLPSocketHandler {
  /**
   * @var string Root URL called when receiving data on the $port
   */
  var $call_url = null;
  
  /**
   * @var string Username used to connect to the Mediboard instance pointed by $call_url
   */
  var $username = null;
  
  /**
   * @var string Password associated to $username
   */
  var $password = null;
  
  /**
   * @var int Port to listen on
   */
  var $port = null;
  
  /**
   * @var SocketServer The SocketServer instance
   */
  var $server = null;
  
  private $clients = array();
  
  function __construct($call_url, $username, $password, $port){
    $this->call_url = $call_url;
    $this->username = $username;
    $this->password = $password;
    $this->port = $port;
    $this->server = new SocketServer(AF_INET, SOCK_STREAM, SOL_TCP);
  }
  
  function handle($request, $id) {
    $client = $this->clients[$id];
    $buffer = &$this->clients[$id]["buffer"];
    
    // Commands
    switch($request) {
      case "__STOP__":
        $buffer = "";
        return false;
        
      case "__RESTART__":
        if (function_exists("quit")) {
          quit("restart");
        }
    }
    
    // Verification qu'on ne recoit pas un en-tete de message en ayant deja des données en buffer
    if ($buffer && strpos($request, "\x0B")) {
      echo sprintf(" !!! Got a header, while having data in the buffer from %d\n", $id);
    }
    
    // Mise en buffer du message
    $buffer .= "$request\n";
    
    echo sprintf(" > Got %d bytes from %d\n", strlen($request), $id);
    
    // Si on recoit le flag de fin de message, on effectue la requete web
    if (strrpos($request, "\x1C") === strlen($request)-1) {
      echo sprintf(" > Got a full message from %d\n", $id);
      
      $post = array(
        "m"       => "eai",
        "dosql"   => "do_receive_mllp",
        "port"    => $this->port,
        "message" => $buffer,
        "client_addr" => $client["addr"],
        "client_port" => $client["port"],
      );
      
      $start = microtime(true);
      $this->http_request_post($this->call_url."/index.php?login={$this->username}:{$this->password}", $post);
      $time = microtime(true) - $start;
      echo sprintf(" > Request done in %f s\n", $time);
      
      $buffer = "";
    }
    
    return md5($request)."\n";
  }
  
  function open($id, $addr, $port = null) {
    if (!isset($this->clients[$id])) {
      $this->clients[$id] = array(
        "buffer" => "",
        "addr"   => $addr,
        "port"   => $port,
      );
    }
    
    echo sprintf(" > New connection [%d] arrived from %s:%d\n", $id, $addr, $port);
    return true;
  }
  
  function cleanup($id) {
    echo sprintf(" > Connection [%d] cleaned-up\n", $id);
  }
  
  function close($id) {
    echo sprintf(" > Connection [%d] closed\n", $id);
  }
  
  function write_error($id) {
    echo sprintf(" !!! Write error to [%d]\n", $id);
  }
  
  /**
   * @param string $url The URL to call
   * @param array $data The data to pass to $url via POST
   * @return string HTTP Responses
   */
  function http_request_post($url, $data) {
    $data_url = http_build_query($data, null, "&");
    $data_len = strlen($data_url);
    
    $scheme = substr($url, 0, strpos($url, ":"));
    
    $ctx = stream_context_create(array(
      $scheme => array(
        "method" => "POST",
        "header" => array (
          "Content-Type: application/x-www-form-urlencoded",
          "Content-Length: $data_len", 
        ),
        "content" => $data_url
      )
    ));
    
    return file_get_contents($url, false, $ctx);
  }
  
  function run(){
    global $version;
    
    $time = strftime("%Y-%m-%d %H:%M:%S");
    $v    = $version['string'];
    $motd = <<<EOT
-------------------------------------------------------
|   Welcome to the Mediboard MLLP Server v.$v   |
|   Started at $time                    |
-------------------------------------------------------

EOT;

    $this->server->bind("0.0.0.0", $this->port)
                 ->setMotd($motd)
                 ->setRequestHandler     (array($this, "handle"))
                 ->setOnOpenHandler      (array($this, "open"))
                 ->setOnCleanupHandler   (array($this, "cleanup"))
                 ->setOnCloseHandler     (array($this, "close"))
                 ->setOnWriteErrorHandler(array($this, "write_error"))
                 ->run();
  }
}