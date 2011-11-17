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
  static $handled        = array ("CPatient");
  protected $profil      = "PAM";
  protected $transaction = "ITI30";
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    if (!$mbObject->_IPP) {
      $IPP = new CIdSante400();
      $IPP->loadLatestFor($mbObject, $receiver->_tag_patient);
      
      $mbObject->_IPP = $IPP->id400;
    }

    // Envoi pas les patients qui n'ont pas d'IPP
    if (!$receiver->_configs["send_all_patients"] && !$mbObject->_IPP) {
      return;
    }

    switch ($mbObject->loadLastLog()->type) {
      case "create":
        $code = "A28";
        break;
      case "store":
        $code = "A31";
        break;
      default:
        $code = null;
        break;
    }
    
    $this->sendITI($this->profil, $this->transaction, $code, $mbObject);
    
    $mbObject->_IPP = null;
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
    
    foreach ($mbObject->_fusion as $group_id => $infos_fus) {
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
      if ((!$patient1_ipp && $patient2_ipp) || ($patient1_ipp && !$patient2_ipp)) {
        if ($patient2_ipp)
          $patient->_IPP = $patient2_ipp;

        $this->sendITI($this->profil, $this->transaction, "A31", $patient);
        continue;
      }
      
      // Cas 2 IPPs : Message de fusion
      if ($patient1_ipp && $patient2_ipp) {
        $patient->_patient_elimine = $patient_eliminee;
        
        $this->sendITI($this->profil, $this->transaction, "A40", $patient);
        continue;
      }
    }  
  }  
}
?>