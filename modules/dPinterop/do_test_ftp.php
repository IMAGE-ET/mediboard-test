<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$testType = mbGetValueFromGet("testType", "ftp");

$ftp = new CFTP();
$ftp->hostname = mbGetValueFromGet("hostname", "localhost");
$ftp->username = mbGetValueFromGet("username", "user");
$ftp->userpass = mbGetValueFromGet("userpass", "pass");
$ftp->port     = mbGetValueFromGet("port"    , 21);
$ftp->timeout  = mbGetValueFromGet("timeout" , 90);
$passif_mode   = mbGetValueFromGet("passive" , false);

if($testType == "ftp") {
  $ftp->connect($passif_mode);
} else {
  $ftp->testSocket();
}
foreach($ftp->logs as $log) {
  CAppUI::setMsg($log);
}

echo CAppUI::getMsg();

?>