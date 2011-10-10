<?php

/**
 * Operator IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("eai", "CEAIOperator");

/**
 * Class COperatorIHE 
 * Operator IHE
 */
class COperatorIHE extends CEAIOperator {
  function event(CExchangeDataFormat $data_format) {
    $msg = $data_format->_message;
    $evt = $data_format->_family_message;

    $evt_class = CHL7Event::getEventClass($evt->event_type, $evt->code);
    if (!in_array($evt_class, $data_format->_messages_supported_class)) {
      throw new CMbException(CAppUI::tr("CEAIDispatcher-no_message_supported_for_this_actor", $evt_class));
    }
    
    // Récupération des informations du message
    $dom_evt = $evt->handle($msg);
    
    try {
      // Création de l'échange
      $exchange_ihe = new CExchangeIHE();

      // Récupération des données du segment MSH
      $data = $dom_evt->getMSHEvenementXML();

      // Gestion de l'acquittement
      $dom_ack                        = new CHL7v2Acknowledgment();
      $dom_ack->_identifiant_acquitte = $data['identifiantMessage'];

      // Acquittement d'erreur d'un document XML recu non valide
      if (!$evt->message->isOK()) {
        $exchange_ihe->populateExchange($data_format, $evt);

        $dom_ack->_ref_exchange_ihe = $exchange_ihe;
        $msgAck                     = $dom_ack->generateAcknowledgment();

        $exchange_ihe->populateErrorExchange($msgAck);

        //return $msgAck;
      }
mbLog($exchange_ihe);
      
    } catch(Exception $e) {
      
    }
  }
}

?>