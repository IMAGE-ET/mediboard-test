<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSmpObjectHandler extends CMbObjectHandler {
  static $handled = array ("CSejour", "CAffectation");

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
    
    // Si pas de tag séjour
    if (!CAppUI::conf("dPplanningOp CSejour tag_dossier")) {
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas de NDA sur le séjour
    if ((isset($mbObject->_no_num_dos) && ($mbObject->_no_num_dos == 1)) &&
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
    
    
    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $mbObject->loadRefPraticien();
      $mbObject->loadNumDossier();
      $mbObject->loadLastLog();
      
      $mbObject->loadRefPatient();
      $mbObject->loadRefAdresseParPraticien();

      // Si Serveur
      if (CAppUI::conf('sip server')) {
        $receiver = new CDestinataireHprim();
        $receiver->message = "patients";
        $receivers = $receiver->loadMatchingList();
        foreach ($receivers as $_receiver) {
          $_receiver->loadConfigValues();
          
          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }

          $initiateur = ($_receiver->_id == $echange_hprim->sender_id) ? $echange_hprim->_id : null;
          
          if (!$initiateur && !CAppUI::conf('sip notify_all_destinataires')) {
            continue;
          }
          
          $mbObject->_id400 = null;
          $id400Patient = new CIdSante400();
          $id400Patient->loadLatestFor($mbObject, $_receiver->_tag_sejour);
          $mbObject->_id400 = $id400Patient->id400;

          $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
          $domEvenementVenuePatient->_ref_receiver = $_receiver;
          $domEvenementVenuePatient->generateTypeEvenement($mbObject, true, $initiateur);
        }        
      }
      // Si Client
      else {
        if ($mbObject->_hprim_initiateur_group_id) {
          return;
        }

        $receiver          = new CDestinataireHprim();
        $receiver->type    = "sip";
        $receiver->message = "patients";
        $receivers         = $receiver->loadMatchingList();
        
        foreach ($receivers as $_receiver) {
          $_receiver->loadConfigValues();
          
          if (CGroups::loadCurrent()->_id != $_receiver->group_id) {
            continue;
          }
          
          if (!$mbObject->_num_dossier) {
            $num_dossier = new CIdSante400();
            //Paramétrage de l'id 400
            $num_dossier->loadLatestFor($mbObject, $_receiver->_tag_sejour);
    
            $mbObject->_num_dossier = $num_dossier->id400;
          }
          
          if (!$mbObject->_ref_patient->_IPP) {
            $IPP = new CIdSante400();
            //Paramétrage de l'id 400
            $IPP->loadLatestFor($mbObject->_ref_patient, $_receiver->_tag_patient);
    
            $mbObject->_ref_patient->_IPP = $IPP->id400;
          }
                    
          $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
          $domEvenementVenuePatient->_ref_receiver = $_receiver;
          $_receiver->sendEvenementPatient($domEvenementVenuePatient, $mbObject);
          
          if ($_receiver->_configs["send_debiteurs_venue"] && $mbObject->_ref_patient->code_regime) {
            $domEvenementDebiteursVenue = new CHPrimXMLDebiteursVenue();
            $domEvenementDebiteursVenue->_ref_receiver = $_receiver;
            $_receiver->sendEvenementPatient($domEvenementDebiteursVenue, $mbObject);
          }
          
          if ($_receiver->_configs["send_mvt_patients"] && $_receiver->_configs["send_default_serv_with_type_sej"] 
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
            $domEvenementMouvementPatient->_ref_receiver = $_receiver;
            $_receiver->sendEvenementPatient($domEvenementMouvementPatient, $affectation);
          }
          
          $mbObject->_num_dossier = null;
        }
      }
    }
    // Traitement Affectation
    elseif ($mbObject instanceof CAffectation) {
      $mbObject->loadRefLit();
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
        $receiver = new CDestinataireHprim();
        $receiver->type = "sip";
        $receiver->message = "patients";
        $receivers = $receiver->loadMatchingList();
        
        foreach ($receivers as $_destinataire) {
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
          $domEvenementMouvementPatient->_ref_receiver = $_destinataire;
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
    
    // Traitement Séjour
    if ($mbObject instanceof CSejour) { 
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
          $tap_NDA = CSejour::getTagNumDossier($_group->_id);

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
    
    // Traitement Séjour
    if ($mbObject instanceof CSejour) {
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
          $receiver = new CDestinataireHprim();
          $receiver->group_id = $group_id;
          $receiver->type     = "sip";
          $receiver->message  = "patients";
          $receivers = $receiver->loadMatchingList();
          
          foreach ($receivers as $_receiver) {
            if ($mbObject->_hprim_initiateur_group_id == $_receiver->group_id) {
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
              $domEvenementVenuePatient->_ref_receiver = $_receiver;
              
              if ($sejour2_nda)
                $sejour->_num_dossier = $sejour2_nda;
                
              $_receiver->sendEvenementPatient($domEvenementVenuePatient, $sejour);
              continue;
            }
            
            // Cas 2 NDA : Message de fusion
            if ($sejour1_nda && $sejour2_nda) {
              $domEvenementFusionVenue = new CHPrimXMLFusionVenue();
              $domEvenementFusionVenue->_ref_receiver = $_receiver;
                            
              $sejour->_sejour_eliminee = $sejour_eliminee;
              $_receiver->sendEvenementPatient($domEvenementFusionVenue, $sejour);
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