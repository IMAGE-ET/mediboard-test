<?php 
/**
 * Send message
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$exchange_guid = CValue::get("exchange_guid");

// Chargement de l'objet
$exchange = CMbObject::loadFromGuid($exchange_guid);
$exchange->loadRefsInteropActor();

if (!$exchange->message_valide) {
  CAppUI::stepAjax("Le message de l'échange est invalide il ne peut pas être renvoyé", UI_MSG_ERROR);
}

$receiver = $exchange->_ref_receiver;

$evenement = null;

if ($receiver instanceof CReceiverIHE) {
  $evenement   = "evenementsPatient";
  $data_format = CIHE::getEvent($exchange);
}

if ($receiver instanceof CDestinataireHprim) {
  if ($exchange->type == "patients") {
    $evenement   = "evenementPatient";
    $data_format = CHPrimXMLEvenementsPatients::getHPrimXMLEvenements($exchange->_message);
  }
  
  if ($exchange->type == "pmsi") {
    $data_format = new CHPrimXMLEvenementsServeurActivitePmsi();
    CAppUI::stepAjax("L'envoi de cet événement n'est actuellement pas pris en charge", UI_MSG_ERROR);
  }
}

if ($receiver instanceof CPhastDestinataire) {
  $data_format = CPhastEvenementsPN13::getXMLEvenementsPN13($exchange->_message);
  $evenement = $data_format->sous_type;
}

if (!$evenement) {
  CAppUI::stepAjax("Aucun événement défini pour cet échange", UI_MSG_ERROR);
}

$source = CExchangeSource::get("$receiver->_guid-$evenement");
if (!$source->_id) {
  CAppUI::stepAjax("Aucune source pour cet acteur", UI_MSG_ERROR);
}

$msg = utf8_encode($exchange->_message);
if ($source instanceof CSourceFileSystem) {
  $source->setData($msg, false, "MB-$exchange->type-$exchange->code-$exchange->_id.$source->fileextension");
}
else {
  $source->setData($msg);
}
$source->send();

if ($ack_data = $source->getACQ()) {
  if ($exchange instanceof CEchangeHprim) {
    $ack_data = utf8_decode($ack_data);
    $exchange->date_echange = mbDateTime();
    $ack = CHPrimXMLAcquittements::getAcquittementEvenementXML($data_format);
    $ack->loadXML($ack_data);
    $doc_valid = $ack->schemaValidate();
    if ($doc_valid) {
      $exchange->statut_acquittement = $ack->getStatutAcquittement();
    }
    $exchange->acquittement_valide = $doc_valid ? 1 : 0;
    $exchange->_acquittement = $ack_data;
    $exchange->store();
  }

  if ($exchange instanceof CExchangeIHE) {
    $ack = new CHL7v2Acknowledgment($data_format);
    $ack->handle($ack_data);
    $exchange->date_echange        = mbDateTime();   
    $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
    $exchange->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange->_acquittement       = $ack_data;
    $exchange->store();
  }
}

CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' a été retraité");

?>