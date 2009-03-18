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
$ftp->timeout  = mbGetValueFromGet("timeout" , 5);
$root_dir      = mbGetValueFromGet("root_dir" , CAppUI::conf('root_dir')."/files/hprim/serveurActes/");
$file          = mbGetValueFromGet("file");
$passif_mode   = mbGetValueFromGet("passive" , false);

if($testType == "ftp") {
  $ftp->connect($passif_mode);
} else if($testType == "sendfile") { 
	if (!$file) {
		CAppUI::setMsg("Un nom de fichier doit etre fourni.");
		return;
	}
	$ftp->connect($passif_mode);
  $ftp->sendFile($root_dir.$file, $file, FTP_ASCII);
} else {
  $ftp->testSocket();
}

foreach($ftp->logs as $log) {
  CAppUI::setMsg($log);
}

echo CAppUI::getMsg();

?>