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
  static $handled = array ("COperation");

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
        
    $receiver = $mbObject->_receiver;
    if (CGroups::loadCurrent()->_id != $receiver->group_id) {
      return;
    }

    $operation = $mbObject;
        
    $sejour  = $operation->_ref_sejour;
    $sejour->loadNDA($receiver->group_id);
    
    $patient = $sejour->loadRefPatient();
    $patient->loadIPP($receiver->group_id);
    
    // Chargement des actes du codable
    $operation->loadRefsActes();  
    $operation->completeField("date");
    
    $this->sendEvenementPMSI("CHPrimXMLEvenementsServeurIntervention", $operation);   
  }
}
?>