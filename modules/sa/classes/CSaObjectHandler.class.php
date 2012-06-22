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
              
              if (CAppUI::conf("sa send_actes_consult")) {
                if ($sejour->loadRefsConsultations()) {
                  foreach ($sejour->_ref_consultations as $_consultation) {
                    if (!$_consultation->sejour_id || !$_consultation->valide) {
                      continue;
                    }  
                    
                    $sejour = $_consultation->loadRefSejour();
                    $this->sendFormatAction("onAfterStore", $_consultation);
                  }
                }
              }
              
              if (CAppUI::conf("sa send_actes_interv")) {
                if ($sejour->loadRefsOperations()) {
                  foreach ($sejour->_ref_operations as $_operation) {
                    $this->sendFormatAction("onAfterStore", $_operation);
                  }
                }
              }
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
        
        switch (CAppUI::conf("sa trigger_operation")) {
          case 'testCloture':
            if ($operation->testCloture()) {
              $this->sendFormatAction("onAfterStore", $operation);
            }
            break;
          
          case 'sortie_reelle':
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
        
        if ($consultation->sejour_id) {
          $sejour = $consultation->loadRefSejour();
          
          switch (CAppUI::conf("sa trigger_consultation")) {
            case 'sortie_reelle':
              break;
            
            default:
              if ($consultation->fieldModified('valide', 1)) {
                $this->sendFormatAction("onAfterStore", $consultation);
              }
              break;
          }
        }
       
        break; 
      default:
        return;
    } 
  }
}
?>
