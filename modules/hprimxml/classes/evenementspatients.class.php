<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenements");

class CHPrimXMLEvenementsPatients extends CHPrimXMLEvenements {
  static $evenements = array(
    'enregistrementPatient' => "CHPrimXMLEnregistrementPatient",
    'fusionPatient'         => "CHPrimXMLFusionPatient",
    'venuePatient'          => "CHPrimXMLVenuePatient",
    'fusionVenue'           => "CHPrimXMLFusionVenue",
    'mouvementPatient'      => "CHPrimXMLMouvementPatient",
    'debiteursVenue'        => "CHPrimXMLDebiteursVenue"
  );
    
  static function getVersionEvenementsPatients() {    
    return "msgEvenementsPatients".str_replace(".", "", CAppUI::conf('hprimxml evt_patients version'));
  } 
  
  static function getHPrimXMLEvenementsPatients($messagePatient) {
    $hprimxmldoc = new CHPrimXMLDocument("patient", self::getVersionEvenementsPatients());
    // Récupération des informations du message XML
    $hprimxmldoc->loadXML(utf8_decode($messagePatient));
    
    $type = $hprimxmldoc->getTypeEvenementPatient();

    if ($type) {
      return new self::$evenements[$type];
    } else {
      return new CHPrimXMLEvenementsPatients();
    }
  }  
   
