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
  static function storeID400CIP(CIdSante400 $id400Patient, CInteropSender $sender, $idSourcePatient, CPatient $newPatient) {
    //Paramétrage de l'id 400
    $id400Patient->object_class = "CPatient";
    $id400Patient->tag          = $sender->_tag_patient;
    $id400Patient->id400        = $idSourcePatient;
    $id400Patient->object_id    = $newPatient->_id;
    $id400Patient->_id          = null;
    $id400Patient->last_update  = mbDateTime();

    return $id400Patient->store();
  }
  
  static function IPPSIPSetting(CIdSante400 $IPP, $idPatientSIP = null) {
    $IPP->object_class = "CPatient";
    $IPP->tag          = CAppUI::conf("sip tag_ipp");
    if ($idPatientSIP)
      $IPP->object_id  = $idPatientSIP;
  }
  
  static function incrementIPPSIP(CPatient $patient,  CInteropSender $sender) {
    
  }
  
  static function storeIPP(CIdSante400 $IPP, CPatient $patient, CInteropSender $sender) {
    /* Gestion du numéroteur */
    $group = new CGroups();
    $group->load($sender->group_id);
    $group->loadConfigValues();
      
    // Génération de l'IPP ? 
    // Non
    if (!$group->_configs["sip_idex_generator"]) {
      if (!$IPP->id400) {
        return null;
      }
      
      if ($patient)
        $IPP->object_id   = $patient->_id;
      $IPP->last_update = mbDateTime();
      
      return $IPP->store();  
    }
    else {
      $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, null, $patient->_id);
      if ($IPP->_id) {
        return;
      }
      
      // Pas d'IPP passé
      if (!$IPP->id400) {
        if (!CIncrementer::generateIdex($patient, $sender->_tag_patient, $sender->group_id)) {
          return CAppUI::tr("CEAIPatient-error-generate-idex");
        }
        
        return null;
      }
      else {
        /* @todo Gestion des plages d'identifiants */
        return CAppUI::tr("CEAIPatient-idex-not-in-the-range");
      }
    }  
  }
  
  static function storePatient(CPatient $newPatient, $IPP) {
    $newPatient->_IPP = $IPP;
    
    return $newPatient->store();
  }
}

?>