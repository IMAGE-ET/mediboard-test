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

// HPRIM export FTP settings
$HPrimConfig   = CAppUI::conf("dPinterop hprim_export");
$fileprefix    = $HPrimConfig["fileprefix"];
$filenbroll    = $HPrimConfig["filenbroll"];
$fileextension = $HPrimConfig["fileextension"];

$doc = new CHPrimXMLServeurActes();
// Compte le nombre de fichiers déjà générés
CMbPath::forceDir($doc->finalpath);
$count = 0;
$dir = dir($doc->finalpath);
while (false !== ($entry = $dir->read())) {
  $count++;
}
$dir->close();
$count -= 2; // Exclure . et ..
$counter = $count % pow(10, $filenbroll);

// Transfert réel
$destination_basename = sprintf("%s%0".$filenbroll."d", $fileprefix, $counter);
  
if($testType == "ftp") {
  $ftp->connect($passif_mode);
} else if($testType == "sendfile") { 
	if (!$file) {
		$AppUI->setMsg("Un nom de fichier doit etre fourni.");
		return;
	}
	$ftp->connect($passif_mode);
  $ftp->sendFile($root_dir.$file, "$destination_basename.$fileextension", FTP_ASCII);
} else {
  $ftp->testSocket();
}

foreach($ftp->logs as $log) {
  $AppUI->setMsg($log);
}

echo $AppUI->getMsg();

?>