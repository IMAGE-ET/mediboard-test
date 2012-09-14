<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("Aucun nom de source d'échange spécifié", UI_MSG_ERROR);
}
if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("Aucun type de test spécifié", UI_MSG_ERROR);
}

$exchange_source = CExchangeSource::get($exchange_source_name, "smtp", true, null, false);

if (!$exchange_source->_id) {
  CAppUI::stepAjax("Veuillez tout d'abord enregistrer vos paramètres de connexion", UI_MSG_ERROR);
}
$exchange_source->init();

if ($type_action == "connexion") {
  try {
    $exchange_source->_mail->SmtpConnect();
    CAppUI::stepAjax("Connecté au serveur $exchange_source->host sur le port $exchange_source->port");
  } catch(phpmailerException $e) {
    CAppUI::stepAjax($e->errorMessage(), UI_MSG_WARNING);
  } catch(CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
  }
} 
elseif ($type_action == "envoi") {
  try {
    $exchange_source->setRecipient($exchange_source->email, $exchange_source->email);
    $exchange_source->setSubject("Test d'envoi de mail par Mediboard");
    $exchange_source->setBody("Ceci est un mail de test envoyé par Mediboard afin de vérifier le fonctionnement de votre serveur SMTP");
    $exchange_source->addAttachment("./images/pictures/logo.png");
    $exchange_source->send();
    CAppUI::stepAjax("Message envoyé au serveur $exchange_source->host sur le port $exchange_source->port");
  } catch(CMbException $e) {
    $e->stepAjax();
  }
} else {
  CAppUI::stepAjax("Type de test non supporté : $type_action", UI_MSG_ERROR);
}