  function __construct() {
    $this->evenement = "evt_patients";
    $this->type = "patients";
                
    parent::__construct("patients", self::getVersionEvenementsPatients());
  }

  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsPatients", false);
  }
  
  static function getActionEvenement($query, $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryAttributNode($query, $node, "action");    
  }
    
  function isActionValide($action, $domAcquittement, $echange_hprim) {
    $messageAcquittement = null;
    
    if (array_key_exists($action, $this->actions)) {
      return $messageAcquittement;
    }
    
    $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E008");
    $doc_valid = $domAcquittement->schemaValidate();
    $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->statut_acquittement = "erreur";
    $echange_hprim->store();
    
    return $messageAcquittement;
  }
  
  function mappingPatient($node, CPatient $mbPatient) {   
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $mbPatient;
  }
  
  static function getPersonnePhysique($node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);

    // Création de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $node);
    
    $sexe = $xpath->queryAttributNode("hprim:personnePhysique", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );
    $mbPatient->sexe = $sexeConversion[$sexe];
    
    // Récupération du typePersonne
    $mbPatient = self::getPersonne($personnePhysique,$mbPatient);
    
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $xpath->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance = $xpath->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $xpath->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance = $xpath->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }
  
  static function getPersonne($node, CMbObject $mbPersonne) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $civilite = $xpath->queryAttributNode("hprim:civiliteHprim", $node, "valeur");
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "mlle"  => "mlle",
      "mr"    => "m",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $node);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $node);
    $adresses = $xpath->queryUniqueNode("hprim:adresses", $node);
    $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
    $ligne = $xpath->getMultipleTextNodes("hprim:ligne", $adresse, true);
    $ville = $xpath->queryTextNode("hprim:ville", $adresse);
    $cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
    $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $node);
    $email = $xpath->getFirstNode("hprim:emails/*", $node);    
    
    if ($mbPersonne instanceof CPatient) {
      if ($civilite) {
        $mbPersonne->civilite = $civiliteHprimConversion[$civilite]; 
      } else {
        $mbPersonne->civilite = "guess";
      }
      $mbPersonne->nom = $nom;
      $mbPersonne->_nom_naissance = $xpath->queryTextNode("hprim:nomNaissance", $node);
      $mbPersonne->prenom = $prenoms[0];
      $mbPersonne->prenom_2 = isset($prenoms[1]) ? $prenoms[1] : null;
      $mbPersonne->prenom_3 = isset($prenoms[2]) ? $prenoms[2] : null;
      $mbPersonne->adresse  = $ligne;
      $mbPersonne->ville = $ville;
      $mbPersonne->pays_insee = $xpath->queryTextNode("hprim:pays", $adresse);
      $pays = new CPaysInsee();
      $pays->numerique = $mbPersonne->pays_insee;
      $pays->loadMatchingObject();
      $mbPersonne->pays = $pays->nom_fr;
      $mbPersonne->cp = $cp;
      $mbPersonne->tel = isset($telephones[0]) ? $telephones[0] : null;
      $mbPersonne->tel2 = isset($telephones[1]) ? $telephones[1] : null;
      $mbPersonne->email = $email;
    } elseif ($mbPersonne instanceof CMediusers) {
      $mbPersonne->_user_last_name  = $nom;
      $mbPersonne->_user_first_name = $prenoms[0];
      $mbPersonne->_user_email      = $email;
      $mbPersonne->_user_phone      = isset($telephones[0]) ? $telephones[0] : null;
      $mbPersonne->_user_adresse    = $ligne;
      $mbPersonne->_user_cp         = $cp;
      $mbPersonne->_user_ville      = $ville;
    }
    
    return $mbPersonne;
  }
  
  static function getActiviteSocioProfessionnelle($node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $activiteSocioProfessionnelle = $xpath->queryTextNode("hprim:activiteSocioProfessionnelle", $node);
    
    $mbPatient->profession = $activiteSocioProfessionnelle ? $activiteSocioProfessionnelle : null; 
    
    return $mbPatient;
  }
  
  static function getPersonnesPrevenir($node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $personnesPrevenir = $xpath->query("hprim:personnesPrevenir/*", $node);
    foreach ($personnesPrevenir as $personnePrevenir) {
      $mbPatient->prevenir_nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePrevenir);
      $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePrevenir);
      $mbPatient->prevenir_prenom = $prenoms[0];
      
      $adresses = $xpath->queryUniqueNode("hprim:adresses", $personnePrevenir);
      $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
      $mbPatient->prevenir_adresse = $xpath->queryTextNode("hprim:ligne", $adresse);
      $mbPatient->prevenir_ville = $xpath->queryTextNode("hprim:ville", $adresse);
      $mbPatient->prevenir_cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
      
      $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $personnePrevenir);
      $mbPatient->prevenir_tel = isset($telephones[0]) ? $telephones[0] : null;
    }
        
    return $mbPatient;
  }
  
  function checkSimilarPatient(CPatient $mbPatient, $xmlPatient) {
    $xpath = new CHPrimXPath($this);
        
    // Création de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom = $prenoms[0];
    
    return $mbPatient->checkSimilar($nom, $prenom);
  }
  
  function getIdSourceObject($query_evt, $query_type) { 
    $xpath = new CHPrimXPath($this);
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $typeEvenement = $xpath->queryUniqueNode($query_evt, $evenementPatient);
    
    $object = $xpath->queryUniqueNode($query_type, $typeEvenement);

    return $this->getIdSource($object);
  }
  
  function mappingVenue($node, CSejour $mbVenue, $cancel = false) { 
    // Si annulation
    if ($cancel) {
      $mbVenue->annule = 1;
      
      return $mbVenue;
    } 
    
    $mbVenue = $this->getNatureVenue($node, $mbVenue);
    $mbVenue = self::getEntree($node, $mbVenue);
    $mbVenue = $this->getMedecins($node, $mbVenue);
    $mbVenue = self::getPlacement($node, $mbVenue);
    $mbVenue = self::getSortie($node, $mbVenue);

    /* TODO Supprimer ceci après l'ajout des times picker */
    $mbVenue->_hour_entree_prevue = null;
    $mbVenue->_min_entree_prevue = null;
    $mbVenue->_hour_sortie_prevue = null;
    $mbVenue->_min_sortie_prevue = null;
    
    return $mbVenue;
  }
  
  static function getAttributesVenue($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
        
    $attributes = array();
    $attributes['confidentiel'] = $xpath->getValueAttributNode($node, "confidentiel"); 
    $attributes['etat'] = $xpath->getValueAttributNode($node, "etat"); 
    $attributes['facturable'] = $xpath->getValueAttributNode($node, "facturable"); 
    $attributes['declarationMedecinTraitant'] = $xpath->getValueAttributNode($node, "declarationMedecinTraitant"); 
    
    return $attributes;
  }
  
  static function getEtatVenue($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->getValueAttributNode($node, "etat"); 
  }
  
  function getNatureVenue($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $destinataire = $this->_ref_echange_hprim->_ref_emetteur;
    $destinataire->loadConfigValues();

    // Obligatoire pour MB
    $nature = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur", "", false);
    $attrNatureVenueHprim = array (
      "hsp"  => "comp",
      "cslt" => "consult",
      "sc"   => "seances",
    );
    
    if (!$mbVenue->type) {
	    if ($nature) {
	      $mbVenue->type = $attrNatureVenueHprim[$nature];
	    }
    }  
    
    // Détermine le type de venue depuis la config des numéros de dossier 
    $mbVenue->type = self::getVenueType($destinataire, $mbVenue->_num_dossier);
    if (!$mbVenue->type) {
      $mbVenue->type = "comp";
    }
    
    return $mbVenue;
  }
  
  static function getVenueType($destinataire, $num_dossier) {
    $types = array(
      "type_sej_hospi"   => "comp",
      "type_sej_ambu"    => "ambu",
      "type_sej_urg"     => "urg",
      "type_sej_scanner" => "seances",
      "type_sej_chimio"  => "seances",
      "type_sej_dialyse" => "seances",
    );
    
    foreach($types as $config => $type) {
      if (!$destinataire->_configs[$config]) continue;
      
      if (preg_match($destinataire->_configs[$config], $num_dossier)) {
        return $type;
      }
    }
  }
  
  static function getEntree($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $entree = $xpath->queryUniqueNode("hprim:entree", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $entree);
    $heure = mbTransformTime($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $entree), null , "%H:%M:%S");
    $modeEntree = $xpath->queryAttributNode("hprim:modeEntree", $entree, "valeur");
    
    $dateHeure = "$date $heure";

  	if($mbVenue->entree_reelle) {
  		$mbVenue->entree_reelle = $dateHeure;
		} else {
			$mbVenue->entree_prevue = $dateHeure;
		}
       
    return $mbVenue;
  }
  
  static function isVenuePraticien($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $medecins = $xpath->queryUniqueNode("hprim:medecins", $node);
    
    if (!is_array($medecins)) {
      return false;
    }
    
    $medecin = $medecins->childNodes;
    foreach ($medecin as $_med) {
      $lien = $xpath->getValueAttributNode($_med, "lien");
      if ($lien != "rsp") {
        return false;
      }
    }
    
    return true;
  }  
  
  function getMedecins($node, CSejour $mbVenue) {    
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $medecins = $xpath->queryUniqueNode("hprim:medecins", $node);
    if (is_array($medecins)) {
      $medecin = $medecins->childNodes;
      foreach ($medecin as $_med) {
     		$mediuser_id = $this->getMedecin($_med);
                
        $lien = $xpath->getValueAttributNode($_med, "lien");
        if ($lien == "rsp") {
          $mbVenue->praticien_id = $mediuser_id;
        }
      } 
    }
    
    // Dans le cas ou la venue ne contient pas de medecin responsable
    // Attribution d'un medecin indeterminé
    if (!$mbVenue->praticien_id) {
      $user = new CUser();
      $mediuser = new CMediusers();
      $user->user_last_name = CAppUI::conf("hprimxml medecinIndetermine");
      if (!$user->loadMatchingObject()) {
        $mediuser->_user_last_name = $user->user_last_name;
        $mediuser->_id = $this->createPraticien($mediuser);
      } else {
        $user->loadRefMediuser();
        $mediuser = $user->_ref_mediuser;
      }
      $mbVenue->praticien_id = $mediuser->_id;
    }
    
    return $mbVenue;
  }
  
  function getMedecin($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $code = $xpath->queryTextNode("hprim:identification/hprim:code", $node);
    $mediuser = new CMediusers();
    $id400 = new CIdSante400();
    $id400->object_class = "CMediusers";
    $id400->tag = $this->getTagMediuser();
    $id400->id400 = $code;
    if ($id400->loadMatchingObject()) {
      $mediuser->_id = $id400->object_id;
    } else {
      // Récupération du typePersonne
      // Obligatoire pour MB
      $personne =  $xpath->queryUniqueNode("hprim:personne", $node, false);
      $mediuser = self::getPersonne($personne, $mediuser);
      
      $mediuser->_id = $this->createPraticien($mediuser);
      
      $id400->object_id = $mediuser->_id;
      $id400->last_update = mbDateTime();
      $id400->store(); 
    }
    
    return $mediuser->_id;
  }
  
  function createPraticien(CMediusers $mediuser) {
    $functions = new CFunctions();
    $functions->text = CAppUI::conf("hprimxml functionPratImport");
    $functions->group_id = CGroups::loadCurrent()->_id;
    $functions->loadMatchingObject();
    if (!$functions->loadMatchingObject()) {
      $functions->type = "cabinet";
      $functions->compta_partagee = 0;
      $functions->store();
    }
    $mediuser->function_id = $functions->_id;
    $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name);
    $mediuser->_user_type = 13; // Medecin
    $mediuser->actif = CAppUI::conf("hprimxml medecinActif") ? 1 : 0; 
    
    $user = new CUser();
    $user->user_last_name = $mediuser->_user_last_name;
    $user->user_first_name  = $mediuser->_user_first_name;
    $listPrat = $user->seek("$user->user_last_name $user->user_first_name");
    if (count($listPrat) == 1) {
      $user = reset($listPrat);
      $user->loadRefMediuser();
      $mediuser = $user->_ref_mediuser;
    } else {
      $mediuser->store();
    }
  	
  	return $mediuser->_id;
  }

  static function getPlacement($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $placement = $xpath->queryUniqueNode("hprim:Placement", $node);
    
    if ($placement) {
      $mbVenue->modalite = $xpath->queryAttributNode("hprim:modePlacement", $placement, "modaliteHospitalisation");
    }
    
    return $mbVenue;
  }
  
  static function getSortie($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $sortie = $xpath->queryUniqueNode("hprim:sortie", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $sortie);
    $heure = mbTransformTime($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $sortie), null , "%H:%M:%S");
		if ($date) {
			$dateHeure = "$date $heure";
		}
    elseif (!$date && !$mbVenue->sortie_prevue) {
      $dateHeure = mbAddDateTime(CAppUI::conf("dPplanningOp CSejour sortie_prevue ".$mbVenue->type).":00:00", $mbVenue->entree_reelle ? $mbVenue->entree_reelle : $mbVenue->entree_prevue);
    } 
    else {
    	$dateHeure = $mbVenue->sortie_reelle ? $mbVenue->sortie_reelle : $mbVenue->sortie_prevue;
    }

  	if($mbVenue->sortie_reelle && CAppUI::conf("hprimxml notifier_sortie_reelle")) {
  		$mbVenue->sortie_reelle = $dateHeure;
		} else {
			$mbVenue->sortie_prevue = $dateHeure;
		}
    
    $modeSortieHprim = $xpath->queryAttributNode("hprim:modeSortieHprim", $sortie, "valeur");
    if ($modeSortieHprim) {
      // décès
      if ($modeSortieHprim == "05") {
        $mbVenue->mode_sortie = "deces";
      } 
      // autre transfert dans un autre CH
      else if ($modeSortieHprim == "02") {
        $mbVenue->mode_sortie = "transfert";
        
        $destination = $xpath->queryUniqueNode("hprim:destination", $sortie);
        if ($destination) {
          $mbVenue = $this->getEtablissementTransfert($destination, $mbVenue);
        }
      } 
      //retour au domicile
      else {
        $mbVenue->mode_sortie = "normal";
      }
    }
    
    return $mbVenue;
  }
  
  function getEtablissementTransfert($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $code = $xpath->queryUniqueNode("hprim:code", $node);
    
    $etabExterne = new CEtabExterne();
    
    
    return $mbVenue->etablissement_transfert_id;
  }
  
  function mappingMouvements($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    /* @FIXME Penser a parcourir tous les mouvements par la suite */
    $mouvement = $xpath->queryUniqueNode("hprim:mouvement", $node);

    if (!CAppUI::conf("hprimxml mvtComplet")) {
      $mbVenue = $this->getMedecinResponsable($mouvement, $mbVenue);
    }
    
    return $mbVenue;
  } 
  
  function getMedecinResponsable($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);    
    
    $medecinResponsable = $xpath->queryUniqueNode("hprim:medecinResponsable", $node);
    
    if ($medecinResponsable) {
      $mbVenue->praticien_id = $this->getMedecin($medecinResponsable);
    }
    
    return $mbVenue;
  }
  
  function mappingDebiteurs($node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    /* @FIXME Penser a parcourir tous les debiteurs par la suite */
    $debiteur = $xpath->queryUniqueNode("hprim:debiteur", $node);

    $mbPatient = $this->getAssurance($debiteur, $mbPatient);
     
    return $mbPatient;
  }
  
  static function getAssurance($node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);  
    
    $assurance = $xpath->queryUniqueNode("hprim:assurance", $node);
    
    // Obligatoire pour MB
    $assure = $xpath->queryUniqueNode("hprim:assure", $assurance, false);
    $mbPatient = self::getAssure($assure, $mbPatient);
    
    $dates = $xpath->queryUniqueNode("hprim:dates", $assurance);
    $mbPatient->deb_amo = $xpath->queryTextNode("hprim:dateDebutDroit", $dates);
    $mbPatient->fin_amo = $xpath->queryTextNode("hprim:dateFinDroit", $dates);
    
    $obligatoire = $xpath->queryUniqueNode("hprim:obligatoire", $assurance);
    $mbPatient->code_regime = $xpath->queryTextNode("hprim:grandRegime", $obligatoire);
    $mbPatient->caisse_gest = $xpath->queryTextNode("hprim:caisseAffiliation", $obligatoire);
    $mbPatient->centre_gest = $xpath->queryTextNode("hprim:centrePaiement", $obligatoire);
    
    return $mbPatient;
  }
  
  static function getAssure($node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);  
    
    $immatriculation = $xpath->queryTextNode("hprim:immatriculation", $node);
    $mbPatient->matricule = $immatriculation;
    $mbPatient->assure_matricule = $immatriculation;
    
    $personne = $xpath->queryUniqueNode("hprim:personne", $node);
    if ($personne) {
      $sexe = $xpath->queryAttributNode("hprim:personne", $node, "sexe");
      $sexeConversion = array (
          "M" => "m",
          "F" => "f",
      );
      
      $mbPatient->assure_sexe = $sexeConversion[$sexe];
      $mbPatient->assure_nom = $xpath->queryTextNode("hprim:nomUsuel", $personne);
      $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personne);
      $mbPatient->assure_prenom = $prenoms[0];
      $mbPatient->assure_prenom_2 = isset($prenoms[1]) ? $prenoms[1] : null;
      $mbPatient->assure_prenom_3 = isset($prenoms[2]) ? $prenoms[2] : null;
      $mbPatient->assure_naissance = $xpath->queryTextNode("hprim:naissance", $personne);
      $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personne);
      $mbPatient->assure_naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
      $mbPatient->rang_beneficiaire = $xpath->queryTextNode("hprim:lienAssure", $node);
      $mbPatient->qual_beneficiaire = CValue::read(CPatient::$rangToQualBenef, $mbPatient->rang_beneficiaire);
    }
    
    return $mbPatient;
  }
  
  function doNotCancelVenue($venue, $domAcquittement, &$echange_hprim) {
    // Impossible d'annuler un séjour en cours 
    if ($venue->entree_reelle) {
      $commentaire = "La venue $venue->_id que vous souhaitez annuler est impossible.";
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E108", $commentaire);
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
      return $messageAcquittement;    
    }
    
    // Impossible d'annuler un dossier ayant une intervention
    $where = array();
    $where['annulee'] = " = '0'";
    $venue->loadRefsOperations($where);
    if (count($venue->_ref_operations) > 0) {
      $commentaire = "La venue $venue->_id que vous souhaitez annuler est impossible.";
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E109", $commentaire);
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
      return $messageAcquittement;    
    }  
  }
}
?>