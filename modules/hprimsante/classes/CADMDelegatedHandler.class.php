<?php

/**
 * $Id$
 *  
 * @category hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * ADM handler
 */
class CADMDelegatedHandler {

  /** @var array */
  static $handled = array ("CPatient");

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
    /** @var CReceiverHprimSante $receiver */
    $receiver = $mbObject->_receiver;

    switch(get_class($mbObject)) {
      case "CPatient":
        $event = new CHPrimSanteADM();
        $event->msg_codes = array (
          array(
            "ADM", $receiver->_configs["ADM_sous_type"]
          )
        );
        return $receiver->sendEvent($event, $mbObject);
        break;
    }
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
      return;
    }

    if (!$mbObject instanceof CPatient) {
      return;
    }
    /** @var CPatient $patient */
    $patient = $mbObject;
    $patient->check();
    $patient->updateFormFields();
    /** @var CInteropReceiver $receiver */
    $receiver = $mbObject->_receiver;

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

      // Cas 1 IPP : Pas de message de fusion mais d'une modification du patient (Un patient avec un IPP et l'autre non)
      if ($patient1_ipp xor $patient2_ipp) {
        if ($patient2_ipp) {
          $patient->_IPP = $patient2_ipp;
        }

        $event = new CHPrimSanteADM();
        $event->msg_codes = array (
          array(
            "ADM", $receiver->_configs["ADM_sous_type"]
          )
        );
        $receiver->sendEvent($event, $patient);
        continue;
      }

      // Cas 2 IPPs : Message de fusion
      if ($patient1_ipp && $patient2_ipp) {
        $patient->_patient_elimine = $patient_eliminee;


        $event = new CHPrimSanteADM();
        $event->msg_codes = array (
          array(
            "ADM", $receiver->_configs["ADM_sous_type"]
          )
        );
        $receiver->sendEvent($event, $patient);
        continue;
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
  }
}
