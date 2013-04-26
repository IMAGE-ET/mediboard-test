<?php

/**
 * SA Event H'XML Handler
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSaEventHprimXMLObjectHandler
 * SA Event H'XML Handler
 */

class CSaEventHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  /**
   * @var array
   */
  static $handled = array ("COperation");

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
        
    $receiver = $mbObject->_receiver;
    if (CGroups::loadCurrent()->_id != $receiver->group_id) {
      return;
    }
    
    if ($mbObject->_eai_initiateur_group_id || !$receiver->isMessageSupported("CHPrimXMLEvenementsServeurIntervention")) {
      return;
    }

    $operation = $mbObject;
        
    $sejour  = $operation->_ref_sejour;
    $sejour->loadNDA($receiver->group_id);
    
    $patient = $sejour->loadRefPatient();
    $patient->loadIPP($receiver->group_id);
    
    // Chargement des actes du codable
    $operation->loadRefsActes(); 
    
    $this->sendEvenementPMSI("CHPrimXMLEvenementsServeurIntervention", $operation);   
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */  
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */  
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
  }
}