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

/* @todo Penser à ajouter les prochains formats */
if (!$exchange instanceof CEchangeHprim) {
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' ne peut retraité car il n'est pas pris en charge", UI_MSG_ERROR);
}

$sender = new $exchange->sender_class;
$sender->load($exchange->sender_id);

if (!$acq = CEAIDispatcher::dispatch($exchange->_message, $sender)) {
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' ne peut retraité", UI_MSG_ERROR);
}

if ($exchange instanceof CEchangeHprim) {
  $dom_acq = CHPrimXMLAcquittements::getAcquittementEvenementXML($sender->_data_format->_family_message);
  $dom_acq->loadXML($acq);
  $doc_valid = $dom_acq->schemaValidate();
  if ($doc_valid) {
    $exchange->statut_acquittement = $dom_acq->getStatutAcquittement();
  }
  $exchange->acquittement_valide = $doc_valid ? 1 : 0;
  $exchange->_acquittement = $acq;
  $exchange->store();
  
  CAppUI::stepAjax("Le message '".CAppUI::tr("$exchange->_class")."' a été retraité");
}

?>