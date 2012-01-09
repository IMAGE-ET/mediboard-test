<?php

/**
 * Exchange IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeIHE 
 * Exchange IHE
 */

class CExchangeIHE extends CExchangeTabular {
  static $messages = array(
    "PAM"    => "CPAM",
    "PAM_FR" => "CPAMFR",
  );
  
  // DB Table key
  var $exchange_ihe_id = null;
  
  var $code            = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'exchange_ihe';
    $spec->key   = 'exchange_ihe_id';
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["receiver_id"]   = "ref class|CReceiverIHE"; 
    $props["object_class"]  = "enum list|CPatient|CSejour|COperation|CAffectation show|0";
    $props["code"]          = "str";
    
    $props["_message"]      = "er7";
    $props["_acquittement"] = "er7";

    return $props;
  }
  
  function handle() {
    return COperatorIHE::event($this);
  }

  function getFamily() {
    return self::$messages;
  }
  
  function isWellFormed($data) {
    return CHL7v2Message::isWellFormed($data);
  }
  
  function getConfigs($actor_guid) {
    list($sender_class, $sender_id) = explode("-", $actor_guid);
    
    $sender_hl7_config = new CHL7Config();
    $sender_hl7_config->sender_class = $sender_class;
    $sender_hl7_config->sender_id    = $sender_id;
    $sender_hl7_config->loadMatchingObject();
    
    return $this->_configs_format = $sender_hl7_config;
  }
  
  function understand($data, CInteropActor $actor = null) {
    if (!$this->isWellFormed($data)) {
      return false;
    }
    
    $hl7_message = new CHL7v2Message();
    $hl7_message->parse($data, false);
    
    $hl7_message_evt = "CHL7Event$hl7_message->event_name";

    foreach ($this->getFamily() as $_message) {
      $message_class = new $_message;
      $evenements = $message_class->getEvenements();

      if (in_array($hl7_message_evt, $evenements)) {
        if (!$hl7_message->i18n_code) {
          $this->_family_message_class = $_message;
          $this->_family_message       = CHL7Event::getEventVersion($hl7_message->version, $hl7_message->event_name);
        }
        else {
          $this->_family_message_class = $_message.$hl7_message->i18n_code;
          $this->_family_message       = CHL7Event::getEventVersion($hl7_message->version, $hl7_message->getI18NEventName());
        }
        
        return true;
      }
    }
  }
  
  function getErrors() {}
  
  function getMessage() {
    if ($this->_message !== null) {
      $hl7_message = new CHL7v2Message();
      $hl7_message->parse($this->_message);
      $this->_doc_errors_msg   = !$hl7_message->isOK(CHL7v2Error::E_ERROR);
      $this->_doc_warnings_msg = !$hl7_message->isOK(CHL7v2Error::E_WARNING);

      return $hl7_message;
    }
  }
  
  function getACK() {
    if ($this->_acquittement !== null) {
      $hl7_ack = new CHL7v2Message();
      $hl7_ack->parse($this->_acquittement);
      $this->_doc_errors_ack   = !$hl7_ack->isOK(CHL7v2Error::E_ERROR);
      $this->_doc_warnings_ack = !$hl7_ack->isOK(CHL7v2Error::E_WARNING);

      return $hl7_ack;
    }
  }
 
  function populateExchange(CExchangeDataFormat $data_format, CHL7Event $event) {
    $this->group_id        = $data_format->group_id;
    $this->sender_id       = $data_format->sender_id;
    $this->sender_class    = $data_format->sender_class;
    $this->version         = $event->message->extension ? $event->message->extension : $event->message->version;
    $this->type            = $event->profil;
    $this->sous_type       = $event->transaction;
    $this->code            = $event->code;
    $this->_message        = $data_format->_message;;
  }
  
  function populateErrorExchange($msgAck) {
    $this->_acquittement       = $msgAck;
    $this->statut_acquittement = null;
    $this->message_valide      = 0;
    $this->acquittement_valide = null;
    $this->date_production     = mbDateTime();
    $this->date_echange        = mbDateTime();
    $this->store();
  }
  
  function populateExchangeACK(CHL7Acknowledgment $ack, $mbObject) {
    $msgAck = $ack->event_ack->msg_hl7;
    
    $this->statut_acquittement = $ack->ack_code;
    $this->acquittement_valide = $ack->event_ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    if ($mbObject) {
      $this->setObjectIdClass($mbObject);
    }
    $this->_acquittement = $msgAck;
    $this->date_echange = mbDateTime();
    $this->store();
    
    return $msgAck;
  }
  
  function setAckAA(CHL7Acknowledgment $ack, $mb_error_codes, $comments = null, CMbObject $mbObject = null) {
    $ack->generateAcknowledgment("AA", $mb_error_codes, "0", "I", $comments, $mbObject);
        
    return $this->populateExchangeACK($ack, $mbObject);
  }
  
  function setAckAR(CHL7Acknowledgment $ack, $mb_error_codes, $comments = null, CMbObject $mbObject = null) {
    $ack->generateAcknowledgment("AR", $mb_error_codes, "207", "E", $comments, $mbObject);
    
    return $this->populateExchangeACK($ack, $mbObject);               
  }
}
?>