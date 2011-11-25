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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_ihe';
    $spec->key   = 'receiver_ihe_id';
    $spec->messages = array(
      "PAM"   => array ("evenementsPatient"),
      "PAMFr" => array ("evenementsPatient"),
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
      return $matches[1];
    }
    
    return null;
  }
  
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;

    $evenement->build($mbObject);    
    $msg = $evenement->flatten();
    
    if ($this->actif && $msg) {
      $source = CExchangeSource::get("$this->_guid-evenementsPatient");
      if ($source->_id) {
        $exchange = $evenement->_exchange_ihe;
        
        // Dans le cas d'une source file system on passe le nom du fichier en paramètre
        if ($source instanceof CSourceFileSystem) {
          $source->setData($msg, false, "MB-$evenement->event_type-$evenement->code-$exchange->_id".$source->fileextension);
        }
        else {
          $source->setData($msg);
        }
        try {
          $source->send();
        } catch (Exception $e) {
          throw new CMbException("CExchangeSource-no-response");
        }
        $ack_data = $source->getACQ();

        $exchange->date_echange = mbDateTime();
          
        if ($ack_data) {
          if ($exchange->type == "PAM") {
            $data_format = CPAM::getPAMEvent($exchange->code, $exchange->version);
          }
          if ($exchange->type == "PAM_FR") {
            $data_format = CPAMFR::getPAMEvent($exchange->code, $exchange->version);
          }
          
          $ack = new CHL7v2Acknowledgment($data_format);
          $ack->handle($ack_data);
          $exchange->date_echange        = mbDateTime();   
          $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
          $exchange->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
          $exchange->_acquittement       = $ack_data;
          $exchange->store();
        } 
        
        $exchange->store();
      }      
    }
  }
}
?>