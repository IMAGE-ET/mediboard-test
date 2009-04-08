<?php

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author SARL OpenXtrem
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
	  "E03" => "Les identifiants fournis sont incohérents. L'IPP renvoyé ne correspond pas à celui associé à l'identifiant source.",
	  "E04" => "Disconcordance entre l'identifiant source et l'identifiant cible.",
	  "E05" => "Identifiants source et cible non fournis.",
	  "E06" => "IPP non fourni."
	);

	static $codesAvertissementInformation = array(
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
			$echange_hprim->statut_acquittement = "erreur";
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
				$messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");

				$echange_hprim->message = $messagePatient;
				$echange_hprim->acquittement = utf8_decode($messageAcquittement);
				$echange_hprim->statut_acquittement = "erreur";
				$echange_hprim->store();
	    
				return $messageAcquittement;
			}
				
			// Identifiant source non fourni et identifiant cible fourni
			if (!$data['idSource'] && $data['idCible']) {
				$IPP = new CIdSante400();
				//Paramétrage de l'id 400
				$IPP->object_class = "CPatient";
				$IPP->tag = CAppUI::conf("mb_id");

				$IPP->id400 = str_pad($data['idCible'], 6, '0', STR_PAD_LEFT);

				// idCible connu
				if ($IPP->loadMatchingObject()) {
					$newPatient->load($IPP->object_id);
					
					if (!$domGetEvenement->checkSimilarPatient($newPatient, $data['patient'])) {
						$commentaire = "Le nom et/ou le prénom sont très différents. "; 
					}
					
					// Mapping du patient
          $newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
          $newPatient->_IPP = $IPP->id400;
          $msgPatient = $newPatient->store();
          $newPatient->loadLogs();

          $modified_fields = "";
          if ($newPatient->_ref_last_log) {
            foreach ($newPatient->_ref_last_log->_fields as $field) {
              $modified_fields .= "$field \n";
            }
          }	
          
          $codes = array ($msgPatient ? "A03" : "I02", "I03");
					if ($msgPatient) {
            $avertissement = $msgPatient." ";
          } else {
            $commentaire .= "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP associé : $IPP->id400.";
          }
     	  }
				// idCible non connu
				else {					
					if (is_numeric($IPP->id400) && (strlen($IPP->id400) <= 6)) {
						// Mapping du patient
            $newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
            $newPatient->_no_ipp = 1;
            $msgPatient = $newPatient->store();
            
						$IPP->object_id = $newPatient->_id;

	          $IPP->last_update = mbDateTime();
	          $msgIPP = $IPP->store();
	  	      
	          $newPatient->_IPP = $IPP->id400;
	          $newPatient->_no_ipp = 0;
	          $msgPatient = $newPatient->store();
                      
					  $codes = array ($msgPatient ? "A02" : "I01", $msgIPP ? "A05" : "A01");
		        if ($msgPatient) {
		          $avertissement = $msgPatient." ";
		        } else {
		          $commentaire = substr("Patient créé : $newPatient->_id. IPP créé : $IPP->id400.", 0, 4000);
		        }
          }
				}
				
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000));
          
        $echange_hprim->acquittement = utf8_decode($messageAcquittement);
        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
        $echange_hprim->store();
        
        return $messageAcquittement;
			}
				
			$id400 = new CIdSante400();
			//Paramétrage de l'id 400
			$id400->object_class = "CPatient";
			$id400->tag = $data['idClient'];
			$id400->id400 = $data['idSource'];

			// Cas 1 : Patient existe sur le SIP
			if($id400->loadMatchingObject()) {
				// Identifiant du patient sur le SIP
				$idPatientSIP = $id400->object_id;
				// Cas 1.1 : Pas d'identifiant cible
				if(!$data['idCible']) {
					if ($newPatient->load($idPatientSIP)) {
						// Mapping du patient
						$newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);

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
						 
						$codes = array ($msgPatient ? "A03" : "I02", $msgIPP ? "A05" : $_IPP_create ? "I06" : "I08");
						if ($msgPatient || $msgIPP) {
							$avertissement = $msgPatient." ".$msgIPP;
						} else {
							$commentaire = substr("Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP créé : $IPP->id400.", 0, 4000);
						}
						$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
						
		        $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
					}
				}
				// Cas 1.2 : Identifiant cible envoyé
				else {
					$IPP = new CIdSante400();
					//Paramétrage de l'id 400
					$IPP->object_class = "CPatient";
					$IPP->tag = CAppUI::conf("mb_id");

					$IPP->id400 = $data['idCible'];
          
					// Id cible connu
					if ($IPP->loadMatchingObject()) {
						// Acquittement d'erreur idSource et idCible incohérent
						if ($idPatientSIP != $IPP->object_id) {
							$commentaire = "L'identifiant source fait référence au patient : $idPatientSIP et l'identifiant cible au patient : $IPP->object_id.";
							$messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E04", $commentaire);

							$echange_hprim->acquittement = utf8_decode($messageAcquittement);
			        $echange_hprim->statut_acquittement = "erreur";
			        $echange_hprim->store();
			        
			        return $messageAcquittement;
						} else {
							$newPatient->load($IPP->object_id);

							// Mapping du patient
							$newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
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
								$avertissement = $msgPatient." ";
							} else {
								$commentaire = substr("Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.", 0, 4000);
							}
							$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $msgPatient ? "A03" : "I02", $avertissement ? $avertissement : $commentaire);
							
              $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
						}
					} 
					// Id cible non connu
					else {
						$commentaire = "L'identifiant source fait référence au patient : $idPatientSIP et l'identifiant cible n'est pas connu.";
            $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E03", $commentaire);

            $echange_hprim->statut_acquittement = "erreur";
            $echange_hprim->acquittement = $messageAcquittement;
				    $echange_hprim->date_echange = mbDateTime();
				    $echange_hprim->store();
    
            return $messageAcquittement; 
					}
				}
			}
			// Cas 2 : Patient n'existe pas sur le SIP
			else {
				// Mapping du patient
				$newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);

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
					 
					$_code_IPP = "I06";
				}

				$IPP->object_id = $newPatient->_id;

				$IPP->last_update = mbDateTime();
				$msgIPP = $IPP->store();

				$mutex->release();

				$newPatient->_IPP = $IPP->id400;
				$newPatient->_no_ipp = 0;
				$msgPatient = $newPatient->store();

				$codes = array ($msgPatient ? "A02" : "I01", $msgID400 ? "A04" : "I04", $msgIPP ? "A05" : $_code_IPP);
				if ($msgPatient || $msgID400 || $msgIPP) {
					$avertissement = $msgPatient." ".$msgID400." ".$msgIPP;
				} else {
					$commentaire = "Patient créé : $newPatient->_id. Identifiant externe créé : $id400Patient->id400. IPP créé : $IPP->id400.";
				}
				$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : $commentaire);
				
				$echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
			}
		} 
		// Si CIP
		else {
		  // Acquittement d'erreur : identifiants source et cible non fournis
      if (!$data['idSource']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E05");

        $echange_hprim->acquittement = utf8_decode($messageAcquittement);
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->date_echange = mbDateTime();
        $echange_hprim->store();
      
        return $messageAcquittement;
      }
      
			$IPP = new CIdSante400();
			//Paramétrage de l'id 400
			$IPP->object_class = "CPatient";
			$IPP->tag = $data['idClient'];
			$IPP->id400 = $data['idSource'];
      
			// idSource non connu
			if(!$IPP->loadMatchingObject()) {
				// idCible fourni
				if ($data['idCible']) {
			    if ($newPatient->load($data['idCible'])) {
			    	// Mapping du patient
            $newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
        
			    	// Evite de passer dans le sip handler
		        $newPatient->_coms_from_hprim = 1;
		        $msgPatient = $newPatient->store();
        
			    	$newPatient->loadLogs();
			      $modified_fields = "";
            if ($newPatient->_ref_last_log) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "I21";
            $_code_Patient = true; 
            $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields.";
			    } else {
			    	$_code_IPP = "I20";
			    }
				} else {
					$_code_IPP = "I22";  
				}
				
        if (!$newPatient->_id) {
	        if ($newPatient->loadMatchingPatient()) {
	        	// Mapping du patient
            $newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
        
            // Evite de passer dans le sip handler
            $newPatient->_coms_from_hprim = 1;
            $msgPatient = $newPatient->store();
        
            $newPatient->loadLogs();
            $modified_fields = "";
            if ($newPatient->_ref_last_log) {
              foreach ($newPatient->_ref_last_log->_fields as $field) {
                $modified_fields .= "$field \n";
              }
            }
            $_code_IPP = "A21";
            $_code_Patient = true; 
            $commentaire = "Patient modifiée : $newPatient->_id.  Les champs mis à jour sont les suivants : $modified_fields.";	          
	        }
        }
                
        if (!$newPatient->_id) {
        	// Mapping du patient
          $newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
        
          // Evite de passer dans le sip handler
          $newPatient->_coms_from_hprim = 1;
          $msgPatient = $newPatient->store();
          
          $commentaire = "Patient créé : $newPatient->_id. ";
        }
          
				$IPP->object_id = $newPatient->_id;
        $IPP->last_update = mbDateTime();
        $msgIPP = $IPP->store();
        
        $codes = array ($msgPatient ? ($_code_Patient ? "A03" : "A02") : ($_code_Patient ? "I02" : "I01"), $msgIPP ? "A05" : $_code_IPP);
        
        if ($msgPatient || $msgIPP) {
          $avertissement = $msgPatient." ".$msgIPP;
	      } else {
	        $commentaire .= "IPP créé : $IPP->id400.";
	      }
			} 
			// idSource connu
			else {
				$newPatient->load($IPP->object_id);
				// Mapping du patient
        $newPatient = $domGetEvenement->createPatient($data['patient'], $newPatient);
                        
        // idCible non fourni
        if (!$data['idCible']) {
          $_code_IPP = "I23"; 
        } else {
        	$tmpPatient = new CPatient();
        	// idCible connu
        	if ($tmpPatient->load($data['idCible'])) {
        		if ($tmpPatient->_id != $IPP->object_id) {
        			$commentaire = "L'identifiant source fait référence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
              $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E04", $commentaire);

              $echange_hprim->acquittement = utf8_decode($messageAcquittement);
              $echange_hprim->statut_acquittement = "erreur";
              $echange_hprim->store();
              
              return $messageAcquittement;
        		}
        		$_code_IPP = "I24"; 
        	}
        	// idCible non connu
        	else {
        		$_code_IPP = "A20";
        	}
        }
        // Evite de passer dans le sip handler
        $newPatient->_coms_from_hprim = 1;
        $msgPatient = $newPatient->store();
        
        $newPatient->loadLogs();
        $modified_fields = "";
        if ($newPatient->_ref_last_log) {
          foreach ($newPatient->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
        $codes = array ($msgPatient ? "A03" : "I02", $_code_IPP);
        
			  if ($msgPatient) {
          $avertissement = $msgPatient." ";
        } else {
          $commentaire = "Patient modifiée : $newPatient->_id. Les champs mis à jour sont les suivants : $modified_fields. IPP associé : $IPP->id400.";
        }
			}
			$messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000));	

			$echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
		}

		$echange_hprim->acquittement = $messageAcquittement;
		$echange_hprim->date_echange = mbDateTime();
		$echange_hprim->store();

		return $messageAcquittement;
	}
}
?>