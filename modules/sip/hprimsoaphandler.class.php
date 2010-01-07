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
    
    // Gestion de l'acquittement
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    
    $domGetEvenement = CHPrimXMLEvenementsPatients::getHPrimXMLEvenementsPatients($messagePatient);
    
    try {
      // Récupération des informations du message XML
      $domGetEvenement->loadXML(utf8_decode($messagePatient));
      $doc_errors = $domGetEvenement->schemaValidate(null, true);
    
      $data = $domGetEvenement->getEvenementPatientXML();
      $domAcquittement->identifiant = $data['identifiantMessage'];
      $domAcquittement->destinataire = $data['idClient'];
      $domAcquittement->destinataire_libelle = $data['libelleClient'];
      $domAcquittement->_sous_type_evt = $domGetEvenement->sous_type;
      
      // Acquittement d'erreur d'un document XML recu non valide
      if ($doc_errors !== true) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E002", $doc_errors);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->date_production = mbDateTime();
        $echange_hprim->emetteur = "inconnu";
        $echange_hprim->destinataire = CAppUI::conf('mb_id');
        $echange_hprim->group_id = CGroups::loadCurrent()->_id;
        $echange_hprim->type = "patients";
        $echange_hprim->sous_type = $domGetEvenement->sous_type ? $domGetEvenement->sous_type : "inconnu";
        $echange_hprim->message = $messagePatient;
        $echange_hprim->acquittement = $messageAcquittement;
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
        $echange_hprim->emetteur       = $data['idClient'];
        $echange_hprim->destinataire   = CAppUI::conf('mb_id');
        $echange_hprim->group_id       = CGroups::loadCurrent()->_id;
        $echange_hprim->identifiant_emetteur = $data['identifiantMessage'];
        $echange_hprim->type           = "patients";
        $echange_hprim->sous_type      = $domGetEvenement->sous_type;
        $echange_hprim->message        = $messagePatient;
        $echange_hprim->message_valide = 1;
      }
      $echange_hprim->date_production = mbDateTime();
      $echange_hprim->store();
  
      $newPatient = new CPatient();
      $newPatient->_hprim_initiator_id = $echange_hprim->_id;
  	  
      // Un événement concernant un patient appartient à l'une des six catégories suivantes :
  
      // Enregistrement d'un patient avec son identifiant (ipp) dans le système
      if ($domGetEvenement instanceof CHPrimXMLEnregistrementPatient) {
        $data = array_merge($data, $domGetEvenement->getEnregistrementPatientXML());
        $echange_hprim->id_permanent = $data['idSource'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->enregistrementPatient($domAcquittement, $echange_hprim, $newPatient, $data);
      } 
      // Fusion de deux ipp
      else if($domGetEvenement instanceof CHPrimXMLFusionPatient) {
        $data = array_merge($data, $domGetEvenement->getFusionPatientXML());
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->fusionPatient($domAcquittement, $echange_hprim, $newPatient, $data);
      } 
      // Venue d'un patient dans l'établissement avec son numéro de venue
      else if($domGetEvenement instanceof CHPrimXMLVenuePatient) {
        $data = array_merge($data, $domGetEvenement->getVenuePatientXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->venuePatient($domAcquittement, $echange_hprim, $newPatient, $data);
      } 
      // Fusion de deux venues
      else if($domGetEvenement instanceof CHPrimXMLFusionVenue) {
        $data = array_merge($data, $domGetEvenement->getFusionVenueXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->fusionVenue($domAcquittement, $echange_hprim, $newPatient, $data);
      }
      // Mouvement du patient dans une unité fonctionnelle ou médicale
      else if($domGetEvenement instanceof CHPrimXMLMouvementPatient) {
        $data = array_merge($data, $domGetEvenement->getMouvementPatientXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->mouvementPatient($domAcquittement, $echange_hprim, $newPatient, $data);
      }
      // Gestion des débiteurs d'une venue de patient
      else if($domGetEvenement instanceof CHPrimXMLDebiteursVenue) {
        $data = array_merge($data, $domGetEvenement->getDebiteursVenueXML());
        $echange_hprim->id_permanent = $data['idSourceVenue'];
        if ($messageAcquittement = $domGetEvenement->isActionValide($data['action'], $domAcquittement, $echange_hprim)) {
          return $messageAcquittement;
        }
        $messageAcquittement = $domGetEvenement->debiteursVenue($domAcquittement, $echange_hprim, $newPatient, $data);
      }
      // Aucun des six événements retour d'erreur
      else {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E007"); 
      }
      return $messageAcquittement;
      
    } catch (Exception $e) {
      $domAcquittement = new CHPrimXMLAcquittementsPatients();
      $domAcquittement->identifiant = $data['identifiantMessage'];
      $domAcquittement->destinataire = $data['idClient'];
      $domAcquittement->destinataire_libelle = $data['libelleClient'];
    	$domAcquittement->_sous_type_evt = $domGetEvenement->sous_type;
    	
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E009", $e->getMessage());
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->date_echange = mbDateTime();
      $echange_hprim->store();
      
      return $messageAcquittement;
    }   
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