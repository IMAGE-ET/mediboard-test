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

if ($exchange instanceof CEchangeHprim) {
  $acquittement = CHprimSoapHandler::evenementPatient($exchange->_message);

  $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
  $domGetAcquittement->loadXML($acquittement);
  $doc_valid = $domGetAcquittement->schemaValidate();
  if ($doc_valid) {
    $exchange->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
  }
  $exchange->acquittement_valide = $doc_valid ? 1 : 0;
  $exchange->_acquittement = $acquittement;
  $exchange->store();
  
  CAppUI::setMsg("Message '$exchange->_class_name' retraité", UI_MSG_OK);
}

echo CAppUI::getMsg();

?>