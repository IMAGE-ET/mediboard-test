<?php

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
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

	static $codesErreur = array(
	  "E00" => "Erreur inattendue",
    "E01" => "L'émetteur du message n'est pas conforme avec l'établissement enregistré dans le SIP.",
	  "E02" => "La grammaire du message XML n'est pas respectée.",
	  "E03" => "Les identifiants fournis sont incohérents dans le SIP. L'IPP renvoyé ne correspond pas à celui associé à l'identifiant source.",
	  "ERR004" => "Disconcordance entre l'identifiant source et l'identifiant cible.",
	);

	static $codesAvertissementInformation = array(
    "A01" => "IPP envoyé non existant sur le SIP, attribution IPP forcée.",
    "A02" => "L'enregistrement du patient a échoué.",
    "A03" => "Modification du patient a échoué.",
    "A04" => "Création de l'id externe a échoué.",
    "A05" => "Création de l'IPP a échoué.",
    "A06" => "Modification de l'id externe a échoué.",

    "I01" => "L'enregistrement du patient est terminé.",
    "I02" => "Modification du patient terminée.",
    "I03" => "Identifiant source non fourni.",
    "I04" => "Identifiant source non connu.",
    "I05" => "Identifiant cible non connu.",
    "I06" => "Identifiant source mis à jour.",
	  "I07" => "Identifiant cible non fourni.",
	  "I08" => "Identifiant cible non fourni mais retrouvé.",
	  "I09" => "Identifiant cible fourni mais déjà utilisé.",
	  "I10" => "Identifiant source non fourni mais retrouvé.",
	);

	function evenementPatient($messagePatient) {
		global $m;

		// Traitement du message des erreurs
		$avertissement = $msgID400 = $msgIPP = "";

		// Création de l'échange
		$echange_hprim = new CEchangeHprim();

		// Gestion de l'acquittement
		$domAcquittement = new CHPrimXMLAcquittementsPatients();

		// Récupération des informations du message XML
		$domGetEvenement = new CHPrimXMLEvenementsPatients();
		$domGetEvenement->loadXML(utf8_decode($messagePatient));
		$doc_errors = $domGetEvenement->schemaValidate(null, true);

		// Acquittement d'erreur d'un document XML recu non valide
		if ($doc_errors !== true) {
			$domAcquittement->_identifiant = "inconnu";
			$domAcquittement->_destinataire = "inconnu";
			$domAcquittement->_destinataire_libelle = "inconnu document xml non valide";

			$messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E02", $doc_errors);

			$echange_hprim->date_production = mbDateTime();
			$echange_hprim->emetteur = "inconnu";
			$echange_hprim->destinataire = CAppUI::conf('mb_id');
			$echange_hprim->type = "evenementsPatients";
			$echange_hprim->message = $messagePatient;
			$echange_hprim->acquittement = utf8_decode($messageAcquittement);
			$echange_hprim->store();

			return $messageAcquittement;
		}

		$data = $domGetEvenement->getEvenementPatientXML();

		$domAcquittement->_identifiant = $data['identifiantMessage'];
		$domAcquittement->_destinataire = $data['idClient'];
		$domAcquittement->_destinataire_libelle = $data['libelleClient'];

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
		}
		$echange_hprim->date_production = mbDateTime();
		$echange_hprim->store();

		$newPatient = new CPatient();
		$newPatient->_hprim_initiator_id = $echange_hprim->_id;

		$mutex = new CMbSemaphore("sip-ipp");

		// Si SIP
		if (CAppUI::conf('sip server')) {
			// Acquittement d'erreur : identifiants source et cible non fournis
			if (!$data['idSource'] && !$data['idCible']) {
				$messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E02", "Identifiants source et cible non fournis");

				$echange_hprim->message = $messagePatient;
				$echange_hprim->acquittement = utf8_decode($messageAcquittement);
				$echange_hprim->store();
	    
				return $messageAcquittement;
			}
				
			// Identifiant source non fourni et identifiant cible non connu
			if (!$data['idSource']) {
				$IPP = new CIdSante400();
				//Paramétrage de l'id 400
				$IPP->object_class = "CPatient";
				$IPP->tag = CAppUI::conf("mb_id");

				$IPP->id400 = $data['idCible'];

				// idCible connu
				if ($IPP->loadMatchingObject()) {
					$newPatient->load($IPP->object_id);
					// Mapping du patient
					$newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
					$newPatient->_IPP = $IPP->id400;
					$msgPatient = $newPatient->store();
					$newPatient->loadLogs();

					$modified_fields = "";
					if ($newPatient->_ref_last_log) {
						foreach ($newPatient->_ref_last_log->_fields as $field) {
							$modified_fields .= "$field \n";
						}
					}		

					$_code_IPP = "I09";
				}
				// idCible non connu
				else {
					// RESTE A GERER CREATION PATIENT CLASSIQUE
				}
				$codes = array ($msgPatient ? "A02" : "I02", $msgIPP ? "A05" : $_code_IPP);
        if ($msgPatient) {
          $avertissement = $msgPatient."\n";
        } else {
          $commentaire = substr("Patient modifiée : $newPatient->_id.\n Les champs mis à jour sont les suivants : $modified_fields.", 0, 4000);
        }
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
          
				$echange_hprim->acquittement = $messageAcquittement;
		    $echange_hprim->date_echange = mbDateTime();
		    $echange_hprim->store();
		
		    return $messageAcquittement;
			}
				
			$id400 = new CIdSante400();
			//Paramétrage de l'id 400
			$id400->id400 = $data['idSource'];
			$id400->object_class = "CPatient";
			$id400->tag = $data['idClient'];

			// Cas 1 : Patient existe sur le SIP
			if($id400->loadMatchingObject()) {
				// Identifiant du patient sur le SIP
				$idPatientSIP = $id400->object_id;
				// Cas 1.1 : Pas d'identifiant cible
				if(!$data['idCible']) {
					// Le patient est connu sur le SIP
					if ($newPatient->load($idPatientSIP)) {
						// Mapping du patient
						$newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);

						// Création de l'IPP
						$IPP = new CIdSante400();
						//Paramétrage de l'id 400
						$IPP->object_class = "CPatient";
						$IPP->tag = CAppUI::conf("mb_id");
						$IPP->object_id = $idPatientSIP;

						$mutex->acquire();
						// Chargement du dernier IPP s'il existe
						if (!$IPP->loadMatchingObject("id400 DESC")) {
							// Incrementation de l'id400
							$IPP->id400++;
							$IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

							$IPP->_id = null;
							$IPP->last_update = mbDateTime();
							$msgIPP = $IPP->store();
								
							$_IPP_create = true;
						}
						$mutex->release();

						$newPatient->_IPP = $IPP->_id;
						$msgPatient = $newPatient->store();
						$newPatient->loadLogs();

						$modified_fields = "";
						if ($newPatient->_ref_last_log) {
							foreach ($newPatient->_ref_last_log->_fields as $field) {
								$modified_fields .= "$field \n";
							}
						}
						 
						$codes = array ($msgPatient ? "A02" : "I02", $msgIPP ? "A05" : $_IPP_create ? "I07" : "I08");
						if ($msgPatient || $msgIPP) {
							$avertissement = $msgPatient."\n".$msgIPP;
						} else {
							$commentaire = substr("Patient modifiée : $newPatient->_id.\n Les champs mis à jour sont les suivants : $modified_fields. IPP crée : $IPP->id400.", 0, 4000);
						}
						$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
					}
				}
				// Cas 1.2 : Identifiant cible envoyé
				else {
					$IPP = new CIdSante400();
					//Paramétrage de l'id 400
					$IPP->object_class = "CPatient";
					$IPP->tag = CAppUI::conf("mb_id");

					$IPP->id400 = $data['idCible'];

					if ($IPP->loadMatchingObject()) {
						// Acquittement d'erreur idSource et idCible incohérent
						if ($idPatientSIP != $IPP->object_id) {
							$commentaire = "L'identifiant source est fait référence au patient : $idPatientSIP et l'identifiant cible au paient : $IPP->object_id.";
							$messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E03", $commentaire);

							$echange_hprim->acquittement = $messageAcquittement;
							$echange_hprim->store();

							return $messageAcquittement;
						} else {
							$newPatient->load($IPP->object_id);

							// Mapping du patient
							$newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);
							$newPatient->_IPP = $IPP->id400;
							$msgPatient = $newPatient->store();
							$newPatient->loadLogs();
							 
							$modified_fields = "";
							if ($newPatient->_ref_last_log) {
								foreach ($newPatient->_ref_last_log->_fields as $field) {
									$modified_fields .= "$field \n";
								}
							}
							 
							if ($msgPatient) {
								$avertissement = $msgPatient."\n";
							} else {
								$commentaire = substr("Patient modifiée : $newPatient->_id.\n Les champs mis à jour sont les suivants : $modified_fields.", 0, 4000);
							}
							$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $msgPatient ? "A02" : "I02", $avertissement ? $avertissement : $commentaire);
						}
					}
				}
			}
			// Cas 2 : Patient n'existe pas sur le SIP
			else {
				// Mapping du patient
				$newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);

				$newPatient->_no_ipp = 1;
				$msgPatient = $newPatient->store();

				// Création de l'identifiant externe TAG CIP + idSource
				$id400Patient = new CIdSante400();
				//Paramétrage de l'id 400
				$id400Patient->object_class = "CPatient";
				$id400Patient->tag = $data['idClient'];

				$id400Patient->id400 = $data['idSource'];
        
				$id400Patient->object_id = $newPatient->_id;
				$id400Patient->_id = null;
				$id400Patient->last_update = mbDateTime();
				$msgID400 = $id400Patient->store();
				
				// Création de l'IPP
				$IPP = new CIdSante400();
				//Paramétrage de l'id 400
				$IPP->object_class = "CPatient";
				$IPP->tag = CAppUI::conf("mb_id");

				// Cas idCible fourni
				if ($data['idCible']) {
					$IPP->id400 = str_pad($data['idCible'], 6, '0', STR_PAD_LEFT);

					$mutex->acquire();
					 
					// idCible fourni non connu
					if (!$IPP->loadMatchingObject() && is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
						$_code_IPP = "A01";
					}
					// idCible fourni connu
					else {
						$IPP->id400 = null;
						$IPP->loadMatchingObject("id400 DESC");

						// Incrementation de l'id400
						$IPP->id400++;
						$IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);
						$IPP->_id = null;
						 
						$_code_IPP = "I09";
					}
				} else {
					$mutex->acquire();
					 
					// Chargement du dernier id externe de prescription du praticien s'il existe
					$IPP->loadMatchingObject("id400 DESC");

					// Incrementation de l'id400
					$IPP->id400++;
					$IPP->id400 = str_pad($IPP->id400, 6, '0', STR_PAD_LEFT);

					$IPP->_id = null;
					 
					$_code_IPP = "I07";
				}

				$IPP->object_id = $newPatient->_id;

				$IPP->last_update = mbDateTime();
				$msgIPP = $IPP->store();

				$mutex->release();

				$newPatient->_IPP = $IPP->id400;
				$newPatient->_no_ipp = 0;
				$msgPatient = $newPatient->store();

				$codes = array ($msgPatient ? "A02" : "I01", $msgID400 ? "AVT004" : "I04", $msgIPP ? "A05" : $_code_IPP);
				if ($msgPatient || $msgID400 || $msgIPP) {
					$avertissement = $msgPatient."\n".$msgID400."\n".$msgIPP;
				} else {
					$commentaire = "Patient crée : $newPatient->_id.\nIdentifiant externe crée : $id400Patient->id400.\nIPP crée : $IPP->id400.";
				}
				$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
			}
		} else {
			$IPP = new CIdSante400();
			//Paramétrage de l'id 400
			$IPP->object_class = "CPatient";
			$IPP->tag = $data['idClient'];
			$IPP->id400 = $data['idSource'];

			// Mapping du patient
			$newPatient = $data['xpath']->createPatient($data['patient'], $newPatient);

			// Evite de passer dans le sip handler
			$newPatient->_coms_from_hprim = 1;

			// Le SIP renvoi l'identifiant local du patient
			if($data['idCible']) {
				$tmpPatient = new CPatient();
				$tmpPatient->_id = $data['idCible'];
				$tmpPatient->load();

				if(($tmpPatient->nom == $newPatient->nom) &&
				($tmpPatient->prenom == $newPatient->prenom) &&
				($tmpPatient->naissance == $newPatient->naissance)) {
					$newPatient->_id = $data['idCible'];
				}
			}

			if(!$IPP->loadMatchingObject()) {
				$msgPatient = $newPatient->store();
				$IPP->object_id = $newPatient->_id;
			} else {
				$newPatient->_id = $IPP->object_id;
				$msgPatient = $newPatient->store();
			}
			$IPP->last_update = mbDateTime();
			$msgIPP = $IPP->store();

			$codes = array ($msgPatient ? "A02" : "I01", $msgIPP ? "A05" : "I07");
			if ($msgPatient || $msgIPP) {
				$avertissement = $msgPatient."\n".$msgIPP;
			} else {
				$commentaire = "Patient crée : $newPatient->_id.\nIPP crée : $IPP->id400.";
			}
			$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
		}

		$echange_hprim->acquittement = $messageAcquittement;
		$echange_hprim->date_echange = mbDateTime();
		$echange_hprim->store();

		return $messageAcquittement;
	}
}
?>