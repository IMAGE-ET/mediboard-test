<?php

/**
 * MbObject utilities EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIMbObject
 * MbObject utilities EAI
 */

class CEAIMbObject {
  /**
   * Get modified fields
   * 
   * @param CMbObject $object First object
   * 
   * @return string Modified fields
   */  
  static function getModifiedFields(CMbObject $object) {
    $modified_fields = "";

    if ($object->_ref_current_log && is_array($object->_ref_current_log->_fields)) {
      $fields = $object->_ref_current_log->_fields;

      $modified_fields = implode(", ", $fields);
    }

    return $modified_fields;
  }
  
  /**
   * Comment log
   * 
   * @param CMbObject $object      First object
   * @param CMbObject $otherObject Other object (merge)
   * 
   * @return string Comment
   */  
  static function getComment(CMbObject $object, CMbObject $otherObject = null) {
    $modified_fields = self::getModifiedFields($object);
    
    if (!$object->_ref_current_log) {
      return "";
    }

    if ($object instanceof CPatient) {
      switch ($object->_ref_current_log->type) {
         // Enregistrement du patient
        case "create":
          $comment = "Le patient a été créé dans Mediboard avec l'IC $object->_id.";
          break;
         // Modification du patient
        case "store":
          $comment = "Le patient avec l'IC '$object->_id' dans Mediboard a été modifié.";
          $comment .= ($modified_fields) ? "Les champs mis à jour sont les suivants : $modified_fields." : null;
          break;
        // Fusion des patients
        case "merge":
          $comment  = "Le patient avec l'IC '$object->_id' a été fusionné avec le patient dont l'IC est '$otherObject->_id'.";
          break;
        default:
          $comment = "";
      }     
      
      return $comment;
    }
    
    if ($object instanceof CSejour) {
      switch ($object->_ref_current_log->type) {
         // Enregistrement du séjour
        case "create":
          $comment = "Le séjour a été créé dans Mediboard avec l'IC $object->_id.";
          break;
         // Modification du séjour
        case "store":
          $comment = "Le séjour avec l'IC '$object->_id' dans Mediboard a été modifié.";
          $comment .= ($modified_fields) ? "Les champs mis à jour sont les suivants : $modified_fields." : null;
          break;
        // Fusion des séjours
        case "merge":
          $comment  = "Le séjour avec l'IC '$object->_id' a été fusionné avec 
                       le séjour dont l'IC est '$otherObject->_id'.";
          break;
        default:
          $comment = "";
      }     
      
      return $comment;
    }
    
    if ($object instanceof CIdSante400) {
      if (!$object->_id) {
        return "";
      }
      
      return "L'IPP/NDA créé est : $object->id400";
    }
  }
  
  /**
   * Recording idex
   * 
   * @param CIdSante400    $idex   Object id400
   * @param CMbObject      $object Object
   * @param CInteropSender $sender Sender
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeIdex(CIdSante400 $idex, CMbObject $object, CInteropSender $sender) {
    $idex->object_id   = $object->_id; 
    $idex->last_update = CMbDT::dateTime();

    return $idex->store();
  }
}