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
    
    // Si client et traitement HPRIM
    if (isset($mbObject->_coms_from_hprim) && ($mbObject->_coms_from_hprim == 1) && !CAppUI::conf('sip server')) {
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
        $dest_hprim->type = "sip";
        $dest_hprim->group_id = CGroups::loadCurrent()->_id;
        $dest_hprim->loadMatchingObject();
        
        if (!$dest_hprim->_id) {
          return;
        }
        
        if (!$mbObject->_IPP) {
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->object_class = "CPatient";
          $IPP->object_id = $mbObject->_id;
          $IPP->tag = $dest_hprim->_tag_patient;
          $IPP->loadMatchingObject();
  
          $mbObject->_IPP = $IPP->id400;
        }
        
        // Envoi pas les patients qui n'ont pas d'IPP
        if (!CAppUI::conf("sip send_all_patients") && !$mbObject->_IPP) {
          return;
        }
        
        $domEvenement = new CHPrimXMLEnregistrementPatient();
        $this->sendEvenement($domEvenement, $dest_hprim, $mbObject);
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
        $dest_hprim->type = "sip";
        $dest_hprim->group_id = CGroups::loadCurrent()->_id;
        $dest_hprim->loadMatchingObject();
        
        if (!$dest_hprim->_id) {
          return;
        }
        
        if (!$mbObject->_num_dossier) {
          $num_dossier = new CIdSante400();
          //Paramétrage de l'id 400
          $num_dossier->object_class = "CSejour";
          $num_dossier->object_id = $mbObject->_id;
          $num_dossier->tag = $dest_hprim->_tag_sejour;
          $num_dossier->loadMatchingObject();
  
          $mbObject->_num_dossier = $num_dossier->id400;
        }
        
        $domEvenementVenuePatient = new CHPrimXMLVenuePatient();
        $this->sendEvenement($domEvenementVenuePatient, $dest_hprim, $mbObject);
        
        if ($mbObject->_ref_patient->code_regime) {
          $domEvenementDebiteursVenue = new CHPrimXMLDebiteursVenue();
          $this->sendEvenement($domEvenementDebiteursVenue, $dest_hprim, $mbObject);
        }
      }
    }
  }

  function onBeforeMerge(CMbObject &$mbObject) {
    if (!$mbObject->_merging) {
      return;
    }
    /*
    // Traitement Patient
    if ($mbObject instanceof CPatient) {
      $patient_eliminee = new CPatient();
      $patient_eliminee->load(reset($mbObject->_merging));

      // Si Client
      if (!CAppUI::conf('sip server')) {
        $dest_hprim = new CDestinataireHprim();
        $dest_hprim->type = "sip";
        $dest_hprim->group_id = CGroups::loadCurrent()->_id;
        $dest_hprim->loadMatchingObject();
        
        // Patient 1
        $IPP_pat1 = new CIdSante400();
        //Paramétrage de l'id 400
        $IPP_pat1->object_class = "CPatient";
        $IPP_pat1->object_id = $patient1->_id;
        $IPP_pat1->tag = $dest_hprim->_tag_patient;
        $IPP_pat1->loadMatchingObject();
        $patient1_ipp = $patient1->_IPP = $IPP_pat1->id400;
        
        // Patient 2
        $IPP_pat2 = new CIdSante400();
        //Paramétrage de l'id 400
        $IPP_pat2->object_class = "CPatient";
        $IPP_pat2->object_id = $patient2->_id;
        $IPP_pat2->tag = $dest_hprim->_tag_patient;
        $IPP_pat2->loadMatchingObject();
        $patient2_ipp = $patient2->_IPP = $IPP_pat2->id400;

        // Cas 0 IPP : Aucune notification envoyée
        if (!$patient1_ipp && !$patient2_ipp) {
          mbTrace("cas : ", "0", true);
          return;
        }
       mbTrace($patient1_ipp, "1", true);
       mbTrace($patient2_ipp, "2", true);
        // Cas 1 IPP : Pas de message de fusion mais d'une modification du patient
        if ((!$patient1_ipp && $patient2_ipp) || ($patient1_ipp && !$patient2_ipp)) {
          mbTrace("cas : ", "1", true);
          $domEvenement = new CHPrimXMLEnregistrementPatient();
          
          if ($patient2_ipp)
            $patient1->_IPP = $patient2_ipp;
            
          $patient1->check();
          $this->sendEvenement($domEvenement, $dest_hprim, $patient1);
        }
        
        // Cas 2 IPPs : Message de fusion
        if ($patient1_ipp && $patient2_ipp) {
          mbTrace("cas : ", "2", true);
          $domEvenement = new CHPrimXMLFusionPatient();
          
          $patient1->_patient_elimine = $patient2;
          $this->sendEvenement($domEvenement, $dest_hprim, $patient1);
        }
      }
    }*/
  }

  function onAfterDelete(CMbObject &$mbObject) {
  }
  
  function sendEvenement ($domEvenement, $dest_hprim, $mbObject) {
    $domEvenement->emetteur = CAppUI::conf('mb_id');
    $domEvenement->destinataire = $dest_hprim->nom;
    
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