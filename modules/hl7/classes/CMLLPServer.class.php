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

$dir = dirname(__FILE__)."/../../..";

$socket_server_class = "$dir/classes/SocketServer.class.php";

// Library not installed
if (!file_exists($socket_server_class)) {
  return;
}

if (!class_exists("SocketServer", false)) {
  require $socket_server_class;
}

require "$dir/includes/version.php";

/**
 * A MLLP server listening with a socket of a port
 */
class CMLLPServer {
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
   * @var string The SSL certificate path (PEM format)
   */
  var $certificate = null;
  
  /**
   * @var string The SSL passphrase
   */
  var $passphrase = null;
  
  /**
   * @var SocketServer The SocketServer instance
   */
  var $server = null;
  
  /**
   * @var integer Request count
   */
  var $request_count = 0;
  
  /**
   * @var string
   */
  var $started_datetime = null;
  
  /**
   * @var SocketClient The SocketClient instance
   */
  static $client = null;
  
  private $clients = array();
  
  /**
   * MLLP server constructor
   * 
   * @param string  $call_url    The Mediboard root URL to call
   * @param string  $username    The Mediboard user name
   * @param string  $password    The Mediboard user password
   * @param integer $port        The port number to listen on
   * @param string  $certificate The path to the SSL/TLS certificate
   * @param string  $passphrase  The SSL/TLS certificate passphrase
   * 
   * @return void
   */
  function __construct($call_url, $username, $password, $port, $certificate = null, $passphrase = null){
    $this->call_url    = $call_url;
    $this->username    = $username;
    $this->password    = $password;
    $this->port        = $port;
    $this->certificate = $certificate;
    $this->passphrase  = $passphrase;
    
    $this->server = new SocketServer(AF_INET, SOCK_STREAM, SOL_TCP);
  }
  
  /**
   * Handle request callback
   * 
   * @param string  $request The request to handle
   * @param integer $id      The client's ID
   * 
   * @return string A hash of the handled request
   */
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
      
