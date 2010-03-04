<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Envoi א la source crייe 'HPRIM21' (FTP)
$exchange_source = CExchangeSource::get("hprim21");
$extension = $exchange_source->fileextension;

$ftp = new CFTP();
$ftp->init($exchange_source);
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