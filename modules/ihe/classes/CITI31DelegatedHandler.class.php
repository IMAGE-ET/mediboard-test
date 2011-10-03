<?php

/**
 * ITI31 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI31DelegatedHandler 
 * ITI31 Delegated Handler
 */
class CITI31DelegatedHandler extends CIHEDelegatedHandler {
  static $handled        = array ("CSejour", "CAffectation");
  protected $profil      = "PAM";
  protected $transaction = "ITI31";
  
  static $inpatient      = array("comp", "ssr", "psy", "seances", "consult");
  static $outpatient     = array("urg", "ambu", "exte");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;  
    
    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      
      // Si Serveur
      if (CAppUI::conf('smp server')) {} 
      // Si Client
      else {
        $code = $this->getCode($sejour);
        
        // Envoi de l'événement
        $this->sendITI($this->profil, $this->transaction, $code, $sejour);
      }
    }
  }
  
  function getCode(CSejour $sejour) {
    $last_log = $sejour->loadLastLog();
    if (!in_array($last_log->type, array("create", "store"))) {
      return null;
    }
    
    // Cas d'une pré-admission
    if ($sejour->_etat == "preadmission") {
      // Création d'une pré-admission
      if ($last_log->type == "create") {
        return "A05";
      } 
      // Modification d'une pré-admission
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A38";
      }
      // Simple modification ? 
      return "Z99";
    }
    
    // Cas d'un séjour en cours (entrée réelle)
    if ($sejour->_etat == "encours") {
      // Admission faite
      if ($sejour->fieldModified("entree_reelle")) {
        // Patient externe
        if (in_array($sejour->type, self::$outpatient)) {
          return "A04";
        } 
        // Admission hospitalisé
        return "A01";
      }
      
      // Modification de l'admission 
      // Externe devient hospitalisé
      if ($sejour->fieldModified("type") && 
          in_array($sejour->type, self::$outpatient) && 
          in_array($sejour->_old->type, self::$inpatient)) {
        return "A06";
      } 

      // Externe devient hospitalisé
      if ($sejour->fieldModified("type") && 
          in_array($sejour->type, self::$inpatient) && 
          in_array($sejour->_old->type, self::$outpatient)) {
        return "A07";
      }
      
      // Cas d'une mutation ? 
      if ($sejour->fieldModified("service_entree_id")) {
        return "A02";
      }
      // Annulation de la mutation ? 
      if ($sejour->fieldModified("service_entree_id", "")) {
        return "A12";
      }
      
      // Changement du médecin responsable
      if ($sejour->fieldModified("praticien_id")) {
        return "A54";
      }
      // Annulation de la mutation ? 
      if ($sejour->fieldModified("praticien_id", "")) {
        return "A55";
      }
      
      // Réattribution dossier administratif
      if ($sejour->fieldModified("patient_id")) {
        return "A44";
      }
      
      /* @todo Changement d'UF Médicale */
      
      /* @todo Changement d'UF de Soins */
      
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A11";
      }
      
      // Simple modification ? 
      return "Z99";
    }
    
    // Cas d'un séjour clôturé (sortie réelle)
    if ($sejour->_etat == "cloture") {
      // Sortie réelle renseignée
      if ($sejour->fieldModified("sortie_reelle")) {
        return "A03";
      }
      // Modification de l'admission 
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A13";
      }
      // Simple modification ? 
      return "Z99";
    }
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

    
  }  
}
?>