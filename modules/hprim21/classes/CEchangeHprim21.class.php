<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 10062 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CEchangeHprim21 extends CExchangeTabular {
  static $messages = array(
    "ADM" => "CADM",
    "REG" => "CREG",
  );
  
  // DB Table key
  var $echange_hprim21_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_hprim21';
    $spec->key   = 'echange_hprim21_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    
    $backProps["complementaire_hprim21"] = "CHprim21Complementaire echange_hprim21_id";
    $backProps["medecins_hprim21"]       = "CHprim21Medecin echange_hprim21_id";
    $backProps["patients_hprim21"]       = "CHprim21Patient echange_hprim21_id";
    $backProps["sejours_hprim21"]        = "CHprim21Sejour echange_hprim21_id";
    
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();

    $props["receiver_id"]  = "ref class|CDestinataireHprim21";
    $props["object_class"] = "enum list|CPatient|CSejour|CMedecin show|0";
    
    $props["_message"]      = "hpr";
    $props["_acquittement"] = "hpr";
    
    return $props;
  }
  
  function handle() {
    return COperatorHPR::event($this);
  }

  function getFamily() {
    return self::$messages;
  }
  
  function isWellFormed($data, CInteropActor $actor = null) {
    try {
      return CHPrim21Message::isWellFormed($data);
    } catch (Exception $e) {
      return false;
    } 
  }
  
  function understand($data, CInteropActor $actor = null) {
    if (!$this->isWellFormed($data, $actor)) {
      return false;
    }
    
    $hpr_message = $this->parseMessage($data, false, $actor);

    $hpr_message_evt = "CHPrim21$hpr_message->event_name".$hpr_message->type;
    
    foreach ($this->getFamily() as $_message) {
      $message_class = new $_message;
      $evenements = $message_class->getEvenements();

      if (in_array($hpr_message_evt, $evenements)) {
        $this->_family_message_class = $_message;
        $this->_family_message       = new $hpr_message_evt;
                
        return true;
      }
    }
  }
  
  /**
   * @return CHPrim21Message
   */
  function getMessage() {
    if ($this->_message !== null) {
      $hpr_message = $this->parseMessage($this->_message);
      
      $this->_doc_errors_msg   = !$hpr_message->isOK(CHL7v2Error::E_ERROR);
      $this->_doc_warnings_msg = !$hpr_message->isOK(CHL7v2Error::E_WARNING);
      
      $this->_message_object   = $hpr_message;

      return $hpr_message;
    }
  }
  
  /**
   * @return CHPrim21Message
   */
  function getACK() {
    if ($this->_acquittement !== null) {
      $hpr_ack = new CHPrim21Message();
      $hpr_ack->parse($this->_acquittement);
      
      $this->_doc_errors_ack   = !$hpr_ack->isOK(CHL7v2Error::E_ERROR);
      $this->_doc_warnings_ack = !$hpr_ack->isOK(CHL7v2Error::E_WARNING);

      return $hpr_ack;
    }
  }
  
  /**
   * @return CHPrim21Message
   */
  function parseMessage($string, $parse_body = true, $actor = null) {
    $hpr_message = new CHPrim21Message();
    
    if (!$this->_id && $actor) {
      $this->sender_id    = $actor->_id;
      $this->sender_class = $actor->_class;
    }

    $hpr_message->parse($string, $parse_body);
    
    return $hpr_message;
  }
  
  function populateExchange(CExchangeDataFormat $data_format, CHPREvent $event) {
    $this->group_id        = $data_format->group_id;
    $this->sender_id       = $data_format->sender_id;
    $this->sender_class    = $data_format->sender_class;
    $this->version         = $event->message->version;
    $this->type            = $event->type_liaison;
    $this->sous_type       = $event->type;
    $this->_message        = $data_format->_message;
  }
  
  function populateErrorExchange(CHPrim21Acknowledgment $ack = null, CHL7Event $event = null) {
    /*if ($ack) {
      $msgAck = $ack->event_ack->msg_hl7;
      $this->_acquittement       = $ack->event_ack->msg_hl7;;
      $this->statut_acquittement = $ack->ack_code;
      $this->acquittement_valide = $ack->event_ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    } 
    else {
      $this->message_valide      = $event->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
      $this->date_production     = mbDateTime();
      $this->date_echange        = mbDateTime();
    }*/

    $this->store();
  }
  
  function populateExchangeACK(CHPrim21Acknowledgment $ack, $object) {
    $msgAck = $ack->event_err->msg_hpr;

    $this->statut_acquittement = $ack->ack_code;
    $this->acquittement_valide = $ack->event_err->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;

    $this->_acquittement = $msgAck;
    $this->date_echange  = mbDateTime();
    $this->store();
    
    return $msgAck;
  }
  
  function setAckI(CHPrim21Acknowledgment $ack, $errors, CMbObject $object = null) {
    $ack->generateAcknowledgment("I", $errors, $object);
        
    return $this->populateExchangeACK($ack, $object);
  }
  
  function setAckP(CHPrim21Acknowledgment $ack, $errors, CMbObject $object = null) {
    $ack->generateAcknowledgment("P", $errors, $object);
        
    return $this->populateExchangeACK($ack, $object);
  }
  
  function setAckT(CHPrim21Acknowledgment $ack, $mb_error_codes, $comments = null, CMbObject $mbObject = null) {
    //$ack->generateAcknowledgment("T", $mb_error_codes, "0", "T", $comments, $object);

    return $this->populateExchangeACK($ack, $object);               
  }
}
?>