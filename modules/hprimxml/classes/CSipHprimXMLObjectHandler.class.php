<?php

/**
 * SIP H'XML Object handler
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSipHprimXMLObjectHandler
 * SIP H'XML Object handler
 */

class CSipHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  static $handled = array ("CPatient", "CCorrespondantPatient");

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
   * @throws CMbException
   *
   * @return bool
   */
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
        return false;
      }
      
      $mbObject->_id400 = null;
      $idexPatient = new CIdSante400();
      $idexPatient->loadLatestFor($mbObject, $receiver->_tag_patient);
      $mbObject->_id400 = $idexPatient->id400;
      
      $this->generateTypeEvenement("CHPrimXMLEnregistrementPatient", $mbObject, true, $initiateur);
    }
    // Si Client
    else {
      if (!$receiver->isMessageSupported("CHPrimXMLEnregistrementPatient")) {
        return false;
      }

      if (!$mbObject->_IPP) {
        // Génération de l'IPP dans le cas de la création, ce dernier n'était pas créé
        if ($msg = $mbObject->generateIPP()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }

        $IPP = new CIdSante400();
        $IPP->loadLatestFor($mbObject, $receiver->_tag_patient);
        $mbObject->_IPP = $IPP->id400;
      }

      // Envoi pas les patients qui n'ont pas d'IPP
      if (!$receiver->_configs["send_all_patients"] && !$mbObject->_IPP) {
        return false;
      }

      $this->sendEvenementPatient("CHPrimXMLEnregistrementPatient", $mbObject);
      
      $mbObject->_IPP = null;
    }

    return true;
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return bool
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    return true;
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @throws CMbException
   *
   * @return bool
   */
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

        /** @var CInteropSender $sender */
        $sender = CMbObject::loadFromGuid($mbObject->_eai_sender_guid);
        if ($sender->group_id == $receiver->group_id) {
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
          if ($patient2_ipp) {
            $patient->_IPP = $patient2_ipp;
          }

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

    return true;
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    return true;
  }
}