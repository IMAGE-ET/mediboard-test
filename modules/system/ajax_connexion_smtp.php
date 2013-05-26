<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// Check params
if (null == $exchange_source_name = CValue::get("exchange_source_name")) {
  CAppUI::stepAjax("CExchangeSource-error-noSourceName", UI_MSG_ERROR);
}
if (null == $type_action = CValue::get("type_action")) {
  CAppUI::stepAjax("CExchangeSource-error-noTestDefined", UI_MSG_ERROR);
}

/** @var CSourceSMTP $exchange_source */
$exchange_source = CExchangeSource::get($exchange_source_name, "smtp", true, null, false);

if (!$exchange_source->_id) {
  CAppUI::stepAjax("CExchangeSource-error-unsavedParameters", UI_MSG_ERROR);
}
$exchange_source->init();

if ($type_action == "connexion") {
  try {
    $exchange_source->_mail->SmtpConnect();
    CAppUI::stepAjax("CSourceSMTP-info-connection-established", UI_MSG_OK, $exchange_source->host, $exchange_source->port);
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
    $body =
      "<h2>Mail de test</h2>
      <p>Ceci est un mail de test envoyé par Mediboard afin de vérifier le fonctionnement de votre serveur SMTP</p>";
    $exchange_source->setBody($body);
    $exchange_source->addAttachment("./images/pictures/logo.png");
    $exchange_source->send();
    CAppUI::stepAjax("CSourceSMTP-info-message-sent", UI_MSG_OK, $exchange_source->host, $exchange_source->port);
  } catch(CMbException $e) {
    $e->stepAjax();
  }
}
else {
  CAppUI::stepAjax("CExchange-unknown-test", UI_MSG_ERROR, $type_action);
}
