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

    // Récupération des informations du message - CHL7v2MessageXML
    $dom_evt = $evt->handle($msg);
    $dom_evt->_is_i18n = $evt->_is_i18n;
    
    try {
      // Création de l'échange
      $exchange_ihe = new CExchangeIHE();
      $exchange_ihe->load($data_format->_exchange_id);
      
      // Récupération des données du segment MSH
      $data = $dom_evt->getMSHEvenementXML();

      // Gestion de l'acquittement
      /* @todo Gestion que de la version 2 pour le moment */
      $ack = new CHL7v2Acknowledgment($evt);
      $ack->message_control_id = $data['identifiantMessage'];

      // Message non supporté pour cet utilisateur
      $evt_class = CHL7Event::getEventClass($evt);
      if (!in_array($evt_class, $data_format->_messages_supported_class)) {
        $data_format->_ref_sender->_delete_file = false;
        // Pas de création d'échange dans le cas : 
        // * où l'on ne souhaite pas traiter le message
        // * où le sender n'enregistre pas les messages non pris en charge
        if (!$data_format->_to_treatment || !$data_format->_ref_sender->save_unsupported_message) {
          return;
        }

        $exchange_ihe->populateExchange($data_format, $evt);
        $exchange_ihe->loadRefsInteropActor();
        $exchange_ihe->populateErrorExchange(null, $evt);
        
        $ack->_ref_exchange_ihe = $exchange_ihe;
        $msgAck = $ack->generateAcknowledgment("AR", "E001", "201");
        
        $exchange_ihe->populateErrorExchange($ack);
        
        return $msgAck;
      }
   
      // Acquittement d'erreur d'un document XML recu non valide
      if (!$evt->message->isOK(CHL7v2Error::E_ERROR)) {
        $exchange_ihe->populateExchange($data_format, $evt);
        $exchange_ihe->loadRefsInteropActor();
        $exchange_ihe->populateErrorExchange(null, $evt);
        
        $ack->_ref_exchange_ihe = $exchange_ihe;
        $msgAck = $ack->generateAcknowledgment("AR", "E002", "207");

        $exchange_ihe->populateErrorExchange($ack);

        return $msgAck;
      }
      
      $exchange_ihe->populateExchange($data_format, $evt);
      $exchange_ihe->message_valide = 1;
      
      // Gestion des notifications ? 
      if (!$exchange_ihe->_id) {
        $exchange_ihe->date_production      = mbDateTime();
        $exchange_ihe->identifiant_emetteur = $data['identifiantMessage'];
      }
      
      $exchange_ihe->store();
      
      // Pas de traitement du message
      if (!$data_format->_to_treatment) {
        return;
      }

      $exchange_ihe->loadRefsInteropActor();

      // Chargement des configs de l'expéditeur
      $sender = $exchange_ihe->_ref_sender;
      $sender->getConfigs($data_format);
			
			CHL7v2Message::setHandleMode($sender->_configs["handle_mode"]); 

      $dom_evt->_ref_exchange_ihe = $exchange_ihe;
      $ack->_ref_exchange_ihe     = $exchange_ihe;

      // Message PAM / DEC 
      $msgAck = self::handleEvent($data, $exchange_ihe, $dom_evt, $ack);
      
      CHL7v2Message::resetBuildMode(); 			
    } catch(Exception $e) {
      $exchange_ihe->populateExchange($data_format, $evt);
      $exchange_ihe->loadRefsInteropActor();
      $exchange_ihe->populateErrorExchange(null, $evt);
      
      $ack = new CHL7v2Acknowledgment($evt);
      $ack->message_control_id = isset($data['identifiantMessage']) ? $data['identifiantMessage'] : "000000000";
      
      $ack->_ref_exchange_ihe = $exchange_ihe;
      $msgAck = $ack->generateAcknowledgment("AR", "E003", "207", "E", $e->getMessage());

      $exchange_ihe->populateErrorExchange($ack);
      
			CHL7v2Message::resetBuildMode(); 
    }

    return $msgAck;
  }
  
  static function handleEvent($data = array(), CExchangeIHE $exchange_ihe, CHL7v2MessageXML $dom_evt, CHL7Acknowledgment $ack) {
    $newPatient = new CPatient();
    $newPatient->_eai_exchange_initiator_id = $exchange_ihe->_id;
    
    $data = array_merge($data, $dom_evt->getContentNodes());
    
    //$exchange_ihe->id_permanent = array_key_exists("PI", $data['personIdentifiers']) ? $data['personIdentifiers']['PI'] : null;

    return $dom_evt->handle($ack, $newPatient, $data);
  }
}

?>