<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

$extension = mbGetValueFromGet("fileextension", $dPconfig["hprim21"]["CHprim21Reader"]["fileextension"]);

$list = array();
$ftp = new CFTP();
$ftp->hostname = $dPconfig["hprim21"]["CHprim21Reader"]["hostname"];
$ftp->username = $dPconfig["hprim21"]["CHprim21Reader"]["username"];
$ftp->userpass = $dPconfig["hprim21"]["CHprim21Reader"]["userpass"];
$ftp->connect();
$list = $ftp->getListFiles("./");

if(!$list) {
  return;
}

foreach($list as $filepath) {
  if(substr($filepath, -(strlen($extension))) == $extension) {
    $filename = basename($filepath);
    $hprimFile = $ftp->getFile($filepath, "tmp/hprim21/$filename");
    $hprimReader = new CHPrim21Reader();
    $hprimReader->readFile($hprimFile);
    if(!count($hprimReader->error_log)) {
      $ftp->delFile($filepath);
    } else {
      mbTrace($hprimReader->error_log, "Erreur(s) pour le fichier '$filepath'");
    }
    unlink($hprimFile);
  } else {
    $ftp->delFile($filepath);
  }
}

?>