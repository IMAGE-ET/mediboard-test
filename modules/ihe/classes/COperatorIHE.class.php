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
      if (!$evt->message->isOK(CHL7v2::E_ERROR)) {
        $exchange_ihe->populateExchange($data_format, $evt);

        $dom_ack->_ref_exchange_ihe = $exchange_ihe;
        $msgAck = $dom_ack->generateAcknowledgment();

        $exchange_ihe->populateErrorExchange($msgAck);

        //return $msgAck;
      }

      // Gestion des notifications ? 
      if (!$exchange_ihe->_id) {
        $exchange_ihe->populateExchange($data_format, $evt);
        $exchange_ihe->identifiant_emetteur = $data['identifiantMessage'];
        $exchange_ihe->message_valide       = 1;
      }
      
      $exchange_ihe->date_production = mbDateTime();
      $exchange_ihe->store();

      $exchange_ihe->loadRefsInteropActor();

      // Chargement des configs de l'expéditeur
      $exchange_ihe->_ref_sender->getConfigs($data_format);

      $dom_evt->_ref_exchange_ihe = $exchange_ihe;
      $dom_acq->_ref_exchange_ihe = $exchange_ihe;

      // Message événement patient
      if ($evt instanceof CHL7EventADT) {
        return self::eventPatient($data, $exchange_ihe, $evt, $dom_evt, $dom_ack);
      }
    } catch(Exception $e) {
      $exchange_ihe->populateExchange($data_format, $evt);
      
      $dom_ack                        = new CHL7v2Acknowledgment();
      $dom_ack->_identifiant_acquitte = isset($data['identifiantMessage']) ? $data['identifiantMessage'] : "000000000";
      $dom_acq->_ref_exchange_ihe     = $exchange_ihe;
      
      $msgAck = $dom_ack->generateAcknowledgment();
      
      $exchange_ihe->populateErrorExchange($msgAck);
      
      return $msgAck;
    }
  }
  
  static function eventPatient($data = array(), CExchangeIHE $exchange_ihe, CHL7Event $evt,
                              CHL7v2MessageXML $dom_evt, CHL7Acknowledgment $dom_ack) {
    $newPatient = new CPatient();
    $newPatient->_eai_exchange_initiator_id = $exchange_ihe->_id;

    switch (get_class($evt)) {
      // Création d'un nouveau patient - Mise à jour d'information du patient
      case "CHL7v2EventADTA28" : 
      case "CHL7v2EventADTA31" :
        $data                       = array_merge($data, $dom_evt->getContentsXML());
        $exchange_ihe->id_permanent = array_key_exists("PI", $data['patientIdentifiers']) ? $data['patientIdentifiers']['PI'] : null;
        $msgAck                     = $dom_evt->recordPerson($dom_ack, $newPatient, $data);
        break;
      // Aucun des événements - retour d'erreur
      default :
        $msgAck = $dom_ack->generateAcknowledgment();
        break;
    }
    
    return $msgAck;
  }
}

?>