<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$hprim_config = CAppUI::conf("hprim21 CHprim21Reader");

$extension = CValue::get("fileextension", $hprim_config["fileextension"]);

$list = array();
$ftp = new CFTP();
$ftp->hostname = $hprim_config["hostname"];
$ftp->username = $hprim_config["username"];
$ftp->userpass = $hprim_config["userpass"];
$ftp->mode     = "FTP_ASCII";
$ftp->passif_mode = "0";
$ftp->timeout  = "90";
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