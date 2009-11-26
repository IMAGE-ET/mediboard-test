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

  function onStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }

    if (!$mbObject->_ref_last_log) {
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
    if (is_array($mbObject->_merging)) {
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
        $dest_hprim->loadMatchingObject();
  
        if (!$mbObject->_IPP) {
          $IPP = new CIdSante400();
          //Paramétrage de l'id 400
          $IPP->object_class = "CPatient";
          $IPP->object_id = $mbObject->_id;
          $IPP->tag = $dest_hprim->_tag;
          $IPP->loadMatchingObject();
  
          $mbObject->_IPP = $IPP->id400;
        }
        
        // Envoi pas les patients qui n'ont pas d'IPP
        if (!CAppUI::conf("sip send_all_patients") && !$mbObject->_IPP) {
          return;
        }
  
        $domEvenement = (!$mbObject->_merging) ? new CHPrimXMLEnregistrementPatient() : new CHPrimXMLFusionPatient();
        $domEvenement->emetteur = CAppUI::conf('mb_id');
        $domEvenement->destinataire = $dest_hprim->destinataire;
        
        $msgEvtPatient = $domEvenement->generateTypeEvenement($mbObject);
           
        if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml")) {
          trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
        }
  
        // Récupère le message d'acquittement après l'execution la methode evenementPatient
        if (null == $acquittement = $client->evenementPatient($msgEvtPatient)) {
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
        $dest_hprim->loadMatchingObject();
  
        if (!$mbObject->_num_dossier) {
          $num_dossier = new CIdSante400();
          //Paramétrage de l'id 400
          $num_dossier->object_class = "CSejour";
          $num_dossier->object_id = $mbObject->_id;
          $num_dossier->tag = $dest_hprim->_tag;
          $num_dossier->tag = CAppUI::conf('mb_id')." group:$dest_hprim->group_id";
          $num_dossier->loadMatchingObject();
  
          $mbObject->_num_dossier = $num_dossier->id400;
        }
        
        $domEvenement = new CHPrimXMLVenuePatient();
        $domEvenement->emetteur = CAppUI::conf('mb_id');
        $domEvenement->destinataire = $dest_hprim->destinataire;
        
        $msgEvtVenuePatient = $domEvenement->generateTypeEvenement($mbObject);

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

  function onMerge(CMbObject &$mbObject) {
    if (!$mbObject->_merging) {
      return;
    }
    
    $patient1_id = $mbObject->_merging[0]; 
    $patient2_id = $mbObject->_merging[1]; 

    // Si Serveur
    if (CAppUI::conf('sip server')) {
      
    }
    // Si CIP
    else {
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->type = "sip";
      $dest_hprim->loadMatchingObject();
  
      $IPP = new CIdSante400();
      //Paramétrage de l'id 400
      $IPP->object_class = "CPatient";
      $IPP->object_id = $patient1_id;
      $IPP->tag = $dest_hprim->_tag;
      $IPP->loadMatchingObject();
      $patient1_ipp = $IPP->id400;
      
      $IPP->object_id = $patient2_id;
      $IPP->loadMatchingObject();
      $patient2_ipp = $IPP->id400;
      
      $min_ipp = "";
      if ($patient1_ipp || $patient2_ipp) {
        $min_ipp = min($patient1_ipp, $patient2_ipp);
      }

      $mbObject->_merging = $min_ipp ? ($patient1_ipp ? $patient1_id : $patient2_id) : (min($patient1_id,$patient2_id));
    }
  }

  function onDelete(CMbObject &$mbObject) {
  }
}
?>