<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSaObjectHandler extends CEAIObjectHandler {
  static $handled = array ("CSejour", "COperation", "CConsultation");

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    switch ($mbObject->_class) {
      // CSejour 
      // Envoi des actes / diags soit quand le séjour est facturé, soit quand le sejour a une sortie réelle
      case 'CSejour': 
        if ($mbObject->fieldModified('facture', 1)) {
          $this->sendFormatAction("onAfterStore", $mbObject);
        }
        
        /*if ($mbObject->fieldModified('sortie_reelle')) {
          $this->sendFormatAction("onAfterStore", $mbObject);
        }*/
        
        break;
      
      // COperation
      // Envoi des actes soit quand l'interv est facturée, soit quand on a la clôture sur l'interv
      case 'COperation':
        if ($mbObject->fieldModified('facture', 1)) {
          $this->sendFormatAction("onAfterStore", $mbObject);
        }
        
        // if $mbObject->testCloture()
        break;
      
      // CConsultation
      // Envoi des actes dans le cas de la clôture de la cotation
      case 'CConsultation':
        if ($mbObject->sejour_id && $mbObject->fieldModified("valide", 1)) {
          $this->sendFormatAction("onAfterStore", $mbObject);
        }
        break;
        
      default:
        return;
    } 
  }
}
?>