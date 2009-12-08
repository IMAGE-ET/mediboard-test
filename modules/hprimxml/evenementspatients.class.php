<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsPatients extends CHPrimXMLDocument {
  static $evenements = array(
    'enregistrementPatient' => "CHPrimXMLEnregistrementPatient",
    'fusionPatient'         => "CHPrimXMLFusionPatient",
    'venuePatient'          => "CHPrimXMLVenuePatient",
    'fusionVenue'           => "CHPrimXMLFusionVenue",
    'mouvementPatient'      => "CHPrimXMLMouvementPatient",
    'debiteursVenue'        => "CHPrimXMLDebiteursVenue"
  );
  
  static function getVersionEvenementsPatients() {
    $version = CAppUI::conf('hprimxml evt_patients version');

    return ($version == "1.051") ? "msgEvenementsPatients1051" : "msgEvenementsPatients105";
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
    $this->destinataire_libelle = "";
    $this->type = "patients";
    
                
    parent::__construct("patients", self::getVersionEvenementsPatients());
  }

  function generateEnteteMessageEvenementsPatients() {
    $evenementsPatients = $this->addElement($this, "evenementsPatients", null, "http://www.hprim.org/hprimXML");
    // Retourne un message d'acquittement par le récepteur
    $this->addAttribute($evenementsPatients, "acquittementAttendu", "oui");
    
    $this->addEnteteMessage($evenementsPatients);
  }
  
  function getEvenementPatientXML() { 
    $xpath = new CMbXPath($this, true);
    
    $data = array();
    $data['acquittement'] = $xpath->queryAttributNode("/hprim:evenementsPatients", null, "acquittementAttendu");

    $query = "/hprim:evenementsPatients/hprim:enteteMessage";
    $entete = $xpath->queryUniqueNode($query);

    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    return $data;
  }
  
  static function getActionEvenement($query, $node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    return $xpath->queryAttributNode($query, $node, "action");    
  }
  
  function getIdSource($node) {
    $xpath = new CMbXPath($this, true);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    // Obligatoire pour MB
    $emetteur = $xpath->queryUniqueNode("hprim:emetteur", $identifiant, false);

    return $xpath->queryTextNode("hprim:valeur", $emetteur);
  }
  
  function getIdCible($node) {
    $xpath = new CMbXPath($this, true);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $recepteur = $xpath->queryUniqueNode("hprim:recepteur", $identifiant);
    
    return $xpath->queryTextNode("hprim:valeur", $recepteur);
  }
  
  function mappingPatient($node, CPatient $mbPatient) {    
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $mbPatient;
  }
  
  static function getPersonnePhysique($node, CPatient $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);

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
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $civilite = $xpath->queryAttributNode("hprim:civiliteHprim", $node, "valeur");
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "mlle"  => "melle",
      "mr"    => "m",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $node);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $node);
    $adresses = $xpath->queryUniqueNode("hprim:adresses", $node);
    $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
    $ligne = $xpath->getMultipleTextNodes("hprim:ligne", $adresse);
    $ville = $xpath->queryTextNode("hprim:ville", $adresse);
    $cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
    $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $node);
    $emails = $xpath->getMultipleTextNodes("hprim:emails/*", $node);    
    
    if ($mbPersonne instanceof CPatient) {
      if ($civilite) {
        $mbPersonne->civilite = $civiliteHprimConversion[$civilite]; 
      } else {
        $mbPersonne->civilite = "guess";
      }
      $mbPersonne->nom = $nom;
      $mbPersonne->_nom_naissance = $xpath->queryTextNode("hprim:nomNaissance", $node);
      $mbPersonne->prenom = $prenoms[0];
      $mbPersonne->prenom_2 = isset($prenoms[1]) ? $prenoms[1] : "";
      $mbPersonne->prenom_3 = isset($prenoms[2]) ? $prenoms[2] : "";
      $mbPersonne->adresse  = $ligne[0];
      if (isset($ligne[1]))
        $mbPersonne->adresse .= " $ligne[1]";
      if (isset($ligne[2]))
        $mbPersonne->adresse .= " $ligne[2]";
      $mbPersonne->ville = $ville;
      $mbPersonne->pays_insee = $xpath->queryTextNode("hprim:pays", $adresse);
      $pays = new CPaysInsee();
      $pays->numerique = $mbPersonne->pays_insee;
      $pays->loadMatchingObject();
      $mbPersonne->pays = $pays->nom_fr;
      $mbPersonne->cp = $cp;
      $mbPersonne->tel = isset($telephones[0]) ? $telephones[0] : "";
      $mbPersonne->tel2 = isset($telephones[1]) ? $telephones[1] : "";
      $mbPersonne->email = isset($emails[0]) ? $emails[0] : "";
    } elseif ($mbPersonne instanceof CMediusers) {
      $mbPersonne->_user_last_name  = $nom;
      $mbPersonne->_user_first_name = $prenoms[0];
      $mbPersonne->_user_email      = isset($emails[0]) ? $emails[0] : "";
      $mbPersonne->_user_phone      = isset($telephones[0]) ? $telephones[0] : "";
      if (is_array($ligne)) {
        $mbPersonne->_user_adresse    = $ligne[0];
        if (isset($ligne[1]))
          $mbPersonne->_user_adresse  .= " $ligne[1]";
        if (isset($ligne[2]))
          $mbPersonne->_user_adresse  .= " $ligne[2]";
      }
      $mbPersonne->_user_cp         = $cp;
      $mbPersonne->_user_ville      = $ville;
    }
    
    return $mbPersonne;
  }
  
  static function getActiviteSocioProfessionnelle($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $mbPatient->profession = $xpath->queryTextNode("hprim:activiteSocioProfessionnelle", $node); 
    
    return $mbPatient;
  }
  
  static function getPersonnesPrevenir($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
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
      $mbPatient->prevenir_tel = isset($telephones[0]) ? $telephones[0] : "";
    }
        
    return $mbPatient;
  }
  
  function checkSimilarPatient($mbPatient, $xmlPatient) {
    $xpath = new CMbXPath($this, true);
        
    // Création de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom = $prenoms[0];
    
    return $mbPatient->checkSimilar($nom, $prenom);
  }
  
  function getIdSourceObject($query_evt, $query_type) { 
    $xpath = new CMbXPath($this, true);
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $typeEvenement = $xpath->queryUniqueNode($query_evt, $evenementPatient);
    
    $object = $xpath->queryUniqueNode($query_type, $typeEvenement);

    return $this->getIdSource($object);
  }
  
  function mappingVenue($node, $mbVenue) {  
    $mbVenue = self::getNatureVenue($node, $mbVenue);
    $mbVenue = self::getEntree($node, $mbVenue);
    $mbVenue = $this->getMedecins($node, $mbVenue);
    $mbVenue = self::getPlacement($node, $mbVenue);
    $mbVenue = self::getSortie($node, $mbVenue);

    return $mbVenue;
  }
  
  static function getAttributesVenue($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
        
    $attributes = array();
    $attributes['confidentiel'] = $xpath->getValueAttributNode($node, "confidentiel"); 
    $attributes['etat'] = $xpath->getValueAttributNode($node, "etat"); 
    $attributes['facturable'] = $xpath->getValueAttributNode($node, "facturable"); 
    $attributes['declarationMedecinTraitant'] = $xpath->getValueAttributNode($node, "declarationMedecinTraitant"); 
    
    return $attributes;
  }
  
  static function getEtatVenue($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    return $xpath->getValueAttributNode($node, "etat"); 
  }
  
  static function getNatureVenue($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    // Obligatoire pour MB
    $nature = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur", "", false);
    $attrNatureVenueHprim = array (
      "hsp"  => "comp",
      "cslt" => "consult",
      "sc"   => "seances",
    );
    if ($nature) {
      $mbVenue->type =  $attrNatureVenueHprim[$nature];
    } else {
      $mbVenue->type = "seances";
    }
      
    return $mbVenue;
  }
  
  static function getEntree($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $entree = $xpath->queryUniqueNode("hprim:entree", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $entree);
    $heure = mbTransformTime($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $entree), null , "%H:%M:%S");
    $modeEntree = $xpath->queryAttributNode("hprim:modeEntree", $entree, "valeur");
    
    $dateHeure = "$date $heure";
    
    $etat = self::getEtatVenue($node);
    if ($etat == "préadmission") {
      $mbVenue->entree_prevue = $dateHeure;
    } 
    if (($etat == "encours") || ($etat == "clôturée")) {
      if (!$mbVenue->_id) {
        $mbVenue->entree_prevue = $dateHeure;
      }
      $mbVenue->entree_reelle = $dateHeure;
    }
       
    return $mbVenue;
  }
  
  static function isVenuePraticien($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
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
  
  function getMedecins($node, $mbVenue) {    
    $xpath = new CMbXPath($node->ownerDocument, true);
    
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
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $code = $xpath->queryTextNode("hprim:identification/hprim:code", $node);
    $mediuser = new CMediusers();
    $id400 = new CIdSante400();
    //Paramétrage de l'id 400
    $id400->object_class = "CMediusers";
    $id400->tag = $this->destinataire;
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
  
  function createPraticien($mediuser) {
    $functions = new CFunctions();
    $functions->text = CAppUI::conf("hprimxml functionPratImport");
    $functions->loadMatchingObject();
    if (!$functions->loadMatchingObject()) {
      $functions->group_id = CGroups::loadCurrent()->_id;
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
    $user->user_username  = $mediuser->_user_username;
    if (!$user->loadMatchingObject()) {
      $mediuser->store();
    }
  	
  	return $mediuser->_id;
  }

  static function getPlacement($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $placement = $xpath->queryUniqueNode("hprim:Placement", $node);
    
    if ($placement) {
      $mbVenue->modalite = $xpath->queryAttributNode("hprim:modePlacement", $placement, "modaliteHospitalisation");
    }
    
    return $mbVenue;
  }
  
  static function getSortie($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $sortie = $xpath->queryUniqueNode("hprim:sortie", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $sortie);
    $heure = mbTransformTime($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $sortie), null , "%H:%M:%S");
    
    $dateHeure = "$date $heure";
    
    if (!$date) {
      $dateHeure = mbAddDateTime(CAppUI::conf("dPplanningOp CSejour sortie_prevue ".$mbVenue->type), $mbVenue->entree_reelle ? $mbVenue->entree_reelle : $mbVenue->entree_prevue);
    }
    
    $etat = self::getEtatVenue($node);
    if (($etat == "préadmission") || ($etat == "encours")) {
      $mbVenue->sortie_prevue = $dateHeure;
    }
    if ($etat == "clôturée") {
      if (!$mbVenue->_id) {
        $mbVenue->sortie_prevue = $dateHeure;
      }
      $mbVenue->sortie_reelle = $dateHeure;
    } 

    return $mbVenue;
  }
  
  function mappingMouvements($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    // Penser a parcourir tous les mouvements par la suite
    $mouvement = $xpath->queryUniqueNode("hprim:mouvement", $node);

    if (!CAppUI::conf("hprimxml mvtComplet")) {
      $mbVenue = $this->getMedecinResponsable($mouvement, $mbVenue);
    }
    
    return $mbVenue;
  } 
  
  function getMedecinResponsable($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);    
    
    $medecinResponsable = $xpath->queryUniqueNode("hprim:medecinResponsable", $node);
    
    if ($medecinResponsable) {
      $mbVenue->praticien_id = $this->getMedecin($medecinResponsable);
    }
    
    return $mbVenue;
  }
  
  function mappingDebiteurs($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    // Penser a parcourir tous les debiteurs par la suite
    $debiteur = $xpath->queryUniqueNode("hprim:debiteur", $node);

    $mbPatient = $this->getAssurance($debiteur, $mbPatient);
     
    return $mbPatient;
  }
  
  static function getAssurance($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);  
    
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
  
  static function getAssure($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);  
    
    $mbPatient->matricule = $xpath->queryTextNode("hprim:immatriculation", $node);
    
    // Obligatoire pour MB    
    $personne = $xpath->queryUniqueNode("hprim:personne", $node, false);
    
    $sexe = $xpath->queryAttributNode("hprim:personne", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );
    $mbPatient->assure_sexe = $sexeConversion[$sexe];
    $mbPatient->assure_nom = $xpath->queryTextNode("hprim:nomUsuel", $personne);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personne);
    $mbPatient->assure_prenom = $prenoms[0];
    $mbPatient->assure_prenom_2 = isset($prenoms[1]) ? $prenoms[1] : "";
    $mbPatient->assure_prenom_3 = isset($prenoms[2]) ? $prenoms[2] : "";
    $mbPatient->assure_naissance = $xpath->queryTextNode("hprim:naissance", $personne);
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personne);
    $mbPatient->assure_naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $mbPatient->rang_beneficiaire = $xpath->queryTextNode("hprim:lienAssure", $node);
    
    return $mbPatient;
  }
}
?>