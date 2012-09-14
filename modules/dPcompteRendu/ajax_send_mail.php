<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$nom     = CValue::post("nom");
$email   = CValue::post("email");
$type    = CValue::post("type");
$file_id = CValue::post("file_id");

// CCompteRendu
if ($type == "doc") {
  $content = stripslashes(CValue::post("content"));
  // L'attribut css height dans l'attribut style d'une image est remplacé par height-min
  // Il faut donc le remplacer par l'attribut height de l'image.l
  $content = preg_replace("/height\s*:\s*([0-9]*)px\s*;/i","\" height=\"$1\" style=\"", $content);
  
  preg_match_all("/<img[^>]*src\s*=\s*[\"']([^\"']+)[\"'][^>]*>/i", $content, $matches);
}
// CFile
else {
  $file = new CFile;
  $file->load($file_id);
}


$exchange_source = CExchangeSource::get("mediuser-" . CAppUI::$user->_id, "smtp");

$exchange_source->init();

try {
  $exchange_source->setRecipient($email, $nom);
  
  if ($type == "doc") {
    $exchange_source->setSubject("Mediboard - Envoi de compte-rendu");
    // Inclusion des images contenues dans le compte-rendu
    foreach ($matches[1] as $key=>$_match) {
      $content = str_replace($_match, "cid:$key", $content);
      $exchange_source->addEmbeddedImage($_SERVER['DOCUMENT_ROOT'] . $_match, $key);
    }

    $exchange_source->setBody($content);
  }
  else {
    $exchange_source->setSubject("Mediboard - Envoi de fichier");
    $exchange_source->addAttachment($file->_file_path, $file->file_name);
    $exchange_source->setBody("Ce document vous a été envoyé via l'application Mediboard.");
  }
  
  $exchange_source->send();
  CAppUI::displayAjaxMsg("Message envoyé");
} catch(phpmailerException $e) {
  CAppUI::displayAjaxMsg($e->errorMessage(), UI_MSG_WARNING);
} catch(CMbException $e) {
  $e->stepAjax();
}

?>