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
    
    $dest_hprim = new CDestinataireHprim();
    
    // Traitement Patient
    if ($mbObject instanceof CPatient) {
      if ($mbObject->_anonyme || $mbObject->_update_vitale) {
        return;
      }
      
      // Si Serveur
      if (CAppUI::conf('sip server')) {
        $listDest = $dest_hprim->loadList();
  
        foreach ($listDest as $_dest) {
          // Recherche si le patient possède un identifiant externe sur le SIP
          $id400 = new CIdSante400();
          //Paramétrage de l'id 400
          $id400->object_id = $mbObject->_id;
          $id400->object_class = "CPatient";
          $id400->tag = "$_dest->destinataire :$_dest->group_id";
  
          if($id400->loadMatchingObject())
            $mbObject->_id400 = $id400->id400;
          else
            $mbObject->_id400 = null;
  
          if (!$mbObject->_IPP) {
            $IPP = new CIdSante400();
            //Paramétrage de l'id 400
            $IPP->object_class = "CPatient";
            $IPP->object_id = $mbObject->_id;
            $IPP->tag = CAppUI::conf("mb_id");
            $IPP->loadMatchingObject();
  
            $mbObject->_IPP = $IPP->id400;
          }
          
          $domEvenement = new CHPrimXMLEnregistrementPatient();
          $domEvenement->emetteur = CAppUI::conf('mb_id');
          $domEvenement->destinataire = $_dest->destinataire;
          $domEvenement->destinataire_libelle = " ";
  
          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }
  
          $initiateur = ($_dest->destinataire == $echange_hprim->emetteur) ? $echange_hprim->_id : null;
  
          $domEvenement->generateTypeEvenement($mbObject, true, $initiateur);
        }
      }
      // Si Client
      else {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
        $destinataires = $dest_hprim->loadMatchingList();
        
        foreach ($destinataires as $_destinataire) {
          if ($mbObject->_hprim_initiateur_group_id == $_destinataire->group_id) {
            continue;
          }
          
          if (!$mbObject->_IPP) {
            $IPP = new CIdSante400();
            $IPP->loadLatestFor($mbObject, $_destinataire->_tag_patient);
            
            $mbObject->_IPP = $IPP->id400;
          }
          
          // Envoi pas les patients qui n'ont pas d'IPP
          if (!CAppUI::conf("sip send_all_patients") && !$mbObject->_IPP) {
            return;
          }
          
          $domEvenement = new CHPrimXMLEnregistrementPatient();
          $this->sendEvenement($domEvenement, $_destinataire, $mbObject);
        }
      }
    // Traitement Sejour
    } else if ($mbObject instanceof CSejour) {
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
        $listDest = $dest_hprim->loadList();
  
        foreach ($listDest as $_dest) {
          // Recherche si le séjour possède un identifiant externe sur le SIP
          $id400 = new CIdSante400();
          //Paramétrage de l'id 400
          $id400->object_id = $mbObject->_id;
          $id400->object_class = "CSejour";
          $id400->tag = $_dest->destinataire;
  
          if($id400->loadMatchingObject())
            $mbObject->_id400 = $id400->id400;
          else
            $mbObject->_id400 = null;
  
          if (!$mbObject->_num_dossier) {
            $num_dossier = new CIdSante400();
            //Paramétrage de l'id 400
            $num_dossier->object_class = "CSejour";
            $num_dossier->object_id = $mbObject->_id;
            $num_dossier->tag = CAppUI::conf("mb_id");
            $num_dossier->loadMatchingObject();
  
            $mbObject->_num_dossier = $num_dossier->id400;
          }
          
          $domEvenement = new CHPrimXMLVenuePatient();
          $domEvenement->emetteur = CAppUI::conf('mb_id');
          $domEvenement->destinataire = $_dest->destinataire;
          $domEvenement->destinataire_libelle = " ";
  
          $echange_hprim = new CEchangeHprim();
          if (isset($mbObject->_hprim_initiator_id)) {
            $echange_hprim->load($mbObject->_hprim_initiator_id);
          }
  
          $initiateur = ($_dest->destinataire == $echange_hprim->emetteur) ? $echange_hprim->_id : null;
  
          $domEvenement->generateTypeEvenement($mbObject, true, $initiateur);
        }
      }
       // Si Client
      else {
        if ($mbObject->_hprim_initiateur_group_id) {
          return;
        }
          
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
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
          $this->sendEvenement($domEvenementVenuePatient, $_destinataire, $mbObject);
          
          if ($mbObject->_ref_patient->code_regime) {
            $domEvenementDebiteursVenue = new CHPrimXMLDebiteursVenue();
            $this->sendEvenement($domEvenementDebiteursVenue, $_destinataire, $mbObject);
          }
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
            $domEvenement = new CHPrimXMLEnregistrementPatient();
            
            if ($patient2_ipp)
              $patient->_IPP = $patient2_ipp;
              
            $this->sendEvenement($domEvenement, $dest_hprim, $patient);
            continue;
          }
          
          // Cas 2 IPPs : Message de fusion
          if ($patient1_ipp && $patient2_ipp) {
            $domEvenement = new CHPrimXMLFusionPatient();
            
            $patient->_patient_elimine = $patient_eliminee;
            $this->sendEvenement($domEvenement, $dest_hprim, $patient);
            continue;
          }
        }        
      }
    }
  }
  
  function onAfterDelete(CMbObject &$mbObject) {
  }
  
  function sendEvenement ($domEvenement, $dest_hprim, $mbObject) {
    $domEvenement->emetteur     = CAppUI::conf('mb_id');
    $domEvenement->destinataire = $dest_hprim->nom;
    $domEvenement->group_id     = $dest_hprim->group_id;
    
    $msgEvtVenuePatient = $domEvenement->generateTypeEvenement($mbObject);
    
    if (CAppUI::conf('sip enable_send')) {
      if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml")) {
        trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
      }
  
      // Récupère le message d'acquittement après l'execution la methode evenementPatient
      if (null == $acquittement = $client->evenementPatient($msgEvtVenuePatient)) {
        trigger_error("Evénement patient impossible sur le SIP : ".$dest_hprim->url);
      }
      
      $echange_hprim = new CEchangeHprim();
      $echange_hprim->load($domEvenement->identifiant);
      $echange_hprim->date_echange = mbDateTime();
      
      $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
      $domGetAcquittement->loadXML(utf8_decode($acquittement));        
      $doc_valid = $domGetAcquittement->schemaValidate();
      
      $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->acquittement = $acquittement;
  
      $echange_hprim->store();
    }
    
  }
}
?>