<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipObjectHandler extends CMbObjectHandler {
  static $handled = array ("CPatient", "CSejour");

  static function isHandled(CMbObject &$mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }

  function onAfterStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }

    if (!$mbObject->_ref_last_log) {
      return;
    }
    // Si pas de tag patient et séjour
    if (!CAppUI::conf("dPplanningOp CSejour tag_dossier") || !CAppUI::conf("dPpatients CPatient tag_ipp")) {
      CAppUI::setMsg("Aucun tag (patient/séjour) de défini pour la synchronisation.", UI_MSG_ERROR);
      return;
    }

    // Si serveur et pas d'IPP sur le patient
    if (isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1) && CAppUI::conf('sip server')) {
      return;
    }
    
    // Cas d'une fusion
    if ($mbObject->_merging) {
      return;
    }
    if ($mbObject->_forwardRefMerging) {
      return;
    }
    
    // Traitement Patient
    if ($mbObject instanceof CPatient) {
      if ($mbObject->_anonyme || $mbObject->_update_vitale) {
        return;
      }
      
      // Si Serveur
      if (CAppUI::conf('sip server')) {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        foreach ($destinataires as $_destinataire) {
          $mbObject->_id400 = null;
          $id400Patient = new CIdSante400();
          $id400Patient->loadLatestFor($mbObject, $_destinataire->_tag_patient);
          $mbObject->_id400 = $id400Patient->id400;

          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }
  
          $initiateur = ($_destinataire->nom == $echange_hprim->emetteur) ? $echange_hprim->_id : null;
          
          $domEvenementEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
          $domEvenementEnregistrementPatient->_ref_destinataire = $_destinataire;
          $this->generateEvenement($domEvenementEnregistrementPatient, $mbObject, true, $initiateur);
        }
      }
      // Si Client
      else {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        
        foreach ($destinataires as $_destinataire) {
          $_destinataire->loadConfigValues();
          if ($mbObject->_hprim_initiateur_group_id) {
            return;
          }

          if (!$mbObject->_IPP) {
            $IPP = new CIdSante400();
            $IPP->loadLatestFor($mbObject, $_destinataire->_tag_patient);
            
            $mbObject->_IPP = $IPP->id400;
          }

          // Envoi pas les patients qui n'ont pas d'IPP
          if (!$_destinataire->_configs["send_all_patients"] && !$mbObject->_IPP) {
            continue;
          }
          
          $domEvenementEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
          $domEvenementEnregistrementPatient->_ref_destinataire = $_destinataire;
          $this->sendEvenement($domEvenementEnregistrementPatient, $mbObject);
          
          $mbObject->_IPP = null;
        }
      }
    } 
    // Traitement Sejour
    elseif ($mbObject instanceof CSejour) {
      $mbObject->loadRefPraticien();
      $mbObject->loadRefPatient();
      $mbObject->_ref_patient->loadIPP();
      if ($mbObject->_ref_prescripteurs) {
        $mbObject->loadRefsPrescripteurs();
      }
      $mbObject->loadRefAdresseParPraticien();
      $mbObject->_ref_patient->loadRefsFwd();
      $mbObject->loadRefsActes();
      foreach ($mbObject->_ref_actes_ccam as $actes_ccam) {
        $actes_ccam->loadRefPraticien();
      }
      $mbObject->loadRefsAffectations();
      $mbObject->loadNumDossier();
      $mbObject->loadLogs();
      $mbObject->loadRefsConsultations();
      $mbObject->loadRefsConsultAnesth();

      // Si Serveur
      if (CAppUI::conf('sip server')) {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        foreach ($destinataires as $_destinataire) {
          $mbObject->_id400 = null;
          $id400Patient = new CIdSante400();
          $id400Patient->loadLatestFor($mbObject, $_destinataire->_tag_patient);
          $mbObject->_id400 = $id400Patient->id400;

          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }
  
          $initiateur = ($_destinataire->nom == $echange_hprim->emetteur) ? $echange_hprim->_id : null;
          
          $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
          $domEvenementVenuePatient->_ref_destinataire = $_destinataire;
          $this->generateEvenement($domEvenementVenuePatient, $mbObject, true, $initiateur);
        }        
      }
      // Si Client
      else {
        if ($mbObject->_hprim_initiateur_group_id) {
          return;
        }

        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        
        foreach ($destinataires as $_destinataire) {
          if (CGroups::loadCurrent()->_id != $_destinataire->group_id) {
            continue;
          }
          
          if (!$mbObject->_num_dossier) {
            $num_dossier = new CIdSante400();
            //Paramétrage de l'id 400
            $num_dossier->loadLatestFor($mbObject, $_destinataire->_tag_sejour);
    
            $mbObject->_num_dossier = $num_dossier->id400;
          }
          
          $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
          $domEvenementVenuePatient->_ref_destinataire = $_destinataire;
          $this->sendEvenement($domEvenementVenuePatient, $mbObject);
          
          if ($mbObject->_ref_patient->code_regime) {
            $domEvenementDebiteursVenue = new CHPrimXMLDebiteursVenue();
            $domEvenementDebiteursVenue->_ref_destinataire = $_destinataire;
            $this->sendEvenement($domEvenementDebiteursVenue, $mbObject);
          }
          
          $mbObject->_num_dossier = null;
        }
      }
    }
  }

  function onBeforeMerge(CMbObject &$mbObject) {
    if (!$mbObject->_merging) {
      return;
    }
    
    // Traitement Patient
    if ($mbObject instanceof CPatient) {
      $patient = $mbObject;

      $patient_elimine = new CPatient();
      $patient_elimine->load(reset($mbObject->_merging));

      // Si Client
      if (!CAppUI::conf('sip server')) {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        
        $mbObject->_fusion = array();
        foreach ($destinataires as $_destinataire) {
          if ($mbObject->_hprim_initiateur_group_id == $_destinataire->group_id) {
            continue;
          }
          
          $patient->_IPP = null;
          $patient->loadIPP($_destinataire->group_id);
          $patient1_ipp = $patient->_IPP;
          
          $patient_elimine->_IPP = null;
          $patient_elimine->loadIPP($_destinataire->group_id);
          $patient2_ipp = $patient_elimine->_IPP;
          
          // Passage en trash de l'IPP du patient a éliminer si fusion
          if ($patient1_ipp && $patient2_ipp) {
            $id400PatientElimine = new CIdSante400();
            $id400PatientElimine->loadLatestFor($patient_elimine);
            $id400PatientElimine->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$_destinataire->_tag_patient;
            $id400PatientElimine->store();
          }
                      
          $mbObject->_fusion[$_destinataire->_id] = array (
            "patientElimine" => $patient_elimine,
            "patient1_ipp" => $patient1_ipp,
            "patient2_ipp" => $patient2_ipp,
          );
        }        
      }
    }
  }
  
  function onAfterMerge(CMbObject &$mbObject) {
    if (!$mbObject->_merging) {
      return;
    }
    
    // Traitement Patient
    if ($mbObject instanceof CPatient) {
      $patient = $mbObject;
      $patient->check();
      $patient->updateFormFields();
      
      // Si Client
      if (!CAppUI::conf('sip server')) {
        foreach ($mbObject->_fusion as $destinataire_id => $infos_fus) {
          $dest_hprim = new CDestinataireHprim();
          $dest_hprim->load($destinataire_id);
          
          if ($mbObject->_hprim_initiateur_group_id == $dest_hprim->group_id) {
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
            $domEvenementEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
            $domEvenementEnregistrementPatient->_ref_destinataire = $dest_hprim;
            
            if ($patient2_ipp)
              $patient->_IPP = $patient2_ipp;
              
            $this->sendEvenement($domEvenementEnregistrementPatient, $patient);
            continue;
          }
          
          // Cas 2 IPPs : Message de fusion
          if ($patient1_ipp && $patient2_ipp) {
            $domEvenementFusionPatient = new CHPrimXMLFusionPatient();
            $domEvenementFusionPatient->_ref_destinataire = $dest_hprim;
                          
            $patient->_patient_elimine = $patient_eliminee;
            $this->sendEvenement($domEvenementFusionPatient, $patient);
            continue;
          }
        }        
      }
    }
  }
  
  function onAfterDelete(CMbObject &$mbObject) {}
  
  function sendEvenement($domEvenement, $mbObject, $referent = null, $initiateur = null) {
    $msgEvtVenuePatient = $domEvenement->generateTypeEvenement($mbObject, $referent, $initiateur);
    
    $dest_hprim = $domEvenement->_ref_destinataire;
    if ($dest_hprim->actif) {
      $source = CExchangeSource::get("$dest_hprim->_guid-evenementPatient");
      $source->setData($msgEvtVenuePatient);
      $source->send();
      $acquittement = $source->receive();
      
      if ($acquittement) {
        $echange_hprim = $domEvenement->_ref_echange_hprim;
        $echange_hprim->date_echange = mbDateTime();
        
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
        $domGetAcquittement->loadXML(utf8_decode($acquittement));
        $domGetAcquittement->_ref_echange_hprim = $echange_hprim;
        $doc_valid = $domGetAcquittement->schemaValidate();
        
        $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        $echange_hprim->_acquittement = $acquittement;
    
        $echange_hprim->store();
      }      
    }
  }
}
?>