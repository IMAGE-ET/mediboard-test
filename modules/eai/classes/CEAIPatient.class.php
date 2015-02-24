<?php

/**
 * Patient utilities EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIPatient
 * Patient utilities EAI
 */

class CEAIPatient extends CEAIMbObject {
  /**
   * Recording the external identifier of the CIP
   * 
   * @param CIdSante400    $idex            Object id400
   * @param CInteropSender $sender          Sender
   * @param int            $idSourcePatient External identifier
   * @param CPatient       $newPatient      Patient
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeID400CIP(CIdSante400 $idex, CInteropSender $sender, $idSourcePatient, CPatient $newPatient) {
    //Paramétrage de l'id 400
    $idex->object_class = "CPatient";
    $idex->tag          = $sender->_tag_patient;
    $idex->id400        = $idSourcePatient;
    $idex->object_id    = $newPatient->_id;
    $idex->_id          = null;
    $idex->last_update  = CMbDT::dateTime();

    return $idex->store();
  }
  
  /**
   * Recording the external identifier of the CIP
   * 
   * @param CIdSante400 $IPP          Object id400
   * @param int         $idPatientSIP External identifier
   * 
   * @return void
   */ 
  static function IPPSIPSetting(CIdSante400 $IPP, $idPatientSIP = null) {
    $IPP->object_class = "CPatient";
    $IPP->tag          = CAppUI::conf("sip tag_ipp");
    if ($idPatientSIP) {
      $IPP->object_id  = $idPatientSIP;
    }
  }
  
  /**
   * Recording IPP
   * 
   * @param CIdSante400    $IPP     Object id400
   * @param CPatient       $patient Patient
   * @param CInteropSender $sender  Sender
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeIPP(CIdSante400 $IPP, CPatient $patient, CInteropSender $sender) {
    /* Gestion du numéroteur */
    $group = new CGroups();
    $group->load($sender->group_id);
    $group->loadConfigValues();
    
    // Purge de l'IPP existant sur le patient et on le remplace par le nouveau
    if ($sender->_configs && $sender->_configs["purge_idex_movements"]) {
      // On charge l'IPP courant du patient
      $patient->loadIPP($sender->group_id);

      $ref_IPP = $patient->_ref_IPP;

      if ($ref_IPP) {
        // Si l'IPP actuel est identique à celui qu'on reçoit on ne fait rien
        if ($ref_IPP->id400 == $IPP->id400) {
          return;
        }

        // On passe l'IPP courant en trash
        $ref_IPP->tag = CAppUI::conf("dPpatients CPatient tag_ipp_trash").$ref_IPP->tag;
        $ref_IPP->_eai_sender_guid = $sender->_guid;
        $ref_IPP->store();

        $patient->trashIPP($ref_IPP);
      }

      // On sauvegarde le nouveau
      $IPP->tag          = $sender->_tag_patient;
      $IPP->object_class = "CPatient";
      $IPP->object_id    = $patient->_id;
      $IPP->last_update  = CMbDT::dateTime();
      $IPP->_eai_sender_guid = $sender->_guid;

      return $IPP->store();  
    }

    // Génération de l'IPP ?
    /* @todo sip_idex_generator doit être remplacé par isIPPSupplier */
    if ($sender->_configs && !$group->_configs["sip_idex_generator"]) {
      if (!$IPP->id400) {
        return null;
      }
      
      if ($patient) {
        $IPP->object_id   = $patient->_id;
      }
        
      $IPP->last_update = CMbDT::dateTime();
      $IPP->_eai_sender_guid = $sender->_guid;

      return $IPP->store();  
    }
    else {
      $IPP_temp = CIdSante400::getMatch("CPatient", $sender->_tag_patient, null, $patient->_id);

      // Pas d'IPP passé
      if (!$IPP->id400) {
        if ($IPP_temp->_id) {
          return null; 
        }
        
        if (!CIncrementer::generateIdex($patient, $sender->_tag_patient, $sender->group_id)) {
          return CAppUI::tr("CEAIPatient-error-generate-idex");
        }
        
        return null;
      }
      else {
        // Si j'ai déjà un identifiant
        if ($IPP_temp->_id) {
          // On passe l'IPP courant en trash
          $IPP_temp->tag = CAppUI::conf("dPpatients CPatient tag_ipp_trash").$IPP_temp->tag;
          $IPP_temp->_eai_sender_guid = $sender->_guid;
          $IPP_temp->store();
        }

        $incrementer = $sender->loadRefGroup()->loadDomainSupplier("CPatient");
        if ($incrementer && ($IPP->id400 < $incrementer->range_min) || ($IPP->id400 > $incrementer->range_max)) {
           return CAppUI::tr("CEAIPatient-idex-not-in-the-range");
        }
        
        $IPP->object_id   = $patient->_id;
        $IPP->last_update = CMbDT::dateTime();
        $IPP->_eai_sender_guid = $sender->_guid;

        return $IPP->store();  
      }
    }
  }

  /**
   * Recording RI sender
   *
   * @param string         $RI_sender Idex value
   * @param CPatient       $patient   Patient
   * @param CInteropSender $sender    Sender
   *
   * @return null|string null if successful otherwise returns and error message
   */
  static function storeRISender($RI_sender, CPatient $patient, CInteropSender $sender) {
    $domain = $sender->loadRefDomain();

    $idex = new CIdSante400();
    $idex->object_class = "CPatient";
    $idex->object_id    = $patient->_id;
    $idex->tag          = $domain->tag;
    $idex->id400        = $RI_sender;
    return $idex->store();
  }

  /**
   * Recording patient
   * 
   * @param CPatient       $newPatient  Patient
   * @param CInteropSender $sender      Sender
   * @param bool           $generateIPP Generate IPP ?
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storePatient(CPatient $newPatient, CInteropSender $sender, $generateIPP = false) {   
    // Notifier les autres destinataires autre que le sender
    $newPatient->_eai_sender_guid = $sender->_guid;
    $newPatient->_generate_IPP    = $generateIPP;
    if ($msg = $newPatient->store()) {
      if ($sender->_configs && $sender->_configs["repair_patient"]) {
        $newPatient->repair();
      }

      // Notifier les autres destinataires autre que le sender
      $newPatient->_eai_sender_guid = $sender->_guid;
      $newPatient->_generate_IPP    = $generateIPP;
      
      return $newPatient->store();
    }
  }
  
  /**
   * Recording patient
   * 
   * @param CPatient    $newPatient Patient
   * @param CIdSante400 $IPP        Object id400
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storePatientSIP(CPatient $newPatient, $IPP) {
    $newPatient->_IPP = $IPP;
    
    return $newPatient->store();
  }
}