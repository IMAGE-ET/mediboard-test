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
  
  static function IPPSIPIncrement(CIdSante400 $IPP) {
    $IPP->id400++;
    $IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
    $IPP->_id   = null;
  }
  
  static function storeIPP(CIdSante400 $IPP, CPatient $newPatient) {
    if ($newPatient)
      $IPP->object_id   = $newPatient->_id;
    $IPP->last_update = mbDateTime();
    
    return $IPP->store();   
  }
  
  static function storePatient(CPatient $newPatient, $IPP) {
    $newPatient->_IPP = $IPP;
    
    return $newPatient->store();
  }
  
  static function getComment(CMbObject $object, CMbObject $otherObject = null) {
    $modified_fields = self::getModifiedFields($object);
    
    if ($object instanceof CPatient) {
      switch ($object->_ref_last_log->type) {
         // Enregistrement du patient
        case "create" :
          $comment = "Le patient a été créé dans Mediboard avec l'IC $object->_id.";
          break;
         // Modification du patient
        case "store" :
          $comment = "Le patient avec l'IC '$object->_id' dans Mediboard a été modifié.";
          $comment .= ($modified_fields) ? "Les champs mis à jour sont les suivants : $modified_fields." : null;
          break;
        // Fusion du patient
        case "merge" : 
          $comment  = "Le patient avec l'IC '$mbPatient->_id' a été fusionné avec le patient dont l'IC est '$mbPatient->_mbPatientElimine_id'.";
      }     
      
      return $comment;
    }
    if ($object instanceof CIdSante400) {
      return "L'IPP créé est : $object->id400";
    }
  }
}

?>