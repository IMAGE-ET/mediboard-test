<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipObjectHandler extends CMbObjectHandler {
  static $handled = array ("CPatient", "CSejour", "CAffectation");

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
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas d'IPP sur le patient ou de numéro de dossier sur le séjour
    if ((isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1)) ||
        (isset($mbObject->_no_num_dos) && ($mbObject->_no_num_dos == 1)) &&
        CAppUI::conf('sip server')) {
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
          $_destinataire->loadConfigValues();
          
          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }

          $initiateur = ($_destinataire->_id == $echange_hprim->emetteur_id) ? $echange_hprim->_id : null;
          
          if (!$initiateur && !CAppUI::conf('sip notify_all_destinataires')) {
            continue;
          }
          
          $mbObject->_id400 = null;
          $id400Patient = new CIdSante400();
          $id400Patient->loadLatestFor($mbObject, $_destinataire->_tag_patient);
          $mbObject->_id400 = $id400Patient->id400;
 
          $domEvenementEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
          $domEvenementEnregistrementPatient->_ref_destinataire = $_destinataire;
          $domEvenementEnregistrementPatient->generateTypeEvenement($mbObject, true, $initiateur);
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
          $_destinataire->sendEvenementPatient($domEvenementEnregistrementPatient, $mbObject);
          
          $mbObject->_IPP = null;
        }
      }
    } 
    // Traitement Sejour
    elseif ($mbObject instanceof CSejour) {
      $mbObject->loadRefPraticien();
      $mbObject->loadNumDossier();
      $mbObject->loadLastLog();
      
      $mbObject->loadRefPatient();
      $mbObject->_ref_patient->loadIPP();
      $mbObject->loadRefAdresseParPraticien();

      // Si Serveur
      if (CAppUI::conf('sip server')) {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        foreach ($destinataires as $_destinataire) {
          $_destinataire->loadConfigValues();
          
          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }

          $initiateur = ($_destinataire->_id == $echange_hprim->emetteur_id) ? $echange_hprim->_id : null;
          
          if (!$initiateur && !CAppUI::conf('sip notify_all_destinataires')) {
            continue;
          }
          
          $mbObject->_id400 = null;
          $id400Patient = new CIdSante400();
          $id400Patient->loadLatestFor($mbObject, $_destinataire->_tag_sejour);
          $mbObject->_id400 = $id400Patient->id400;

          $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
          $domEvenementVenuePatient->_ref_destinataire = $_destinataire;
          $domEvenementVenuePatient->generateTypeEvenement($mbObject, true, $initiateur);
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
          $_destinataire->loadConfigValues();
          
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
          $_destinataire->sendEvenementPatient($domEvenementVenuePatient, $mbObject);
          
          if ($_destinataire->_configs["send_debiteurs_venue"] && $mbObject->_ref_patient->code_regime) {
            $domEvenementDebiteursVenue = new CHPrimXMLDebiteursVenue();
            $domEvenementDebiteursVenue->_ref_destinataire = $_destinataire;
            $_destinataire->sendEvenementPatient($domEvenementDebiteursVenue, $mbObject);
          }
          
          if ($_destinataire->_configs["send_mvt_patients"] && $_destinataire->_configs["send_default_serv_with_type_sej"] 
                && ($mbObject->_ref_last_log->type == "create")) {
            $service = new CService();
            $service->load(CAppUI::conf("dPhospi default_service_types_sejour $mbObject->type"));
            if (!$service->_id) {
              // envoi par défaut le premier de la liste si pas défini
              $service->loadObject();  
            }
                        
            $affectation = new CAffectation();
            $affectation->entree = $mbObject->entree;
            $affectation->sortie = $mbObject->sortie;
            $affectation->loadRefLit();
            $affectation->_ref_lit->loadRefChambre();
            $affectation->_ref_lit->_ref_chambre->_ref_service = $service;
            $affectation->sejour_id = $mbObject->_id;
            $affectation->loadRefSejour();
            $affectation->_ref_sejour->loadNumDossier();
            $affectation->_ref_sejour->loadRefPatient();
            $affectation->_ref_sejour->loadRefPraticien();
            
            $domEvenementMouvementPatient = new CHPrimXMLMouvementPatient();
            $domEvenementMouvementPatient->_ref_destinataire = $_destinataire;
            $_destinataire->sendEvenementPatient($domEvenementMouvementPatient, $affectation);
          }
          
          $mbObject->_num_dossier = null;
        }
      }
    }
    // Traitement Affectation
    elseif ($mbObject instanceof CAffectation) {
      $mbObject->_ref_lit->loadRefChambre();
      $mbObject->_ref_lit->_ref_chambre->loadRefService();
      $mbObject->loadLastLog();
      $mbObject->loadRefSejour();
      $mbObject->_ref_sejour->loadNumDossier();
      $mbObject->_ref_sejour->loadRefPatient();
      $mbObject->_ref_sejour->loadRefPraticien();
      
      // Si Serveur
      if (CAppUI::conf('sip server')) { }
      // Si Client
      else {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
        $dest_hprim->message = "patients";
        $destinataires = $dest_hprim->loadMatchingList();
        
        foreach ($destinataires as $_destinataire) {
          $_destinataire->loadConfigValues();
          
          if (!$_destinataire->_configs["send_mvt_patients"]) {
            continue;
          }
          
          if (!$mbObject->_ref_sejour->_num_dossier) {
            $num_dossier = new CIdSante400();
            //Paramétrage de l'id 400
            $num_dossier->loadLatestFor($mbObject->_ref_sejour, $_destinataire->_tag_sejour);
    
            $mbObject->_ref_sejour->_num_dossier = $num_dossier->id400;
          }
          
          $domEvenementMouvementPatient = new CHPrimXMLMouvementPatient();
          $domEvenementMouvementPatient->_ref_destinataire = $_destinataire;
          $_destinataire->sendEvenementPatient($domEvenementMouvementPatient, $mbObject);
          
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
        $mbObject->_fusion = array();
        foreach (CGroups::loadGroups() as $_group) {
          if ($mbObject->_hprim_initiateur_group_id == $_group->_id) {
            continue;
          }
          
          $patient->_IPP = null;
          $patient->loadIPP($_group->_id);
          $patient1_ipp = $patient->_IPP;
          
          $patient_elimine->_IPP = null;
          $patient_elimine->loadIPP($_group->_id);
          $patient2_ipp = $patient_elimine->_IPP;
          
          // Passage en trash des IPP des patients
          $tap_IPP = CPatient::getTagIPP($_group->_id);
          
          $id400Patient               = new CIdSante400();
          $id400Patient->tag          = $tap_IPP;
          $id400Patient->object_class = "CPatient";
          $id400Patient->object_id    = $patient->_id;
          $id400sPatient = $id400Patient->loadMatchingList();
          
          $id400PatientElimine               = new CIdSante400();
          $id400PatientElimine->tag          = $tap_IPP;
          $id400PatientElimine->object_class = "CPatient";
          $id400PatientElimine->object_id    = $patient_elimine->_id;
          $id400sPatientElimine = $id400PatientElimine->loadMatchingList();
          
          $id400s = array_merge($id400sPatient, $id400sPatientElimine);
          if (count($id400s) > 1) {
            foreach ($id400s as $_id_400) {
              // On continue pour ne pas mettre en trash l'IPP du patient que l'on garde
              if ($_id_400->id400 == $patient1_ipp) {
                continue;
              }
              $_id_400->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$tap_IPP;
              $_id_400->last_update = mbDateTime();
              $_id_400->store();
            }
          }
                      
          $mbObject->_fusion[$_group->_id] = array (
            "patientElimine" => $patient_elimine,
            "patient1_ipp"   => $patient1_ipp,
            "patient2_ipp"   => $patient2_ipp,
          );
        }        
      }
    }
    // Traitement Séjour
    else if ($mbObject instanceof CSejour) { 
      $sejour = $mbObject;

      $sejour_eliminee = new CSejour();
      $sejour_eliminee->load(reset($mbObject->_merging));
      $sejour_eliminee->updateFormFields();
      $sejour_eliminee->updateFormFields();
      $sejour_eliminee->loadRefPatient();
      $sejour_eliminee->loadRefPraticien();
      $sejour_eliminee->loadLastLog();
      $sejour_eliminee->loadRefAdresseParPraticien();
      
      // Si Client
      if (!CAppUI::conf('sip server')) {
        $mbObject->_fusion = array();
        foreach (CGroups::loadGroups() as $_group) {
          if ($mbObject->_hprim_initiateur_group_id == $_group->_id) {
            continue;
          }
          
          $sejour->_num_dossier = null;
          $sejour->loadNumDossier($_group->_id);
          $sejour1_nda = $sejour->_num_dossier;
          
          $sejour_eliminee->_num_dossier = null;
          $sejour_eliminee->loadNumDossier($_group->_id);
          $sejour2_nda = $sejour_eliminee->_num_dossier;
          
          // Passage en trash des NDA des séjours
          $tap_NDA = CPatient::getTagIPP($_group->_id);
          
          $id400Sejour               = new CIdSante400();
          $id400Sejour->tag          = $tap_NDA;
          $id400Sejour->object_class = "CSejour";
          $id400Sejour->object_id    = $sejour->_id;
          $id400sSejour = $id400Sejour->loadMatchingList();
          
          $id400SejourElimine               = new CIdSante400();
          $id400SejourElimine->tag          = $tap_NDA;
          $id400SejourElimine->object_class = "CSejour";
          $id400SejourElimine->object_id    = $sejour_eliminee->_id;
          $id400sSejourElimine = $id400SejourElimine->loadMatchingList();
          
          $id400s = array_merge($id400sSejour, $id400sSejourElimine);
          if (count($id400s) > 1) {
            foreach ($id400s as $_id_400) {
              // On continue pour ne pas mettre en trash le NDA du séjour que l'on garde
              if ($_id_400->id400 == $sejour1_nda) {
                continue;
              }
              $_id_400->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_trash').$tap_NDA;
              $_id_400->last_update = mbDateTime();
              $_id_400->store();
            }
          }
                      
          $mbObject->_fusion[$_group->_id] = array (
            "sejourEliminee" => $sejour_eliminee,
            "sejour1_nda"    => $sejour1_nda,
            "sejour2_nda"    => $sejour2_nda,
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
        foreach ($mbObject->_fusion as $group_id => $infos_fus) {
          $dest_hprim = new CDestinataireHprim();
          $dest_hprim->group_id = $group_id;
          $dest_hprim->type     = "sip";
          $dest_hprim->message  = "patients";
          $destinataires = $dest_hprim->loadMatchingList();
          
          foreach ($destinataires as $_dest_hprim) {
            if ($mbObject->_hprim_initiateur_group_id == $_dest_hprim->group_id) {
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
              $domEvenementEnregistrementPatient->_ref_destinataire = $_dest_hprim;
              
              if ($patient2_ipp)
                $patient->_IPP = $patient2_ipp;
                
              $_dest_hprim->sendEvenementPatient($domEvenementEnregistrementPatient, $patient);
              continue;
            }
            
            // Cas 2 IPPs : Message de fusion
            if ($patient1_ipp && $patient2_ipp) {
              $domEvenementFusionPatient = new CHPrimXMLFusionPatient();
              $domEvenementFusionPatient->_ref_destinataire = $_dest_hprim;
                            
              $patient->_patient_elimine = $patient_eliminee;
              $_dest_hprim->sendEvenementPatient($domEvenementFusionPatient, $patient);
              continue;
            }
          }
        }        
      }
    }
    // Traitement Séjour
    else if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      $sejour->check();
      $sejour->updateFormFields();
      $sejour->loadRefPatient();
      $sejour->loadRefPraticien();
      $sejour->loadLastLog();
      $sejour->loadRefAdresseParPraticien();
      
      // Si Client
      if (!CAppUI::conf('sip server')) {
        foreach ($mbObject->_fusion as $group_id => $infos_fus) {
          $dest_hprim = new CDestinataireHprim();
          $dest_hprim->group_id = $group_id;
          $dest_hprim->type     = "sip";
          $dest_hprim->message  = "patients";
          $destinataires = $dest_hprim->loadMatchingList();
          
          foreach ($destinataires as $_dest_hprim) {
            if ($mbObject->_hprim_initiateur_group_id == $_dest_hprim->group_id) {
              continue;
            }
            
            $sejour1_nda = $sejour->_num_dossier = $infos_fus["sejour1_nda"];
            
            $sejour_eliminee = $infos_fus["sejourEliminee"];
            $sejour2_nda = $sejour_eliminee->_num_dossier = $infos_fus["sejour2_nda"];
  
            // Cas 0 NDA : Aucune notification envoyée
            if (!$sejour1_nda && !$sejour2_nda) {
              continue;
            }
           
            // Cas 1 NDA : Pas de message de fusion mais d'une modification de la venue
            if ((!$sejour1_nda && $sejour2_nda) || ($sejour1_nda && !$sejour2_nda)) {
              $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
              $domEvenementVenuePatient->_ref_destinataire = $_dest_hprim;
              
              if ($sejour2_nda)
                $sejour->_num_dossier = $sejour2_nda;
                
              $_dest_hprim->sendEvenementPatient($domEvenementVenuePatient, $sejour);
              continue;
            }
            
            // Cas 2 NDA : Message de fusion
            if ($sejour1_nda && $sejour2_nda) {
              $domEvenementFusionVenue = new CHPrimXMLFusionVenue();
              $domEvenementFusionVenue->_ref_destinataire = $_dest_hprim;
                            
              $sejour->_sejour_eliminee = $sejour_eliminee;
              $_dest_hprim->sendEvenementPatient($domEvenementFusionVenue, $sejour);
              continue;
            }
          }
        }        
      }
    }
  }
  
  function onAfterDelete(CMbObject &$mbObject) {}
}
?>