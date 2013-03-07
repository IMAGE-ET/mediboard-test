<?php

/**
 * Sejour utilities EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAISejour
 * Patient utilities EAI
 */

class CEAISejour extends CEAIMbObject {
  /**
   * Recording the external identifier of the CIP
   * 
   * @param CIdSante400    $id400Sejour    Object id400
   * @param CInteropSender $sender         Sender
   * @param int            $idSourceSejour External identifier
   * @param CSejour        $newSejour      Admit
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeID400CIP(CIdSante400 $id400Sejour, CInteropSender $sender, $idSourceSejour, CSejour $newSejour) {
    //Paramétrage de l'id 400
    $id400Sejour->object_class = "CSejour";
    $id400Sejour->tag          = $sender->_tag_sejour;
    $id400Sejour->id400        = $idSourceSejour;
    $id400Sejour->object_id    = $newSejour->_id;
    $id400Sejour->_id          = null;
    $id400Sejour->last_update  = CMbDT::dateTime();

    return $id400Sejour->store();
  }
  
  /**
   * Recording the external identifier of the CIP
   * 
   * @param CIdSante400 $NDA        Object id400
   * @param int         $idVenueSMP External identifier
   * 
   * @return void
   */ 
  static function NDASMPSetting(CIdSante400 $NDA, $idVenueSMP = null) {
    $NDA->object_class = "CSejour";
    $NDA->tag          = CAppUI::conf("smp tag_dossier");
    if ($idVenueSMP) {
      $NDA->object_id  = $idVenueSMP;
    }
  }
  
  /**
   * Increment NDA
   * 
   * @param CIdSante400 $NDA Object id400
   * 
   * @return void
   */ 
  static function NDASMPIncrement(CIdSante400 $NDA) {
    $NDA->id400++;
    $NDA->id400 = str_pad($NDA->id400, 6, '0', STR_PAD_LEFT);
    $NDA->_id   = null;
  }
  
  /**
   * Recording NDA
   * 
   * @param CIdSante400    $NDA    Object id400
   * @param CSejour        $sejour Admit
   * @param CInteropSender $sender Sender
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeNDA(CIdSante400 $NDA, CSejour $sejour, CInteropSender $sender) {
    /* Gestion du numéroteur */
    $group = new CGroups();
    $group->load($sender->group_id);
    $group->loadConfigValues();
    
    // Purge du NDA existant sur le séjour et on le remplace par le nouveau
    if ($sender->_configs["purge_idex_movements"]) {
      // On charge le NDA courant du séjour
      $sejour->loadNDA($sender->group_id);
      
      $ref_NDA = $sejour->_ref_NDA;
      
      // Si le NDA actuel est identique à celui qu'on reçoit on ne fait rien
      if ($ref_NDA->id400 == $NDA->id400) {
        return;
      }
      
      // On passe le NDA courant en trash
      $ref_NDA->tag = CAppUI::conf("dPplanningOp CSejour tag_dossier_trash").$ref_NDA->tag;
      $ref_NDA->store();
      
      // On sauvegarde le nouveau
      $NDA->tag          = $sender->_tag_sejour;
      $NDA->object_class = "CSejour";
      $NDA->object_id    = $sejour->_id;
      $NDA->last_update  = CMbDT::dateTime();
      
      return $NDA->store();  
    }
      
    // Génération du NDA ? 
    // Non
    if (!$group->_configs["smp_idex_generator"]) {
      if (!$NDA->id400) {
        return null;
      }
      
      if ($sejour) {
        $NDA->object_id   = $sejour->_id;
      }
        
      $NDA->last_update = CMbDT::dateTime();
      
      return $NDA->store();  
    }
    else {
      $NDA_temp = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, null, $sejour->_id);
      if ($NDA_temp->_id) {
        return;
      }
    
      // Pas de NDA passé
      if (!$NDA->id400) {
        if (!CIncrementer::generateIdex($sejour, $sender->_tag_sejour, $sender->group_id)) {
          return CAppUI::tr("CEAISejour-error-generate-idex");
        }
        
        return null;
      }
      else {
        /* @todo Gestion des plages d'identifiants */
        if (($NDA->id400 < $group->_configs["nda_range_min"]) || ($NDA->id400 > $group->_configs["nda_range_max"])) {
           return CAppUI::tr("CEAISejour-idex-not-in-the-range");
        }
        
        $NDA->object_id   = $sejour->_id;
        $NDA->last_update = CMbDT::dateTime();
      
        return $NDA->store();  
      }
    }  
  }

  /**
   * Recording admit
   * 
   * @param CSejour     $newSejour Admit
   * @param CIdSante400 $NDA       Object id400
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeSejour(CSejour $newSejour, $NDA) {
    $newSejour->_NDA = $NDA;
    
    return $newSejour->store();
  }
  
}

?>