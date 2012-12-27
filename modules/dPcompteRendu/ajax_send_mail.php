<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$nom         = CValue::post("nom");
$email       = CValue::post("email");
$object_guid = CValue::post("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

$exchange_source = CExchangeSource::get("mediuser-" . CAppUI::$user->_id, "smtp");

$exchange_source->init();

try {
  $exchange_source->setRecipient($email, $nom);
  $exchange_source->setSubject("Mediboard - Envoi de document");
  $exchange_source->setBody("Ce document vous a été envoyé via l'application Mediboard.");

  switch ($object->_class) {
    case "CCompteRendu":
      $object->makePDFpreview(true);
      $file = $object->_ref_file;
      $exchange_source->addAttachment($file->_file_path, $file->file_name);
      break;
    case "CFile":
      $exchange_source->addAttachment($object->_file_path, $object->file_name);
  }
  $exchange_source->send();
  CAppUI::displayAjaxMsg("Message envoyé");
} catch(phpmailerException $e) {
  CAppUI::displayAjaxMsg($e->errorMessage(), UI_MSG_WARNING);
} catch(CMbException $e) {
  $e->stepAjax();
}
