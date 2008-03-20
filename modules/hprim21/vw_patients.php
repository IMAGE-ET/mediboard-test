<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

$extension = mbGetValueFromGet("fileextension", $dPconfig["hprim21"]["CHprim21Reader"]["fileextension"]);

$ftp = new CFTP();
$ftp->hostname = mbGetValueFromGet("hostname", $dPconfig["hprim21"]["CHprim21Reader"]["hostname"]);
$ftp->username = mbGetValueFromGet("username", $dPconfig["hprim21"]["CHprim21Reader"]["username"]);
$ftp->userpass = mbGetValueFromGet("userpass", $dPconfig["hprim21"]["CHprim21Reader"]["userpass"]);
$ftp->connect();
$list = $ftp->getListFiles("./ftp");

foreach($list as $filepath) {
  if(substr($filepath, -(strlen($extension))) == $extension) {
    $filename = basename($filepath);
    $hprimFile = $ftp->getFile($filepath, "tmp/hprim21/$filename");
    $hprimReader = new CHPrim21Reader();
    $hprimReader->readFile($hprimFile);
    if(!count($hprimReader->error_log)) {
      $ftp->delFile($filepath);
    }
    unlink($hprimFile);
  }
}

mbTrace($ftp->logs);

?>