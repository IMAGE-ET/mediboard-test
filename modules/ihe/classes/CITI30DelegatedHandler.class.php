<?php

/**
 * ITI30 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI30DelegatedHandler 
 * ITI30 Delegated Handler
 */
class CITI30DelegatedHandler extends CITIDelegatedHandler {
  /**
   * @var array
   */
  static $handled     = array ("CPatient", "CCorrespondantPatient", "CIdSante400");
  /**
   * @var string
   */
  public $profil      = "PAM";
  /**
   * @var string
   */
  public $message     = "ADT";
  /**
   * @var string
   */
  public $transaction = "ITI30";

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
      return false;
    }

    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);
    
    $eai_initiateur_group_id = $mbObject->_eai_initiateur_group_id;
    
    // Création/MAJ d'un correspondant patient
    if ($mbObject instanceof CCorrespondantPatient) {
      if (!$mbObject->patient_id) {
        return; 
      }
      
      $mbObject                           = $mbObject->loadRefPatient();
      $mbObject->_receiver                = $receiver;
      $mbObject->_eai_initiateur_group_id = $eai_initiateur_group_id;
      
      $code = "A31";
    }
    
    // MAJ de l'IPP du patient
    elseif ($mbObject instanceof CIdSante400) {
      $idex = $mbObject;
      
      // Concerne pas les patients / Pas en mode modification
      if ($idex->object_class != "CPatient" || !$idex->_old->_id) {
        return;
      }

      // Pas un tag IPP
      if ($idex->tag != CPatient::getTagIPP()) {
        return;
      }
     
      // Vraiment une modif de l'idex ?
      if ($idex->id400 == $idex->_old->id400) {
        return;
      }
      
      $code = "A47";
      
      $patient = new CPatient();
      $patient->load($idex->object_id);
      $patient->_receiver = $receiver;
      
      $patient->_patient_elimine = clone $patient;
      
      // Affecte le nouvel IPP au patient
      $patient->_IPP = $idex->id400;
      
      // Affecte l'ancien IPP au "patient éliminé"
      $patient->_patient_elimine->_IPP = $idex->_old->id400;

      if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }
      
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
      
      return;
    }
    // Création/MAJ d'un patient
    else {
      switch ($mbObject->_ref_current_log->type) {
        case "create":
          $code = "A28";
          break;
        case "store":
          // Patient lié
          if ($mbObject->fieldModified("patient_link_id")) {
            $code = "A24";
            break;
          }
          
          // Annulation de la liaison avec le patient lié
          if ($mbObject->_old->patient_link_id && !$mbObject->patient_link_id) {
            $code = "A37";
            break;
          }
          
          // Dans tous les autres cas il s'agit d'une modification
          $code = ($receiver->_configs["send_update_patient_information"] == "A08") ? "A08" : "A31";
          break;
        default:
          $code = null;
          break;
      }
    }
    
    $patient = $mbObject;
    if ($patient->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
      return;
    }
     
    if (!$patient->_IPP) {
      // Génération de l'IPP dans le cas de la création, ce dernier n'était pas créé
      if ($msg = $patient->generateIPP()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
      
      $IPP = new CIdSante400();
      $IPP->loadLatestFor($patient, $receiver->_tag_patient);
      $patient->_IPP = $IPP->id400;
    }

    // Envoi pas les patients qui n'ont pas d'IPP
    if (!$receiver->_configs["send_all_patients"] && !$patient->_IPP) {
      return;
    }
    
    $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
    
    $patient->_IPP = null;
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    if ($mbObject instanceof CPatient) {  
      $patient = $mbObject;
      $patient->check();
      $patient->updateFormFields();
      
      $receiver = $mbObject->_receiver; 
      $receiver->getInternationalizationCode($this->transaction);

      foreach ($patient->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }      

        $patient1_ipp     = $patient->_IPP = $infos_fus["patient1_ipp"];
        
        $patient_eliminee = $infos_fus["patientElimine"];
        $patient2_ipp     = $patient_eliminee->_IPP = $infos_fus["patient2_ipp"];
  
        // Cas 0 IPP : Aucune notification envoyée
        if (!$patient1_ipp && !$patient2_ipp) {
          continue;
        }
  
        // Cas 1 IPP : Pas de message de fusion mais d'une modification du patient
        if ($patient1_ipp xor $patient2_ipp) {
          if ($patient2_ipp) {
            $patient->_IPP = $patient2_ipp;
          }

          $code = ($receiver->_configs["send_update_patient_information"] == "A08") ? "A08" : "A31";
          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }
            
          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
          continue;
        }
        
        // Cas 2 IPPs : Message de fusion
        if ($patient1_ipp && $patient2_ipp) {
          $patient->_patient_elimine = $patient_eliminee;
          
          if (!$this->isMessageSupported($this->transaction, $this->message, "A40", $receiver)) {
            return;
          }
          
          $this->sendITI($this->profil, $this->transaction, $this->message, "A40", $patient);
          continue;
        }
      } 
    } 
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
      return false;
    }
    
    return true;
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
      return false;
    }

    // On gère seulement la suppression des correspondants patient
    if (!$mbObject instanceof CCorrespondantPatient) {
      return;
    }
    
    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);
    
    $patient = $mbObject->loadRefPatient();
    $patient->_receiver = $receiver;
    
    $code = ($receiver->_configs["send_update_patient_information"] == "A08") ? "A08" : "A31";
        
    if ($patient->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
      return;
    }
    
    // Envoi pas les patients qui n'ont pas d'IPP
    if (!$receiver->_configs["send_all_patients"] && !$patient->_IPP) {
      return;
    }
    
    $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
  }
}