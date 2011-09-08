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
  static function storeID400CIP(CIdSante400 $id400Patient, CInteropSender $sender, $idSourceSejour, CSejour $newSejour) {
    //Paramétrage de l'id 400
    $id400Patient->object_class = "CSejour";
    $id400Patient->tag          = $sender->_tag_sejour;
    $id400Patient->id400        = $idSourceSejour;
    $id400Patient->object_id    = $newSejour->_id;
    $id400Patient->_id          = null;
    $id400Patient->last_update  = mbDateTime();

    return $id400Patient->store();
  }
  
  static function NDASMPSetting(CIdSante400 $NDA, $idVenueSMP = null) {
    $NDA->object_class = "CSejour";
    $NDA->tag          = CAppUI::conf("sip tag_nda");
    if ($idVenueSMP)
      $NDA->object_id  = $idVenueSMP;
  }
  
  static function NDASMPIncrement(CIdSante400 $NDA) {
    $NDA->id400++;
    $NDA->id400 = str_pad($NDA->id400, 6, '0', STR_PAD_LEFT);
    $NDA->_id   = null;
  }
  
  static function storeNDA(CIdSante400 $NDA, CSejour $newSejour) {
    if ($newSejour)
      $NDA->object_id   = $newSejour->_id;
    $NDA->last_update = mbDateTime();
    
    return $NDA->store();   
  }
  
  static function storeSejour(CSejour $newSejour, $NDA) {
    $newSejour->_NDA = $NDA;
    
    return $newSejour->store();
  }
}

?>