      case "__STATS__":
        return json_encode($this->getStats());
    }
    
    // Verification qu'on ne recoit pas un en-tete de message en ayant deja des données en buffer
    if ($buffer && strpos($request, "\x0B")) {
      echo sprintf(" !!! Got a header, while having data in the buffer from %d\n", $id);
    }
    
    echo sprintf(" > Got %d bytes from %d\n", strlen($request), $id);
    
    // Si on recoit le flag de fin de message, on effectue la requete web
    if (strrpos($request, "\x1C") === strlen($request)-1) {
      $buffer .= substr($request, 0, -1);
      
      echo sprintf(" > Got a full message from %d\n", $id);
      
      $post = array(
        "m"       => "eai",
        "dosql"   => "do_receive_mllp",
        "port"    => $this->port,
        "message" => $buffer,
        "client_addr" => $client["addr"],
        "client_port" => $client["port"],
        "suppressHeaders" => 1,
      );
      
      $start = microtime(true);
      
      $url = $this->call_url."/index.php?suppressHeaders=1&login={$this->username}:{$this->password}";
      $ack = $this->requestHttpPost($url, $post);
      
      $this->request_count++;
      $time = microtime(true) - $start;
      echo sprintf(" > Request done in %f s\n", $time);
      
      $buffer = "";
      return "\x0B$ack\x1C\x0D";
    }
    else {
      // Mise en buffer du message
      $buffer .= "$request\n";
    }
    
    return "";
    return md5($request)."\n";
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
  
  /**
   * Connection cleanup callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function cleanup($id) {
    unset($this->clients[$id]);
    echo sprintf(" > Connection [%d] cleaned-up\n", $id);
  }
  
  /**
   * Connection close callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function close($id) {
    echo sprintf(" > Connection [%d] closed\n", $id);
  }
  
  /**
   * Write error callback
   * 
   * @param integer $id The client's ID
   * 
   * @return void
   */
  function writeError($id) {
    echo sprintf(" !!! Write error to [%d]\n", $id);
  }
  
  /**
   * Get the server's stats
   * 
   * @return array An array of various stats
   */
  function getStats(){
    return array(
      "request_count" => $this->request_count,
      "started"       => $this->started_datetime,
      "memory"        => memory_get_usage(true),
      "memory_peak"   => memory_get_peak_usage(true),
    );
  }
  
  /**
   * Execute an HTTP POST request
   * 
   * @param string $url  The URL to call
   * @param array  $data The data to pass to $url via POST
   * 
   * @return string HTTP Response
   */
  function requestHttpPost($url, $data) {
    $data_url = http_build_query($data, null, "&");
    $data_len = strlen($data_url);
    
    $scheme = substr($url, 0, strpos($url, ":"));
    $options = array(
      $scheme => array(
        "method" => "POST",
        "header" => array (
          "Content-Type: application/x-www-form-urlencoded",
          "Content-Length: $data_len", 
        ),
        "content" => $data_url
      )
    );
    
    $ctx = stream_context_create($options);
    
    return file_get_contents($url, false, $ctx);
  }
  
  /**
   * Run the MLLP server
   * 
   * @return void
   */
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
    $this->started_datetime = $time;

    $server = $this->server->bind("0.0.0.0", $this->port, $this->certificate, $this->passphrase);
    
    //$server->setMotd($motd);
    $server->setRequestHandler(array($this, "handle"));
    $server->setOnOpenHandler(array($this, "open"));
    $server->setOnCleanupHandler(array($this, "cleanup"));
    $server->setOnCloseHandler(array($this, "close"));
    $server->setOnWriteErrorHandler(array($this, "writeError"));
    $server->run();
  }
  
  /**
   * Send an MLLP request
   * 
   * @param string  $host    The client's IP to send the request to
   * @param integer $port    The client's port number
   * @param string  $message The message to send
   * 
   * @return string The clien's response
   */
  static function send($host, $port, $message) {
    $root_dir = dirname(__FILE__)."/../../..";
    
    require_once "$root_dir/classes/SocketClient.class.php";
    
    try {
      if (!self::$client) {
        self::$client = new SocketClient();
        self::$client->connect($host, $port);
      }
      
      return self::$client->sendandrecive($message);
    }
    catch(Exception $e) {
      throw $e;
    }
  }
  
  /**
   * Get a list of the current MLLP servers processes
   * 
   * @return array A list of structures containing the processes information
   */
  static function getPsStatus(){
    $tmp_dir = self::getTmpDir();
    
    $pid_files = glob("$tmp_dir/pid.*");
    $processes = array();
    
    foreach ($pid_files as $_file) {
      $_pid = substr($_file, strrpos($_file, ".")+1);
      $launched = strftime("%Y-%m-%d %H:%M:%S", filemtime($_file));
      $processes[$_pid] = array(
        "port"     => file_get_contents($_file),
        "launched" => $launched,
        "launched_rel" => CMbDate::relative($launched)
      );
    }
    
    if (PHP_OS == "WINNT") {
      exec("tasklist", $out);
      $out = array_slice($out, 2); 
      
      foreach ($out as $_line) {
        $_pid = (int)substr($_line, 26, 8);
        if (!isset($processes[$_pid])) {
          continue;
        }
        
        $_ps_name = trim(substr($_line, 0, 25));
        $processes[$_pid]["ps_name"] = $_ps_name;
      }
    }
    else {
      exec("ps -e", $out);
      $out = array_slice($out, 1); 
      
      foreach ($out as $_line) {
        $_pid = (int)substr($_line, 0, 5);
        if (!isset($processes[$_pid])) {
          continue;
        }
      
        $_ps_name = trim(substr($_line, 24));
        $processes[$_pid]["ps_name"] = $_ps_name;
      }
    }
    
    return $processes;
  }
  
  /**
   * Returns the temp directory
   * 
   * @return string The temp directory path
   */
  static function getTmpDir() {
    $root_dir = dirname(__FILE__)."/../../..";
    
    require_once "$root_dir/classes/CMbPath.class.php";
    
    $tmp_dir = "$root_dir/tmp/socket_server";
    CMbPath::forceDir($tmp_dir);
    
    return $tmp_dir;
  }

  /**
   * A sample ORU message formatted in ER7
   * 
   * @return string A sample ORU message formatted in ER7
   */
  final static function sampleORU(){
    $date = strftime("%Y%m%d%H%M%S");
    $er7 = <<<ER7
MSH|^~\&|||||||ORU^R01|HP104220879017992|P|2.3||||||8859/1
PID|1||000038^^^&&^PI~323328^^^Mediboard&1.2.250.1.2.3.4&OX^RI||TEST^Obx^^^m^^L^A||19800101|M|||^^^^^^H|||||||12000041^^^&&^AN||||||||||||N||VALI|20120116161701||||||
PV1|1|I|UF1^^^&&^O|R|12000041^^^&&^RI||929997607^FOO^Bar^^^^^^&1.2.250.1.71.4.2.1&ISO^L^^^ADELI^^^^^^^^^^|||||||90||P|929997607^FOO^Bar^^^^^^&1.2.250.1.71.4.2.1&ISO^L^^^ADELI^^^^^^^^^^||321120^^^Mediboard&1.2.250.1.2.3.4&OX^RI||AMBU|N||||||||||||||4||||||||||||||||
OBR||||Mediboard test|||$date

ER7;
    
    $obx = array();
    $obx[] = "OBX||NM|0002-4b60^Tcore^MDIL|0|".(rand(350, 400)/10)."|0004-17a0^°C^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4bb8^SpO2^MDIL|0|".  rand(80, 100).     "|0004-0220^%^MDIL|||||F";
    $obx[] = "OBX||NM|0002-5000^Resp^MDIL|0|".  rand(20, 50).      "|0004-0ae0^rpm^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4182^HR^MDIL|0|".    rand(40, 90).      "|0004-0aa0^bpm^MDIL|||||F";
    
    $obx[] = "OBX||NM|0002-4a15^ABPs^MDIL|0|".    rand(90, 160).   "|0004-0f20^mmHg^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4a16^ABPd^MDIL|0|".    rand(30, 90).    "|0004-0f20^mmHg^MDIL|||||F";
    $obx[] = "OBX||NM|0002-4a17^ABPm^MDIL|0|".    rand(80, 100).   "|0004-0f20^mmHg^MDIL|||||F";
    
    $er7 .= implode("\n", $obx);
    
    return $er7;
  }

  /**
   * Return the list of sample messages defined in this class
   * 
   * @return array The list of available test messages
   */
  static function getList(){
    $reflection = new ReflectionClass('CMLLPServer');
    $list = $reflection->getMethods(ReflectionMethod::IS_FINAL);
    
    $types = array();
    foreach ($list as $_method) {
      $types[] = substr($_method->name, 6);
    }
    
    return $types;
  }
}
