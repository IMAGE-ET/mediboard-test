<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("sip", "soaphandler");

/**
 * The CHprimSoapHandler class
 */
class CHprimSoapHandler extends CSoapHandler {

  static $paramSpecs = array(
    "evenementPatient" => array ( 
      "messagePatient" => "string"),
    "evenementServeurActes" => array ( 
      "messageServeurActes" => "string"),
    "evenementPmsi" => array ( 
      "messagePmsi" => "string"),
    "calculatorAuth" => array ( 
      "operation" => "string",
      "entier1"   => "int",
      "entier2"   => "int")
  );

  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare facility.
   * @param CHPrimXMLEvenementsPatients messagePatient
   * @return CHPrimXMLAcquittementsPatients messageAcquittement 
   **/
  function evenementPatient($messagePatient) {
    // Création de l'échange
    $echange_hprim = new CEchangeHprim();
    $messageAcquittement = null;
    $data = array();
        
    $domGetEvenement = CHPrimXMLEvenementsPatients::getHPrimXMLEvenementsPatients($messagePatient);
    
    try {
      // Récupération des informations du message XML
      $domGetEvenement->loadXML(utf8_decode($messagePatient));
      $doc_errors = $domGetEvenement->schemaValidate(null, true);
    
      $data = $domGetEvenement->getEnteteEvenementXML("evenementsPatients");
      
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->register($data['idClient']);
      
      // Gestion de l'acquittement
      $domAcquittement = new CHPrimXMLAcquittementsPatients();
      $domAcquittement->_identifiant_acquitte = $data['identifiantMessage'];
      $domAcquittement->_sous_type_evt        = $domGetEvenement->sous_type;
      
      // Acquittement d'erreur d'un document XML recu non valide
      if ($doc_errors !== true) {
        $echange_hprim->date_production     = mbDateTime();
        $echange_hprim->group_id            = $dest_hprim->group_id;
        $echange_hprim->store();
       
        $domAcquittement->_ref_echange_hprim = $echange_hprim;
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E002", $doc_errors);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->emetteur_id         = $data['idClient'] ? $dest_hprim->_id : 0;
        $echange_hprim->type                = "patients";
        $echange_hprim->sous_type           = $domGetEvenement->sous_type ? $domGetEvenement->sous_type : "inconnu";
        $echange_hprim->_message            = $messagePatient;
        $echange_hprim->_acquittement       = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->message_valide      = 0;
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        $echange_hprim->store();
  
        return $messageAcquittement;
      }
    
      if (!$echange_hprim->_id) {
        $echange_hprim->emetteur_id          = $dest_hprim->_id;
        $echange_hprim->group_id             = $dest_hprim->group_id;
        $echange_hprim->identifiant_emetteur = $data['identifiantMessage'];
        $echange_hprim->type                 = "patients";
        $echange_hprim->sous_type            = $domGetEvenement->sous_type;
        $echange_hprim->_message             = $messagePatient;
        $echange_hprim->message_valide       = 1;
      }
      $echange_hprim->date_production = mbDateTime();
      $echange_hprim->store();
      $echange_hprim->loadRefsDestinataireHprim();
      // Chargement des configs de l'émetteur
      $echange_hprim->_ref_emetteur->loadConfigValues();

      $domGetEvenement->_ref_echange_hprim = $echange_hprim;
      $domAcquittement->_ref_echange_hprim = $echange_hprim;
      
      $newPatient = new CPatient();
      $newPatient->_hprim_initiator_id = $echange_hprim->_id;

      // Un événement concernant un patient appartient à l'une des six catégories suivantes :
      // Enregistrement d'un patient avec son identifiant (ipp) dans le système
      if ($domGetEvenement instanceof CHPrimXMLEnregistrementPatient) {
        $data = array_merge($data, $domGetEvenement->getContentsXML());
        $echange_hprim->id_permanent = $data['idSourcePatient'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->enregistrementPatient($domAcquittement, $newPatient, $data);
      } 
      // Fusion de deux ipp
      else if($domGetEvenement instanceof CHPrimXMLFusionPatient) {
        $data = array_merge($data, $domGetEvenement->getContentsXML());
        $echange_hprim->id_permanent = $data['idSourcePatient'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->fusionPatient($domAcquittement, $newPatient, $data);
      } 
      // Venue d'un patient dans l'établissement avec son numéro de venue
      else if($domGetEvenement instanceof CHPrimXMLVenuePatient) {
        $data = array_merge($data, $domGetEvenement->getContentsXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->venuePatient($domAcquittement, $newPatient, $data);
      } 
      // Fusion de deux venues
      else if($domGetEvenement instanceof CHPrimXMLFusionVenue) {
        $data = array_merge($data, $domGetEvenement->getContentsXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->fusionVenue($domAcquittement, $newPatient, $data);
      }
      // Mouvement du patient dans une unité fonctionnelle ou médicale
      else if($domGetEvenement instanceof CHPrimXMLMouvementPatient) {
        $data = array_merge($data, $domGetEvenement->getContentsXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->mouvementPatient($domAcquittement, $newPatient, $data);
      }
      // Gestion des débiteurs d'une venue de patient
      else if($domGetEvenement instanceof CHPrimXMLDebiteursVenue) {
        $data = array_merge($data, $domGetEvenement->getContentsXML());
        $echange_hprim->id_permanent = $data['idSourcePatient'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->debiteursVenue($domAcquittement, $newPatient, $data);
      }
      // Aucun des six événements retour d'erreur
      else {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E007"); 
      }
      return $messageAcquittement;
      
    } catch (Exception $e) {
      $domAcquittement = new CHPrimXMLAcquittementsPatients();
      // Type par défaut
      $domAcquittement->_sous_type_evt = "enregistrementPatient";
      $domAcquittement->_identifiant_acquitte = $data['identifiantMessage'];
      $domAcquittement->_ref_echange_hprim = $echange_hprim;
      
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E009", $e->getMessage());
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->_acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->date_echange = mbDateTime();
      $echange_hprim->store();
      
      return $messageAcquittement;
    }   
  }
  
  /**
   * Codage CCAM vers les systèmes de facturation
   * @param CHPrimXMLEvenementServeurActes messageServeurActes
   * @return CHPrimXMLAcquittementsServeurActes messageAcquittement 
   **/
  function evenementServeurActes($messageServeurActes) {
    // Création de l'échange
    $echange_hprim = new CEchangeHprim();
    $messageAcquittement = null;
    $data = array();
    
    // Gestion de l'acquittement
    $domAcquittement = new CHPrimXMLAcquittementsServeurActes();
    
    $domGetEvenement = new CHPrimXMLEvenementsServeurActes();
    
    try {
      // Récupération des informations du message XML
      $domGetEvenement->loadXML(utf8_decode($messageServeurActes));
      $doc_errors = $domGetEvenement->schemaValidate(null, true);
    
      $data = $domGetEvenement->getEnteteEvenementXML("evenementsServeurActes");
      $domAcquittement->identifiant = $data['identifiantMessage'];
      $domAcquittement->destinataire = $data['idClient'];
      $domAcquittement->destinataire_libelle = $data['libelleClient'];
      $domAcquittement->_sous_type_evt = $domGetEvenement->sous_type;
      
      // Acquittement d'erreur d'un document XML recu non valide
      if ($doc_errors !== true) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("erreur", "E002", $doc_errors);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->date_production = mbDateTime();
        $echange_hprim->emetteur = $data['idClient'] ? $dest_hprim->_id : 0;
        $echange_hprim->destinataire = CAppUI::conf('mb_id');
        $echange_hprim->group_id = CGroups::loadCurrent()->_id;
        $echange_hprim->type = "pmsi";
        $echange_hprim->sous_type = $domGetEvenement->sous_type ? $domGetEvenement->sous_type : "inconnu";
        $echange_hprim->_message = $messageServeurActes;
        $echange_hprim->_acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->message_valide = 0;
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        $echange_hprim->store();
  
        return $messageAcquittement;
      }
    
      // Récupère l'initiateur du message s'il existe
      if (CAppUI::conf('sip server')) {
        $echange_hprim->identifiant_emetteur = intval($data['identifiantMessage']);
        $echange_hprim->loadMatchingObject();
      }
      if (!$echange_hprim->_id) {
        $echange_hprim->emetteur       = $dest_hprim->_id;
        $echange_hprim->destinataire   = CAppUI::conf('mb_id');
        $echange_hprim->group_id       = CGroups::loadCurrent()->_id;
        $echange_hprim->identifiant_emetteur = $data['identifiantMessage'];
        $echange_hprim->type           = "pmsi";
        $echange_hprim->sous_type      = $domGetEvenement->sous_type;
        $echange_hprim->_message        = $messageServeurActes;
        $echange_hprim->message_valide = 1;
      }
      $echange_hprim->date_production = mbDateTime();
      $echange_hprim->store();
  
      $data = array_merge($data, $domGetEvenement->getContentsXML());
      $echange_hprim->id_permanent = $data['idSourceVenue'];
      $messageAcquittement = $domGetEvenement->serveurActes($domAcquittement, $echange_hprim, $data);
     
      return $messageAcquittement;
    } catch (Exception $e) {
      /*$domAcquittement = new CHPrimXMLAcquittementsServeurActes();
      $domAcquittement->identifiant = $data['identifiantMessage'];
      $domAcquittement->destinataire = $data['idClient'];
      $domAcquittement->destinataire_libelle = $data['libelleClient'];
      $domAcquittement->_sous_type_evt = "evenementServeurActe";
      
      $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("erreur", "E009", $e->getMessage());
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->_acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->date_echange = mbDateTime();
      $echange_hprim->store();*/
      
      return $messageAcquittement;
    }
  }
  
  /**
   * Diagnostics CIM vers les systèmes de facturation
   * @param CHPrimXMLEvenementsPmsi messagePmsi
   * @return CHPrimXMLAcquittementsPmsi messageAcquittement 
   **/
  function evenementPmsi($messagePmsi) {
    // Création de l'échange
    $echange_hprim = new CEchangeHprim();
    $messageAcquittement = null;
    $data = array();
    
    // Gestion de l'acquittement
    $domAcquittement = new CHPrimXMLAcquittementsPmsi();
    
    
    
    return $messageAcquittement;
  }
  
  function calculatorAuth($operation, $entier1, $entier2) {
    $result = 0;

    if (($operation != "add") && ($operation != "subtract")) {
      return "Veuillez utiliser une methode d'operation valable (add/subtract).";
    } 
    if (!$entier1 || !$entier2) {
      return "Veuillez indiquer 2 entiers.";
    } 
    if ($operation == "add") {
      $result = $entier1 + $entier2;
    }
    if ($operation == "subtract") {
      $result = $entier1 -$entier2;
    }
    
    return $result;
  }
}
?>