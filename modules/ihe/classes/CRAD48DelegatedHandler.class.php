<?php

/**
 * RAD48 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CRAD48DelegatedHandler 
 * RAD48 Delegated Handler
 */
class CRAD48DelegatedHandler extends CITIDelegatedHandler {
  static $handled        = array ("CConsultation");
  protected $profil      = "SWF";
  protected $message     = "SIU";
  protected $transaction = "RAD48";
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * @see parent::onAfterStore()
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $consultation = $mbObject;
    $consultation->loadLastLog();
    
    // Récupération du code du trigger    
    $code = $this->getCode($consultation);
    
    if ($consultation->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $consultation->_receiver)) {
      return;
    }
    
    $this->sendITI($this->profil, $this->transaction, $this->message, $code, $consultation);    
  }

  /**
   * @see parent::onBeforeDelete()
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    return true;
  }

  /**
   * @see parent::onAfterDelete()
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    return true;
  }
  
  function getCode(CMbObject $scheduling) {
    $current_log = $scheduling->loadLastLog();
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $receiver = $scheduling->_receiver;
    $configs  = $receiver->_configs;
    
    $scheduling->loadOldObject();
    
    // Création d'un rendez-vous
    if ($current_log->type == "create") {
      return "S12";
    } 
    
    // Déplacement d'un rendez-vous (heure ou plageconsult_id)
    if ($scheduling->fieldModified("heure") || $scheduling->fieldModified("plageconsult_id")) {
      return "S13";
    }
    
    // Modification d'un rendez-vous
    if ($scheduling->fieldModified("annule", "1")) {
      return "S15";
    }
    
    // Annulation d'un rendez-vous
    return "S14";
  }
}
