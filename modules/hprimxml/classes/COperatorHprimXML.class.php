<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 10912 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * The COperatorHprimXML class
 */
class COperatorHprimXML extends CEAIOperator {
  function event(CExchangeDataFormat $data_format) {
    $msg     = $data_format->_message;
    $dom_evt = $data_format->_family_message->getHPrimXMLEvenements($msg);
    
    $dom_evt_class = get_class($dom_evt);
    if (!in_array($dom_evt_class, $data_format->_messages_supported_class)) {
      throw new CMbException(CAppUI::tr("CEAIDispatcher-no_message_supported_for_this_actor", $dom_evt_class));
    }
    
    // Récupération des informations du message XML
    $dom_evt->loadXML($msg);
    $doc_errors = $dom_evt->schemaValidate(null, true);
    
    // Récupération du noeud racine
    $root     = $dom_evt->documentElement;
    $nodeName = $root->nodeName;
    
    try {
      // Création de l'échange
      $echg_hprim = new CEchangeHprim();
      $echg_hprim->load($data_format->_exchange_id);
      
      // Récupération des données de l'entête
      $data = $dom_evt->getEnteteEvenementXML($nodeName);
  
      // Gestion de l'acquittement
      $dom_acq                        = CHPrimXMLAcquittements::getAcquittementEvenementXML($dom_evt);
      $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
      $dom_acq->_sous_type_evt        = $dom_evt->sous_type;

      // Acquittement d'erreur d'un document XML recu non valide
      if ($doc_errors !== true) {
        $echg_hprim->populateEchange($data_format, $dom_evt);

        $dom_acq->_ref_echange_hprim = $echg_hprim;
        $msgAcq    = $dom_acq->generateAcquittements($dom_acq instanceof CHPrimXMLAcquittementsServeurActivitePmsi ? 
         "err" : "erreur", "E002", $doc_errors);
        $doc_valid = $dom_acq->schemaValidate();

        $echg_hprim->populateErrorEchange($msgAcq, $doc_valid, "erreur");

        return $msgAcq;
      }

      // Gestion des notifications ? 
      if (!$echg_hprim->_id) {
        $echg_hprim->populateEchange($data_format, $dom_evt);
        $echg_hprim->identifiant_emetteur = $data['identifiantMessage'];
        $echg_hprim->message_valide       = 1;
      }
      
      $echg_hprim->date_production = mbDateTime();
      $echg_hprim->store();
      
      if (!$data_format->_to_treatment) {
        return;
      }
      
      $echg_hprim->loadRefsInteropActor();

      // Chargement des configs de l'expéditeur
      $echg_hprim->_ref_sender->getConfigs($data_format);

      $dom_evt->_ref_echange_hprim = $echg_hprim;
      $dom_acq->_ref_echange_hprim = $echg_hprim;
    
      // Message événement patient
      if ($dom_evt instanceof CHPrimXMLEvenementsPatients) {
        return self::eventPatient($dom_evt, $data, $dom_acq, $echg_hprim);
      }
      // Message serveur activité PMSI
      elseif ($dom_evt instanceof CHPrimXMLEvenementsServeurActivitePmsi) {
        return self::eventPMSI($dom_evt, $data, $dom_acq, $echg_hprim);
      }
    } catch(Exception $e) {
      $echg_hprim->populateEchange($data_format, $dom_evt);
      
      $dom_acq = CHPrimXMLAcquittements::getAcquittementEvenementXML($dom_evt);
      
      // Type par défaut
      $dom_acq->_sous_type_evt        = "none";
      $dom_acq->_identifiant_acquitte = isset($data['identifiantMessage']) ? $data['identifiantMessage'] : "000000000";
      $dom_acq->_ref_echange_hprim    = $echg_hprim;

      $msgAcq = $dom_acq->generateAcquittements($dom_acq instanceof CHPrimXMLAcquittementsServeurActivitePmsi ? 
         "err" : "erreur", "E009", $e->getMessage());
    
      $doc_valid = $dom_acq->schemaValidate();
      $echg_hprim->populateErrorEchange($msgAcq, $doc_valid, "erreur");
      
      return $msgAcq;
    }
  }
  
  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare facility.
   * @param CHPrimXMLEvenementsPatients messagePatient
   * @return CHPrimXMLAcquittementsPatients messageAcquittement 
   **/
  static function eventPatient(CHPrimXMLEvenementsPatients $dom_evt, 
                               $data = array(), CHPrimXMLAcquittementsPatients $dom_acq, CEchangeHprim $echg_hprim) {
    $newPatient = new CPatient();
    $newPatient->_eai_exchange_initiator_id = $echg_hprim->_id;
   
    // Un événement concernant un patient appartient à l'une des six catégories suivantes :
    // Enregistrement d'un patient avec son identifiant (ipp) dans le système
    if ($dom_evt instanceof CHPrimXMLEnregistrementPatient) {
      $data = array_merge($data, $dom_evt->getContentsXML());
      $echg_hprim->id_permanent = $data['idSourcePatient'];
      if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq)) {
        return $msgAcq;
      }
      $msgAcq = $dom_evt->enregistrementPatient($dom_acq, $newPatient, $data);
    } 
    // Fusion de deux ipp
    else if($dom_evt instanceof CHPrimXMLFusionPatient) {
      $data = array_merge($data, $dom_evt->getContentsXML());
      $echg_hprim->id_permanent = $data['idSourcePatient'];
      if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq)) {
        return $msgAcq;
      }
      $msgAcq = $dom_evt->fusionPatient($dom_acq, $newPatient, $data);
    } 
    // Venue d'un patient dans l'établissement avec son numéro de venue
    else if($dom_evt instanceof CHPrimXMLVenuePatient) {
      $data = array_merge($data, $dom_evt->getContentsXML());
      $echg_hprim->id_permanent = $data['idSourceVenue'];
      if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq)) {
        return $msgAcq;
      }
      $msgAcq = $dom_evt->venuePatient($dom_acq, $newPatient, $data);
    } 
    // Fusion de deux venues
    else if($dom_evt instanceof CHPrimXMLFusionVenue) {
      $data = array_merge($data, $dom_evt->getContentsXML());
      $echg_hprim->id_permanent = $data['idSourceVenue'];
      if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq)) {
        return $msgAcq;
      }
      $msgAcq = $dom_evt->fusionVenue($dom_acq, $newPatient, $data);
    }
    // Mouvement du patient dans une unité fonctionnelle ou médicale
    else if($dom_evt instanceof CHPrimXMLMouvementPatient) {
      $data = array_merge($data, $dom_evt->getContentsXML());
      $echg_hprim->id_permanent = $data['idSourceVenue'];
      if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq)) {
        return $msgAcq;
      }
      $msgAcq = $dom_evt->mouvementPatient($dom_acq, $newPatient, $data);
    }
    // Gestion des débiteurs d'une venue de patient
    else if($dom_evt instanceof CHPrimXMLDebiteursVenue) {
      $data = array_merge($data, $dom_evt->getContentsXML());
      $echg_hprim->id_permanent = $data['idSourcePatient'];
      if ($msgAcq = $dom_evt->isActionValide($data['action'], $dom_acq, $echg_hprim)) {
        return $msgAcq;
      }
      $msgAcq = $dom_evt->debiteursVenue($dom_acq, $newPatient, $data);
    }
    // Aucun des six événements retour d'erreur
    else {
      $msgAcq = $dom_acq->generateAcquittements("erreur", "E007"); 
    }
    return $msgAcq;
  }
  
  static function eventPMSI(CExchangeDataFormat $data_format, CHPrimXMLEvenementsServeurActivitePmsi $dom_evt, 
                            $data = array(), CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq, CEchangeHprim $echg_hprim) {
    mbTrace($dom_evt);
  }
}
?>