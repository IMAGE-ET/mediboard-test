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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_ihe';
    $spec->key   = 'receiver_ihe_id';
    $spec->messages = array(
      "PAM" => array ( 
        "evenementsPatient" 
      ),
      "PAMFr" => array ( 
        "evenementsPatient" 
      ),
    );
    return $spec;
  }
  
  function getBackProps() {
    $backProps                   = parent::getBackProps();
    $backProps['object_configs'] = "CReceiverIHEConfig object_id";
    $backProps['echanges']       = "CExchangeIHE receiver_id";
    
    return $backProps;
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
  
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;
    
    $evenement->build($mbObject);    
    $msg = $evenement->flatten();
    
  }
}
?>