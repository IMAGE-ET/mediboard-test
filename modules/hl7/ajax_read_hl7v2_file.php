<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

// Envoi à la source créée 'HL7 v.2'
$exchange_source = CExchangeSource::get("hl7v2");
$extension = $exchange_source->fileextension;

$ftp = new CFTP();
$ftp->init($exchange_source);
$ftp->connect();

if (!$list = $ftp->getListFiles("./")) {
  CAppUI::stepAjax("Le répertoire ne contient aucun fichier", UI_MSG_ERROR);
}

foreach($list as $filepath) {
  if (substr($filepath, -(strlen($extension))) == $extension) {
    $filename = uniqid().basename($filepath);
    $hl7File = $ftp->getFile($filepath, "tmp/hl7/$filename");
    
    $hl7v2_reader = new CHL7v2Reader();
    $hl7v2_reader->readFile($hl7File);
    
    unlink($hl7File);
  } else {
    $ftp->delFile($filepath);
  }
}

?>