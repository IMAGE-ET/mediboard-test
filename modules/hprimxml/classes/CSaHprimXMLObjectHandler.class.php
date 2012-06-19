<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSaHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  static $handled = array ("CSejour", "COperation", "CConsultation");

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
    
    switch ($mbObject->_class) {
      // CSejour 
      // Envoi des actes / diags soit quand le séjour est facturé, soit quand le sejour a une sortie réelle, soit quand on a la clôture sur le sejour
      case 'CSejour': 
        $sejour = $mbObject;
        $sejour->loadNDA($receiver->group_id);
        
        $patient = $sejour->loadRefPatient();
        $patient->loadIPP($receiver->group_id);
        
        if (CAppUI::conf("sa send_only_with_ipp_nda")) {
          if (!$patient->_IPP || !$sejour->_NDA) {
            throw new CMbException("CSaObjectHandler-send_only_with_ipp_nda", UI_MSG_ERROR);
          }
        }
        
        if ($sejour->DP || $sejour->DR || (count($sejour->loadRefDossierMedical()->_codes_cim) > 0)) {
          $evt = (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ? 
                     "CHPrimXMLEvenementsServeurEtatsPatient" : "CHPrimXMLEvenementsPmsi";
                     
          $this->sendEvenementPMSI($evt, $sejour);         
        }
      
        break;
      // COperation
      // Envoi des actes soit quand l'interv est facturée, soit quand on a la clôture sur l'interv
      case 'COperation':
        $operation = $mbObject;
        
        $sejour  = $operation->_ref_sejour;
        $sejour->loadNDA($receiver->group_id);
        
        $patient = $sejour->loadRefPatient();
        $patient->loadIPP($receiver->group_id);
        
        break;
      // CConsultation
      // Envoi des actes dans le cas de la clôture de la cotation
      case 'CConsultation':
        $consultation = $mbObject;
        
        $patient = $consultation->loadRefPatient();
        $patient->loadIPP($receiver->group_id);
        
        $sejour  = $consultation->_ref_sejour;
        $sejour->loadNDA($receiver->group_id);
        
        break; 
    }
    
    if (CAppUI::conf("sa send_only_with_ipp_nda")) {
      if (!$patient->_IPP || !$sejour->_NDA) {
        throw new CMbException("CSaObjectHandler-send_only_with_ipp_nda", UI_MSG_ERROR);
      }
    }

    $codable = $mbObject;
    // Chargement des actes du codable
    $codable->loadRefsActes();  
    
    // Envoi des actes CCAM / NGAP
    if (empty($codable->_ref_actes_ccam) && empty($codable->_ref_actes_ngap)) {
      return;
    }
    
    $this->sendEvenementPMSI("CHPrimXMLEvenementsServeurActes", $codable);   
  }
}
?>