<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12588 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  static $handled = array ("CPatient", "CCorrespondantPatient");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    
    if ($mbObject instanceof CCorrespondantPatient) {
      $mbObject = $mbObject->loadRefPatient();
      $mbObject->_receiver = $receiver;
    }
    
    // Si Serveur
    if (CAppUI::conf('sip server')) {  
      $echange_hprim = new CEchangeHprim();
      if (isset($mbObject->_eai_exchange_initiator_id)) {
        $echange_hprim->load($mbObject->_eai_exchange_initiator_id);
      }

      $initiateur = ($receiver->_id == $echange_hprim->sender_id) ? $echange_hprim->_id : null;
      
      $group = new CGroups();
      $group->load($receiver->group_id);
      $group->loadConfigValues();
      
      if (!$initiateur && !$group->_configs["sip_notify_all_actors"]) {
        return;
      }
      
      $mbObject->_id400 = null;
      $id400Patient = new CIdSante400();
      $id400Patient->loadLatestFor($mbObject, $receiver->_tag_patient);
      $mbObject->_id400 = $id400Patient->id400;
      
      $this->generateTypeEvenement("CHPrimXMLEnregistrementPatient", $mbObject, true, $initiateur);
    }
    // Si Client
    else {
      if ($mbObject->_eai_initiateur_group_id || !$receiver->isMessageSupported("CHPrimXMLEnregistrementPatient")) {
        return;
      }
      
      if (!$mbObject->_IPP) {
        $IPP = new CIdSante400();
        $IPP->loadLatestFor($mbObject, $receiver->_tag_patient);
        
        $mbObject->_IPP = $IPP->id400;
      }
  
      // Envoi pas les patients qui n'ont pas d'IPP
      if (!$receiver->_configs["send_all_patients"] && !$mbObject->_IPP) {
        return;
      }
      
      $this->sendEvenementPatient("CHPrimXMLEnregistrementPatient", $mbObject);
      
      $mbObject->_IPP = null;
    }
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    $patient = $mbObject;
    $patient->check();
    $patient->updateFormFields();
    
    $receiver = $mbObject->_receiver;    
    
    // Si Client
    if (!CAppUI::conf('sip server')) {
      foreach ($mbObject->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }      
      
        if ($mbObject->_eai_initiateur_group_id == $receiver->group_id) {
          continue;
        }
        
        $patient1_ipp = $patient->_IPP = $infos_fus["patient1_ipp"];
        
        $patient_eliminee = $infos_fus["patientElimine"];
        $patient2_ipp = $patient_eliminee->_IPP = $infos_fus["patient2_ipp"];

        // Cas 0 IPP : Aucune notification envoyée
        if (!$patient1_ipp && !$patient2_ipp) {
          continue;
        }
       
        // Cas 1 IPP : Pas de message de fusion mais d'une modification du patient
        if ((!$patient1_ipp && $patient2_ipp) || ($patient1_ipp && !$patient2_ipp)) {
          if ($patient2_ipp)
            $patient->_IPP = $patient2_ipp;

          $this->sendEvenementPatient("CHPrimXMLEnregistrementPatient", $patient);
          continue;
        }
        
        // Cas 2 IPPs : Message de fusion
        if ($patient1_ipp && $patient2_ipp) {
          $patient->_patient_elimine = $patient_eliminee;
          
          $this->sendEvenementPatient("CHPrimXMLFusionPatient", $patient);
          continue;
        }
      }        
    }
  }  

  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }
}
?>