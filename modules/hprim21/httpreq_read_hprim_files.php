<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

// Envoi à la source créée 'HPRIM21' (FTP)
$exchange_source = CExchangeSource::get("hprim21", "ftp");
$extension = $exchange_source->fileextension;

$ftp = new CFTP();
$ftp->init($exchange_source);

try {
  $ftp->connect();
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
}

$list = array();
try {
  $list = $ftp->getListFiles(".");
} catch (CMbException $e) {
  CAppUI::stepAjax($e->getMessage(), UI_MSG_WARNING); 
}

if (empty($list)) {
  CAppUI::stepAjax("Le répertoire ne contient aucun fichier", UI_MSG_ERROR);
}

$sender_ftp = new CSenderFTP();
$sender_ftp->user_id = CUser::get()->_id;
$sender_ftp->loadMatchingObject();

$count = CAppUI::conf("eai max_files_to_process");
$list = array_slice($list, 0, $count);

foreach($list as $filepath) {
  if (substr($filepath, -(strlen($extension))) == $extension) {
    $filename = basename($filepath);
    $hprimFile = $ftp->getFile($filepath, "tmp/hprim21/$filename");
    
    // Création de l'échange
    $echg_hprim21 = new CEchangeHprim21();
    $echg_hprim21->group_id        = CGroups::loadCurrent()->_id;
    $echg_hprim21->sender_class    = $sender_ftp->_class;
    $echg_hprim21->sender_id       = $sender_ftp->_id;
    $echg_hprim21->date_production = CMbDT::dateTime();
    $echg_hprim21->store();
    
    $hprimReader = new CHPrim21Reader();
    $hprimReader->_echange_hprim21 = $echg_hprim21;
    $hprimReader->readFile($hprimFile);
    
    // Mapping de l'échange
    $echg_hprim21 = $hprimReader->bindEchange($hprimFile);
    
    if (!count($hprimReader->error_log)) {
      $echg_hprim21->message_valide = true;
      $ftp->delFile($filepath);
    } else {
      $echg_hprim21->message_valide = false;
      CAppUI::stepAjax("Erreur(s) pour le fichier '$filepath' : $hprimReader->error_log", UI_MSG_WARNING);
    }
    $msg = $echg_hprim21->store();
    $msg ? CAppUI::stepAjax("Erreur lors de la création de l'échange : $msg", UI_MSG_WARNING) : 
           CAppUI::stepAjax("L'échange '$echg_hprim21->_id' a été créé.");
    unlink($hprimFile);
  } else {
    $ftp->delFile($filepath);
  }
}

?>