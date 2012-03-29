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
    
    // Envoi des diags du séjour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      if ($sejour->DP || $sejour->DR || (count($sejour->loadRefDossierMedical()->_codes_cim) > 0)) {
        $evt = (CAppUI::conf("hprimxml send_diagnostic") == "evt_serveuretatspatient") ? 
                   "CHPrimXMLEvenementsServeurEtatsPatient" : "CHPrimXMLEvenementsPmsi";
                   
        $this->sendEvenementPMSI($evt, $sejour);         
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