<?php

/**
 * Envoi d'un docitem par mail
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$nom         = CValue::post("nom");
$email       = CValue::post("email");
$subject     = CValue::post("subject");
$body        = CValue::post("body");
$object_guid = CValue::post("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

/** @var $exchange_source CSourceSMTP */
$exchange_source = CExchangeSource::get("mediuser-" . CMediusers::get()->_id, "smtp");

$exchange_source->init();

if (CAppUI::pref('hprim_med_header')) {
  $body = $object->makeHprimHeader($exchange_source->email, $email) . "\n" . $body;
}

try {
  $exchange_source->setRecipient($email, $nom);
  $exchange_source->setSubject($subject);
  $exchange_source->setBody(nl2br($body));

  switch ($object->_class) {
    case "CCompteRendu":
      /** @var $object CCompteRendu */
      $object->makePDFpreview(true);
      $file = $object->_ref_file;
      $exchange_source->addAttachment($file->_file_path, $file->file_name);
      break;
    case "CFile":
      /** @var $object CFile */
      $exchange_source->addAttachment($object->_file_path, $object->file_name);
  }
  $exchange_source->send();
  $exchange_source->createUserMail(CAppUI::$user->_id, $object);
  CAppUI::displayAjaxMsg("Message envoyé");
}
catch(phpmailerException $e) {
  CAppUI::displayAjaxMsg($e->errorMessage(), UI_MSG_WARNING);
}
catch(CMbException $e) {
  $e->stepAjax();
}
