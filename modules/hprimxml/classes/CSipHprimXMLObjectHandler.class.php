<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12588 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  static $handled = array ("CPatient");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    // Si Serveur
    if (CAppUI::conf('sip server')) {  
      $echange_hprim = new CEchangeHprim();
      if (isset($mbObject->_hprim_initiator_id)) {
        $echange_hprim->load($mbObject->_hprim_initiator_id);
      }

      $initiateur = ($receiver->_id == $echange_hprim->sender_id) ? $echange_hprim->_id : null;
      
      if (!$initiateur && !CAppUI::conf('sip notify_all_destinataires')) {
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
      if ($mbObject->_hprim_initiateur_group_id || !$receiver->isMessageSupported("CHPrimXMLEnregistrementPatient")) {
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

    $patient = $mbObject;

    $patient_elimine = new CPatient();
    $patient_elimine->load(reset($mbObject->_merging));

    // Si Client
    if (!CAppUI::conf('sip server')) {
      $mbObject->_fusion = array();
      foreach (CGroups::loadGroups() as $_group) {
        if ($mbObject->_hprim_initiateur_group_id == $_group->_id) {
          continue;
        }
        
        $patient->_IPP = null;
        $patient->loadIPP($_group->_id);
        $patient1_ipp = $patient->_IPP;
        
        $patient_elimine->_IPP = null;
        $patient_elimine->loadIPP($_group->_id);
        $patient2_ipp = $patient_elimine->_IPP;
        
        // Passage en trash des IPP des patients
        $tap_IPP = CPatient::getTagIPP($_group->_id);
        
        $id400Patient               = new CIdSante400();
        $id400Patient->tag          = $tap_IPP;
        $id400Patient->object_class = "CPatient";
        $id400Patient->object_id    = $patient->_id;
        $id400sPatient = $id400Patient->loadMatchingList();
        
        $id400PatientElimine               = new CIdSante400();
        $id400PatientElimine->tag          = $tap_IPP;
        $id400PatientElimine->object_class = "CPatient";
        $id400PatientElimine->object_id    = $patient_elimine->_id;
        $id400sPatientElimine = $id400PatientElimine->loadMatchingList();

        $id400s = array_merge($id400sPatient, $id400sPatientElimine);

        if (count($id400s) > 1) {
          foreach ($id400s as $_id_400) {
            // On continue pour ne pas mettre en trash l'IPP du patient que l'on garde
            if ($_id_400->id400 == $patient1_ipp) {
              continue;
            }
            $_id_400->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$tap_IPP;
            $_id_400->last_update = mbDateTime();
            $_id_400->store();
          }
        }

        $mbObject->_fusion[$_group->_id] = array (
          "patientElimine" => $patient_elimine,
          "patient1_ipp"   => $patient1_ipp,
          "patient2_ipp"   => $patient2_ipp,
        );
      }        
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
      
        if ($mbObject->_hprim_initiateur_group_id == $receiver->group_id) {
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
}
?>