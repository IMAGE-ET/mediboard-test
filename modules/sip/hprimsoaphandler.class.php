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
      "messagePatient" => "string")
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

	function evenementPatient($messagePatient) {
		global $m;
		
		// Création de l'échange
		$echange_hprim = new CEchangeHprim();

		// Gestion de l'acquittement
		$domAcquittement = new CHPrimXMLAcquittementsPatients();
    
		$hprimxmldoc = new CHPrimXMLDocument();
	  // Récupère le type de l'événement
    $type = $hprimxmldoc->getTypeEvenementPatient();
    // Un événement concernant un patient appartient à l'une des six catégories suivantes
    switch ($type) {
      case "enregistrementPatient" :
      	$domGetEvenement = new CHPrimXMLEnregistrementPatient();
        break;
      case "fusionPatient" :
      	$domGetEvenement = new CHPrimXMLFusionPatient();
        break;
      default;
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E07"); 
    }
    
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
    
    mbTrace($domGetEvenement, "DOM Evenement", true);
		// Un événement concernant un patient appartient à l'une des six catégories suivantes
		switch ($type) {
			case "enregistrementPatient" :
				$messageAcquittement = $domGetEvenement->enregistrementPatient($domGetEvenement, $domAcquittement, $echange_hprim, $newPatient, $data);
			  break;
			case "fusionPatient" :
				$messageAcquittement = $this->fusionPatient();
				break;
			default;
			  $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E07"); 
		}
		
		return $messageAcquittement;
	}
}
?>