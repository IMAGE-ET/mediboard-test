<?php

/**
 * Receiver IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CReceiverIHE 
 * Receiver IHE
 */

class CReceiverIHE extends CInteropReceiver {
  // DB Table key
  var $receiver_ihe_id  = null;
  
  var $_extension       = null;
  var $_i18n_code       = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_ihe';
    $spec->key   = 'receiver_ihe_id';
    $spec->messages = array(
      "PAM"    => array ("evenementsPatient"),
      "PAM_FR" => array ("evenementsPatient"),
      "DEC"    => array ("evenementsObservation"),
    );
    return $spec;
  }
  
  function getBackProps() {
    $backProps                   = parent::getBackProps();
    $backProps['object_configs'] = "CReceiverIHEConfig object_id";
    $backProps['echanges']       = "CExchangeIHE receiver_id";
    
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    if (!$this->_configs) {
      $this->loadConfigValues();
    }
  }
  
  function loadRefsExchangesSources() {
    if (!$this->_ref_msg_supported_family) {
      $this->getMessagesSupportedByFamily();
    }

    $this->_ref_exchanges_sources = array();
    foreach ($this->_ref_msg_supported_family as $_evenement) {
      $this->_ref_exchanges_sources[$_evenement] = CExchangeSource::get("$this->_guid-$_evenement", null, true, $this->_type_echange);
    }
  }
  
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    $ihe_object_handlers = CIHE::getObjectHandlers();
    $object_handler_class  = get_class($objectHandler);
    if (array_key_exists($object_handler_class, $ihe_object_handlers)) {
      return new $ihe_object_handlers[$object_handler_class];
    }
  }
  
  function getHL7Version($transaction) {
    $iti_hl7_version = $this->_configs[$transaction."_HL7_version"];
    foreach (CHL7::$versions as $_version => $_sub_versions) {      
      if (in_array($iti_hl7_version, $_sub_versions)) {
        return $_version;
      }
    }
  }
  
  function getInternationalizationCode($transaction) {
    $iti_hl7_version = $this->_configs[$transaction."_HL7_version"];
    
    if (preg_match("/([A-Z]{2})_(.*)/", $iti_hl7_version, $matches)) {
      $this->_i18n_code = $matches[1];
    }
    
    return $this->_i18n_code;
  }
  
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;
    
    // build_mode = Mode simplifié lors de la génération du message
    $this->loadConfigValues();
    CHL7v2Message::setBuildMode($this->_configs["build_mode"]); 
    $evenement->build($mbObject);  
    CHL7v2Message::resetBuildMode(); 

    if (!$msg = $evenement->flatten()) {
      return;
    }
    
    $source = CExchangeSource::get("$this->_guid-evenementsPatient");
   
    if (!$source->_id || !$source->active) {
      return;
    }
    
    $exchange = $evenement->_exchange_ihe;

    if ($this->_configs["encoding"] == "UTF-8") {
      $msg = utf8_encode($msg); 
    }
    
    $source->setData($msg, null, $exchange);
    try {
      $source->send();
    } catch (Exception $e) {
      throw new CMbException("CExchangeSource-no-response");
    }
    
    $exchange->date_echange = mbDateTime();

    $ack_data = $source->getACQ();
    if (!$ack_data) {
      $exchange->store();
      return;
    }  
    
    $data_format = CIHE::getEvent($exchange);

    $ack = new CHL7v2Acknowledgment($data_format);
    $ack->handle($ack_data);
    $exchange->date_echange        = mbDateTime();   
    $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
    $exchange->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange->_acquittement       = $ack_data;
    $exchange->store();
  }
}
?>