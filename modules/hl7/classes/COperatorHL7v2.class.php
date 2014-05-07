<?php

/**
 * Operator HL7v2
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class COperatorHL7v2
 * Operator HL7v2
 */
class COperatorHL7v2 extends CEAIOperator {
  /**
   * Event
   *
   * @param CExchangeDataFormat $data_format Data format
   *
   * @return null|string
   */
  function event(CExchangeDataFormat $data_format) {
    $msg               = $data_format->_message;
    /** @var CHL7v2Event $evt */
    $evt               = $data_format->_family_message;
    $evt->_data_format = $data_format;

    // R�cup�ration des informations du message
    /** @var CHL7v2MessageXML $dom_evt */
    $dom_evt = $evt->handle($msg);
    $dom_evt->_is_i18n = $evt->_is_i18n;

    try {
      // Cr�ation de l'�change
      $exchange_hl7v2 = new CExchangeHL7v2();
      $exchange_hl7v2->load($data_format->_exchange_id);
      
      // R�cup�ration des donn�es du segment MSH
      $data = $dom_evt->getMSHEvenementXML();

      // Gestion de l'acquittement
      $ack = $dom_evt->getEventACK($evt);
      $ack->message_control_id = $data['identifiantMessage'];

      // Message non support� pour cet utilisateur
      $evt_class = CHL7Event::getEventClass($evt);
      if (!in_array($evt_class, $data_format->_messages_supported_class)) {
        $data_format->_ref_sender->_delete_file = false;
        // Pas de cr�ation d'�change dans le cas : 
        // * o� l'on ne souhaite pas traiter le message
        // * o� le sender n'enregistre pas les messages non pris en charge
        if (!$data_format->_to_treatment || !$data_format->_ref_sender->save_unsupported_message) {
          return;
        }

        $exchange_hl7v2->populateExchange($data_format, $evt);
        $exchange_hl7v2->loadRefsInteropActor();
        $exchange_hl7v2->populateErrorExchange(null, $evt);
        
        $ack->_ref_exchange_hl7v2 = $exchange_hl7v2;
        $msgAck = $ack->generateAcknowledgment("AR", "E001", "201");

        $exchange_hl7v2->populateErrorExchange($ack);
        
        return $msgAck;
      }

      $sender = $data_format->_ref_sender;
      $sender->getConfigs($data_format);

      // Acquittement d'erreur d'un document XML recu non valide
      if (!$sender->_configs["bypass_validating"] && !$evt->message->isOK(CHL7v2Error::E_ERROR)) {
        $exchange_hl7v2->populateExchange($data_format, $evt);
        $exchange_hl7v2->loadRefsInteropActor();
        $exchange_hl7v2->populateErrorExchange(null, $evt);

        $ack->_ref_exchange_hl7v2 = $exchange_hl7v2;
        $msgAck = $ack->generateAcknowledgment("AR", "E002", "207");

        $exchange_hl7v2->populateErrorExchange($ack);

        return $msgAck;
      }

      $exchange_hl7v2->populateExchange($data_format, $evt);
      $exchange_hl7v2->message_valide = 1;
      
      // Gestion des notifications ? 
      if (!$exchange_hl7v2->_id) {
        $exchange_hl7v2->date_production      = CMbDT::dateTime();
        $exchange_hl7v2->identifiant_emetteur = $data['identifiantMessage'];
      }

      $exchange_hl7v2->store();
      
      // Pas de traitement du message
      if (!$data_format->_to_treatment) {
        return;
      }

      $exchange_hl7v2->loadRefsInteropActor();

      // Chargement des configs de l'exp�diteur
      $sender = $exchange_hl7v2->_ref_sender;
      $sender->getConfigs($data_format);

      if (!$dom_evt->checkApplicationAndFacility($data, $sender)) {
        return;
      }

      if (!empty($sender->_configs["handle_mode"])) {
        CHL7v2Message::setHandleMode($sender->_configs["handle_mode"]);
      }

      $dom_evt->_ref_exchange_hl7v2 = $exchange_hl7v2;
      $ack->_ref_exchange_hl7v2     = $exchange_hl7v2;

      // Message PAM / DEC / PDQ / SWF
      $msgAck = self::handleEvent($exchange_hl7v2, $dom_evt, $ack, $data);

      CHL7v2Message::resetBuildMode();
    }
    catch(Exception $e) {
      $exchange_hl7v2->populateExchange($data_format, $evt);
      $exchange_hl7v2->loadRefsInteropActor();
      $exchange_hl7v2->populateErrorExchange(null, $evt);
      
      $ack = new CHL7v2Acknowledgment($evt);
      $ack->message_control_id = isset($data['identifiantMessage']) ? $data['identifiantMessage'] : "000000000";
      
      $ack->_ref_exchange_hl7v2 = $exchange_hl7v2;
      $msgAck = $ack->generateAcknowledgment("AR", "E003", "207", "E", $e->getMessage());

      $exchange_hl7v2->populateErrorExchange($ack);
      
      CHL7v2Message::resetBuildMode(); 
    }

    return $msgAck;
  }

  /**
   * Handle event PAM / DEC / PDQ / SWF message
   *
   * @param CExchangeHL7v2     $exchange_hl7v2 Exchange HL7v2
   * @param CHL7v2MessageXML   $dom_evt        DOM Event
   * @param CHL7Acknowledgment $ack            Acknowledgment
   * @param array              $data           Nodes data
   *
   * @return null|string
   */
  static function handleEvent(CExchangeHL7v2 $exchange_hl7v2, CHL7v2MessageXML $dom_evt, CHL7Acknowledgment $ack, $data = array()) {
    $newPatient = new CPatient();
    $newPatient->_eai_exchange_initiator_id = $exchange_hl7v2->_id;

    $data = array_merge($data, $dom_evt->getContentNodes());

    return $dom_evt->handle($ack, $newPatient, $data);
  }
}