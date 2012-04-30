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
      // Envoi des actes / diags soit quand le séjour est facturé, soit quand le sejour a une sortie réelle, soit quand on a la clôture sur le sejour
      case 'CSejour': 
        $sejour = $mbObject;
        
        $send_only_with_type = CAppUI::conf("sa send_only_with_type");
        if ($send_only_with_type && ($send_only_with_type != $sejour->type)) {
          return;  
        }
        
        switch (CAppUI::conf("sa trigger_sejour")) {
          case 'sortie_reelle':
            if ($sejour->fieldModified('sortie_reelle')) {
              $this->sendFormatAction("onAfterStore", $sejour);
            }
            break;
            
          case 'testCloture':
            if ($sejour->testCloture()) {
              $this->sendFormatAction("onAfterStore", $sejour);
            }
            break;
            
          default:
            if ($sejour->fieldModified('facture', 1)) {
              $this->sendFormatAction("onAfterStore", $sejour);
            }
            break;
        }
        break;
      
      // COperation
      // Envoi des actes soit quand l'interv est facturée, soit quand on a la clôture sur l'interv
      case 'COperation':
        $operation = $mbObject;
        
        $sejour  = $operation->_ref_sejour;
        $send_only_with_type = CAppUI::conf("sa send_only_with_type");
        if ($send_only_with_type && ($send_only_with_type != $sejour->type)) {
          CAppUI::stepAjax("CSaObjectHandler-send_only_with_type", UI_MSG_WARNING, CAppUI::tr("CSejour.type.$sejour->type"));
        }
        
        switch (CAppUI::conf("sa trigger_operation")) {
          case 'testCloture':
            if ($operation->testCloture()) {
              $this->sendFormatAction("onAfterStore", $operation);
            }
            break;
          
          default:
            if ($operation->fieldModified('facture', 1)) {
              $this->sendFormatAction("onAfterStore", $operation);
            }
            break;
        }
        break;
      
      // CConsultation
      // Envoi des actes dans le cas de la clôture de la cotation
      case 'CConsultation':
        $consultation = $mbObject;
        if ($consultation->sejour_id && $consultation->fieldModified("valide", 1)) {
          $this->sendFormatAction("onAfterStore", $consultation);
        }
        break;
        
      default:
        return;
    } 
  }
}
?>