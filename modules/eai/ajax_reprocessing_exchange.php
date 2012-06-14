<?php 
/**
 * Reprocessing exchange
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

// Chargement de l'échange demandé
$object = new CMbObject();
$exchange = $object->loadFromGuid($exchange_guid);

$sender = new $exchange->sender_class;
$sender->load($exchange->sender_id);

// Suppression de l'identifiant dans le cas où l'échange repasse pour éviter un autre échange avec
// un identifiant forcé
if ($exchange instanceof CExchangeAny) {
  $exchange_id = $exchange->_id;
  $exchange->_id = null;
}

if (!$ack_data = CEAIDispatcher::dispatch($exchange->_message, $sender, $exchange->_id)) {
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' ne peut retraité", UI_MSG_ERROR);
}

$exchange->load($exchange->_id);

if ($exchange instanceof CEchangeHprim) {
  $ack = CHPrimXMLAcquittements::getAcquittementEvenementXML($sender->_data_format->_family_message);
  $ack->loadXML($ack_data);
  $doc_valid = $ack->schemaValidate();
  if ($doc_valid) {
    $exchange->statut_acquittement = $ack->getStatutAcquittement();
  }
  $exchange->date_echange        = mbDateTime();
  $exchange->acquittement_valide = $doc_valid ? 1 : 0;
  $exchange->_acquittement = $ack_data;
  $exchange->store();
  
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' a été retraité");
}

if ($exchange instanceof CExchangeIHE) {
  $ack = new CHL7v2Acknowledgment($sender->_data_format->_family_message);
  $ack->handle($ack_data);
  $exchange->date_echange        = mbDateTime(); 
  $exchange->statut_acquittement = $ack->getStatutAcknowledgment(); 
  $exchange->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
  $exchange->_acquittement       = $ack_data;
  $exchange->store();
  
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' a été retraité");
}

// Dans le cas d'un échange générique on le supprime
if ($exchange instanceof CExchangeAny) {
  $exchange->_id = $exchange_id;
  $exchange->delete();
  
  CAppUI::stepAjax("Le message a été supprimé");
}

?>