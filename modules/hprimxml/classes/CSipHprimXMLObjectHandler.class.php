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
      $patient = $mbObject->loadRefPatient();
      $patient->_receiver = $receiver;
    }
    else {
      $patient = $mbObject;
    }

    // Si Serveur
    if (CAppUI::conf('sip server')) {  
      $echange_hprim = new CEchangeHprim();
      if (isset($patient->_eai_exchange_initiator_id)) {
        $echange_hprim->load($patient->_eai_exchange_initiator_id);
      }

      $initiateur = ($receiver->_id == $echange_hprim->sender_id) ? $echange_hprim->_id : null;
      
      $group = new CGroups();
      $group->load($receiver->group_id);
      $group->loadConfigValues();
      
      if (!$initiateur && !$group->_configs["sip_notify_all_actors"]) {
        return false;
      }

      $patient->_id400 = null;
      $idexPatient = new CIdSante400();
      $idexPatient->loadLatestFor($patient, $receiver->_tag_patient);
      $patient->_id400 = $idexPatient->id400;
      
      $this->generateTypeEvenement("CHPrimXMLEnregistrementPatient", $patient, true, $initiateur);
    }
    // Si Client
    else {
      if (!$receiver->isMessageSupported("CHPrimXMLEnregistrementPatient")) {
        return false;
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
        return false;
      }

      $this->sendEvenementPatient("CHPrimXMLEnregistrementPatient", $patient);

      if ($receiver->_configs["send_insured_without_admit"]) {
        if (!$receiver->isMessageSupported("CHPrimXMLDebiteursVenue")) {
          return false;
        }

        $sejour = new CSejour();
        $where = array();
        $where["patient_id"] = "= '$patient->_id'";
        $where["group_id"]   = "= '$receiver->group_id'";

        $datetime = CMbDT::dateTime();
        $where["sortie"]    = ">= '$datetime'";

        /** @var CSejour[] $sejours */
        $sejours = $sejour->loadList($where);

        // On va transmettre les informations sur le débiteur pour le séjour en cours, et ceux à venir
        foreach ($sejours as $_sejour) {
          if (!$patient->code_regime) {
            continue;
          }

          $_sejour->_receiver = $receiver;
          $_sejour->loadLastLog();

          $_sejour->loadRefPatient();

          if (!$_sejour->_NDA) {
            // Génération du NDA dans le cas de la création, ce dernier n'était pas créé
            if ($msg = $_sejour->generateNDA()) {
              CAppUI::setMsg($msg, UI_MSG_ERROR);
            }

            $NDA = new CIdSante400();
            $NDA->loadLatestFor($_sejour, $receiver->_tag_sejour);
            $sejour->_NDA = $NDA->id400;
          }

          if ($receiver->isMessageSupported("CHPrimXMLDebiteursVenue")) {
            $this->sendEvenementPatient("CHPrimXMLDebiteursVenue", $_sejour);
          }
        }
      }

      $patient->_IPP = null;
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
        $sender = $mbObject->_eai_sender_guid ? CMbObject::loadFromGuid($mbObject->_eai_sender_guid) : null;
        if ($sender && $sender->group_id == $receiver->group_id) {
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