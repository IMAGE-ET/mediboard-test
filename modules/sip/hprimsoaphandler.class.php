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

  static $codes = array(
    "E00" => "Erreur inattendue",
    "E01" => "L'émetteur du message n'est pas conforme avec l'établissement enregistré dans le SIP.",
    "E02" => "La grammaire du message XML n'est pas respectée.",
    "E03" => "Les identifiants fournis sont incohérents. L'IPP renvoyé ne correspond pas à celui associé à l'identifiant source.",
    "E04" => "Disconcordance entre l'identifiant source et l'identifiant cible.",
    "E05" => "Identifiants source et cible non fournis.",
    "E06" => "IPP non fourni.",
    "E07" => "Le type d'événement reçu ne correspond pas a un type d'événement patient du modèle HPRIM.",
  
    "A01" => "IPP envoyé non existant sur le SIP, attribution IPP forcée.",
    "A02" => "L'enregistrement du patient a échoué.",
    "A03" => "Modification du patient a échoué.",
    "A04" => "Création de l'IC a échoué.",
    "A05" => "Création de l'IPP a échoué.",
    "A06" => "Modification de l'IC a échoué.",
    "A20" => "IPP connu, IC non connu. Mauvais IC sur le SIP.",
    "A21" => "IPP non connu, IC non fourni. Patient retrouvé. Association IPP.",
    "A22" => "IPP non connu, IC non connu. Patient retrouvé. Association IPP.",

    "I01" => "L'enregistrement du patient est terminé.",
    "I02" => "Modification du patient terminée.",
    "I03" => "IC non fourni.",
    "I04" => "IC non connu. Association IC.",
    "I05" => "IC mis à jour. Modification IC.",
    "I06" => "IPP non fourni. Association IPP.",
    "I07" => "IPP non connu. Association IPP.",
    "I08" => "IPP non fourni mais retrouvé.",
    "I09" => "IPP fourni mais déjà utilisé. Association IPP.",
    "I20" => "IPP non connu, IC non connu. Association IPP.",
    "I21" => "IPP non connu, IC connu. Association IPP.",
    "I22" => "IPP non connu, IC non fourni. Association IPP.",
    "I23" => "IPP connu, IC non fourni.",
    "I24" => "IPP connu, IC connu.",
  );
  
  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare facility.
   * @param CHPrimXMLEvenementsPatients messagePatient
   * @return CHPrimXMLAcquittementsPatients messageAcquittement 
   **/
  function evenementPatient($messagePatient) {
    global $m;
    
    // Création de l'échange
    $echange_hprim = new CEchangeHprim();

    // Gestion de l'acquittement
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    
    $domGetEvenement = CHPrimXMLEvenementsPatients::getHPrimXMLEvenementsPatients($messagePatient);

    // Récupération des informations du message XML
    $domGetEvenement->loadXML(utf8_decode($messagePatient));
    $doc_errors = $domGetEvenement->schemaValidate(null, true);

    // Acquittement d'erreur d'un document XML recu non valide
    if ($doc_errors !== true) {
      $domAcquittement->_identifiant = "inconnu";
      $domAcquittement->_destinataire = "inconnu";
      $domAcquittement->_destinataire_libelle = "inconnu document xml non valide";

      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E02", $doc_errors);
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->date_production = mbDateTime();
      $echange_hprim->emetteur = "inconnu";
      $echange_hprim->destinataire = CAppUI::conf('mb_id');
      $echange_hprim->type = "evenementsPatients";
      $echange_hprim->message = $messagePatient;
      $echange_hprim->acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->message_valide = 0;
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->store();

      return $messageAcquittement;
    }
    
    $data = $domGetEvenement->getEvenementPatientXML();
    
    $domAcquittement->_identifiant = $data['identifiantMessage'];
    $domAcquittement->_destinataire = $data['idClient'];
    $domAcquittement->_destinataire_libelle = $data['libelleClient'];

    // Récupère l'initiateur du message s'il existe
    if (CAppUI::conf('sip server')) {
      $echange_hprim->identifiant_emetteur = intval($data['identifiantMessage']);
      $echange_hprim->loadMatchingObject();
    }
    if (!$echange_hprim->_id) {
      $echange_hprim->emetteur = $data['idClient'];
      $echange_hprim->destinataire = CAppUI::conf('mb_id');
      $echange_hprim->identifiant_emetteur = $data['identifiantMessage'];
      $echange_hprim->type = "evenementsPatients";
      $echange_hprim->sous_type = "enregistrementPatient";
      $echange_hprim->message = $messagePatient;
      $echange_hprim->message_valide = 1;
    }
    $echange_hprim->date_production = mbDateTime();
    $echange_hprim->store();

    $newPatient = new CPatient();
    $newPatient->_hprim_initiator_id = $echange_hprim->_id;

    // Un événement concernant un patient appartient à l'une des six catégories suivantes
    // Enregistrement d'un patient
    if ($domGetEvenement instanceof CHPrimXMLEnregistrementPatient) {
      $data = array_merge($data, $domGetEvenement->getEnregistrementPatientXML());
      $messageAcquittement = $domGetEvenement->enregistrementPatient($domAcquittement, $echange_hprim, $newPatient, $data);
    } 
    // Fusion d'un patient
    else if($domGetEvenement instanceof CHPrimXMLFusionPatient) {
      $data = array_merge($data, $domGetEvenement->getFusionPatientXML());
      $messageAcquittement = $domGetEvenement->fusionPatient();
    } // Venue d'un patient
    else if($domGetEvenement instanceof CHPrimXMLVenuePatient) {
      $data = array_merge($data, $domGetEvenement->getVenuePatientXML());
      $messageAcquittement = $domGetEvenement->venuePatient($domAcquittement, $echange_hprim, $newPatient, $newSejour, $data);
    }
    // Aucun des six événements retour d'erreur
    else {
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E07"); 
    }
    
    return $messageAcquittement;
  }
  
  function calculatorAuth($operation, $entier1, $entier2) {
    $result = 0;
    if (($operation != "add") || ($operation != "subtract")) {
      return "Veuillez utiliser une méthode d'opération valable (add/subtract).";
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