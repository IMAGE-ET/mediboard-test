<?php

$dir = dirname(__FILE__)."/../../..";
require "$dir/lib/phpsocket/SocketServer.php";
require "$dir/includes/version.php";

class CMLLPSocketHandler {
  var $call_url = null;
  var $username = null;
  var $password = null;
  var $port = null;
  
  /**
   * @var SocketServer
   */
  var $server = null;
  
  function __construct($call_url, $username, $password, $port){
    $this->call_url = $call_url;
    $this->username = $username;
    $this->password = $password;
    $this->port = $port;
    
    $this->server = new SocketServer(AF_INET, SOCK_STREAM, SOL_TCP);
  }
  
  function handle($request, $id) {
    $this->http_request_post($this->call_url."/index.php?login=$this->username:$this->password&m=eai&a=void&suppressHeaders=1", array("message" => $request));
    
    echo sprintf("*** Got %d bytes from %d\n", strlen($request), $id);
    return md5($request);
  }
  
  function open($id, $addr, $port = null) {
    echo sprintf("New connection [%d] arrived from %s:%d\n", $id, $addr, $port);
    return ("127.0.0.1" == $addr);
  }
  
  function cleanup($id) {
    echo sprintf("Connection [%d] cleaned-up\n", $id);
  }
  
  function close($id) {
    echo sprintf("Connection [%d] closed\n", $id);
  }
  
  function write_error($id) {
    echo sprintf("Write error to [%d]\n", $id);
  }
  
  function http_request_post($url, $data) {
    $data_url = http_build_query($data);
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
    
    $motd = 
      "-- Welcome to the Mediboard MLLP Server v.{$version['string']} -- \n".
      strftime("--      %Y-%m-%d %H:%M:%S");
      
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