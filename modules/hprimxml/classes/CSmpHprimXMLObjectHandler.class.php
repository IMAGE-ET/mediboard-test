<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage smp
 * @version $Revision: 12577 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSmpHprimXMLObjectHandler extends CHprimXMLObjectHandler {
  static $handled = array ("CSejour", "CAffectation");

  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
    $receiver = $mbObject->_receiver;  
    
    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $mbObject->loadRefPraticien();
      $mbObject->loadNDA();
      $mbObject->loadLastLog();
      
      $mbObject->loadRefPatient();
      $mbObject->loadRefAdresseParPraticien();

      // Si Serveur
      if (CAppUI::conf('smp server')) {
        
        $echange_hprim = new CEchangeHprim();
        if (isset($mbObject->_eai_exchange_initiator_id)) {
          $echange_hprim->load($mbObject->_eai_exchange_initiator_id);
        }

        $initiateur = ($receiver->_id == $echange_hprim->sender_id) ? $echange_hprim->_id : null;
        
        $group = new CGroups();
        $group->load($receiver->group_id);
        $group->loadConfigValues();
        
        if (!$initiateur && !$group->_configs["sip_notify_all_actors"]) {
          
        }
        
        $mbObject->_id400 = null;
        $id400Patient = new CIdSante400();
        $id400Patient->loadLatestFor($mbObject, $receiver->_tag_sejour);
        $mbObject->_id400 = $id400Patient->id400;

        $this->generateTypeEvenement("CHPrimXMLVenuePatient", $mbObject, true, $initiateur);
      }
      // Si Client
      else {
        if ($mbObject->_eai_initiateur_group_id || !$receiver->isMessageSupported("CHPrimXMLVenuePatient")) {
          return;
        }
          
        if (CGroups::loadCurrent()->_id != $receiver->group_id) {
          return;
        }
        
        if (!$mbObject->_NDA) {
          $nda = new CIdSante400();
          //Paramétrage de l'id 400
          $nda->loadLatestFor($mbObject, $receiver->_tag_sejour);
  
          $mbObject->_NDA = $nda->id400;
        }
        
        if (!$mbObject->_ref_patient->_IPP) {
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->loadLatestFor($mbObject->_ref_patient, $receiver->_tag_patient);
  
          $mbObject->_ref_patient->_IPP = $IPP->id400;
        }
        
        $this->sendEvenementPatient("CHPrimXMLVenuePatient", $mbObject);
        
        if ($receiver->isMessageSupported("CHPrimXMLDebiteursVenue") && 
            $mbObject->_ref_patient->code_regime) {
          $this->sendEvenementPatient("CHPrimXMLDebiteursVenue", $mbObject);
        }
        
        if ($receiver->isMessageSupported("CHPrimXMLMouvementPatient") && $receiver->_configs["send_default_serv_with_type_sej"] 
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
          $affectation->_ref_sejour->loadNDA();
          $affectation->_ref_sejour->loadRefPatient();
          $affectation->_ref_sejour->loadRefPraticien();
          
          $this->sendEvenementPatient("CHPrimXMLMouvementPatient", $affectation);
        }
        
        $mbObject->_NDA = null;
      }
    }
    // Traitement Affectation
    elseif ($mbObject instanceof CAffectation) {
      // Si Client
      if (!CAppUI::conf('smp server')) {
        if (!$receiver->isMessageSupported("CHPrimXMLMouvementPatient")) {
          return;
        }
        
        $mbObject->loadRefLit();
        $mbObject->_ref_lit->loadRefChambre();
        $mbObject->_ref_lit->_ref_chambre->loadRefService();
        $mbObject->loadLastLog();
        $mbObject->loadRefSejour();
        $mbObject->_ref_sejour->loadNDA();
        $mbObject->_ref_sejour->loadRefPatient();
        $mbObject->_ref_sejour->loadRefPraticien();
      
        if (!$mbObject->_ref_sejour->_NDA) {
          $nda = new CIdSante400();
          //Paramétrage de l'id 400
          $nda->loadLatestFor($mbObject->_ref_sejour, $receiver->_tag_sejour);
  
          $mbObject->_ref_sejour->_NDA = $nda->id400;
        }
        
        $this->sendEvenementPatient("CHPrimXMLMouvementPatient", $mbObject);
        
        $mbObject->_NDA = null;
      }
    }
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    // Traitement Séjour
    if ($mbObject instanceof CSejour) { 
      $sejour = $mbObject;

      $sejour_eliminee = new CSejour();
      $sejour_eliminee->load(reset($mbObject->_merging));
      $sejour_eliminee->updateFormFields();
      $sejour_eliminee->loadRefPatient();
      $sejour_eliminee->loadRefPraticien();
      $sejour_eliminee->loadLastLog();
      $sejour_eliminee->loadRefAdresseParPraticien();
      
      // Si Client
      if (!CAppUI::conf('smp server')) {
        $mbObject->_fusion = array();
        foreach (CGroups::loadGroups() as $_group) {
          if ($mbObject->_eai_initiateur_group_id == $_group->_id) {
            continue;
          }
          
          $sejour->_NDA = null;
          $sejour->loadNDA($_group->_id);
          $sejour1_nda = $sejour->_NDA;

          $sejour_eliminee->_NDA = null;
          $sejour_eliminee->loadNDA($_group->_id);
          $sejour2_nda = $sejour_eliminee->_NDA;
          
          // Passage en trash des NDA des séjours
          $tap_NDA = CSejour::getTagNDA($_group->_id);

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
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
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
      
      $receiver = $mbObject->_receiver;
      
      // Si Client
      if (!CAppUI::conf('smp server')) {
        foreach ($mbObject->_fusion as $group_id => $infos_fus) {
          if ($receiver->group_id != $group_id) {
            continue;
          } 

          if ($mbObject->_eai_initiateur_group_id == $receiver->group_id) {
            continue;
          }
          
          $sejour1_nda = $sejour->_NDA = $infos_fus["sejour1_nda"];
          
          $sejour_eliminee = $infos_fus["sejourEliminee"];
          $sejour2_nda = $sejour_eliminee->_NDA = $infos_fus["sejour2_nda"];

          // Cas 0 NDA : Aucune notification envoyée
          if (!$sejour1_nda && !$sejour2_nda) {
            continue;
          }
         
          // Cas 1 NDA : Pas de message de fusion mais d'une modification de la venue
          if ((!$sejour1_nda && $sejour2_nda) || ($sejour1_nda && !$sejour2_nda)) {
            if ($sejour2_nda)
              $sejour->_NDA = $sejour2_nda;
            
            $this->sendEvenementPatient("CHPrimXMLVenuePatient", $sejour);
            continue;
          }
          
          // Cas 2 NDA : Message de fusion
          if ($sejour1_nda && $sejour2_nda) {
            $sejour->_sejour_eliminee = $sejour_eliminee;
            
            $this->sendEvenementPatient("CHPrimXMLFusionVenue", $sejour);
            continue;
          }
        }        
      }
    }
  }  

  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
  }
}
?>