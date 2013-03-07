<?php

/**
 * Patients
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLEvenementsPatients
 * Patients
 */

class CHPrimXMLEvenementsPatients extends CHPrimXMLEvenements {
  static $evenements = array(
    'enregistrementPatient' => "CHPrimXMLEnregistrementPatient",
    'fusionPatient'         => "CHPrimXMLFusionPatient",
    'venuePatient'          => "CHPrimXMLVenuePatient",
    'fusionVenue'           => "CHPrimXMLFusionVenue",
    'mouvementPatient'      => "CHPrimXMLMouvementPatient",
    'debiteursVenue'        => "CHPrimXMLDebiteursVenue"
  );

  /**
   * Get version
   *
   * @return string
   */
  static function getVersionEvenementsPatients() {    
    return "msgEvenementsPatients".str_replace(".", "", CAppUI::conf('hprimxml evt_patients version'));
  }

  /**
   * Get event
   *
   * @param string $messagePatient Message
   *
   * @return CHPrimXMLEvenementsPatients|void
   */
  static function getHPrimXMLEvenements($messagePatient) {
    $hprimxmldoc = new CHPrimXMLDocument("patient", self::getVersionEvenementsPatients());
    // Récupération des informations du message XML
    $hprimxmldoc->loadXML($messagePatient);
    
    $type = $hprimxmldoc->getTypeEvenementPatient();

    if ($type) {
      return new self::$evenements[$type];
    } 

    return new CHPrimXMLEvenementsPatients();
  }

  /**
   * Construct
   *
   * @return CHPrimXMLEvenementsPatients
   */
  function __construct() {
    $this->evenement = "evt_patients";
    $this->type = "patients";
                
    parent::__construct("patients", self::getVersionEvenementsPatients());
  }

  /**
   * Get events
   *
   * @return array
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * Generate header message
   *
   * @return void
   */
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsPatients", false);
  }

  /**
   * Mapping patient
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  function mappingPatient(DOMNode $node, CPatient $mbPatient) {
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    $sender = $this->_ref_echange_hprim->_ref_sender;
    
    if (!$sender->_configs["fully_qualified"]) {
      $mbPatient->nullifyAlteredFields();
    }
    
    return $mbPatient;
  }

  /**
   * Get
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Person
   *
   * @return CMbObject|CMediusers|CPatient
   */
  static function getPersonnePhysique(DOMNode $node, CPatient $mbPatient) {
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
    $mbPatient = self::getPersonne($personnePhysique, $mbPatient);
    
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $xpath->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance = $xpath->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $xpath->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance = $xpath->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }

  /**
   * Return person
   *
   * @param DOMNode   $node       Node
   * @param CMbObject $mbPersonne Person
   *
   * @return CMbObject|CMediusers|CPatient
   */
  static function getPersonne(DOMNode $node, CMbObject $mbPersonne) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $civilite = $xpath->queryAttributNode("hprim:civiliteHprim", $node, "valeur");
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "mlle"  => "mlle",
      "mr"    => "m",
      "dr"    => "dr",
      "pr"    => "pr",
      "bb"    => "enf",
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
      }
      else if ($mbPersonne->civilite == null) {
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
      $mbPersonne->tel  = isset($telephones[0]) && ($telephones[0] != $mbPersonne->tel2) ? $telephones[0] : null;
      $mbPersonne->tel2 = isset($telephones[1]) && ($telephones[1] != $mbPersonne->tel) ? $telephones[1] : null;
      $mbPersonne->email = $email;
    }
    elseif ($mbPersonne instanceof CMediusers) {
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

  /**
   * Récupérer l'activité socio-professionnelle
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  static function getActiviteSocioProfessionnelle(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $activiteSocioProfessionnelle = $xpath->queryTextNode("hprim:activiteSocioProfessionnelle", $node);
    
    $mbPatient->profession = $activiteSocioProfessionnelle ? $activiteSocioProfessionnelle : null;
    
    return $mbPatient;
  }

  /**
   * Récupérer les personnes à prévenir
   *
   * @param DOMNode  $node      Node
   * @param CPatient $mbPatient Patient
   *
   * @return CPatient
   */
  static function getPersonnesPrevenir(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $personnesPrevenir = $xpath->query("hprim:personnesPrevenir/*", $node);
    
    foreach ($personnesPrevenir as $personnePrevenir) {
      $prevenir = new CCorrespondantPatient;
      $prevenir->relation = "prevenir";
      $prevenir->nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePrevenir);
      $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePrevenir);
      $prevenir->prenom = $prenoms[0];
      
      $adresses = $xpath->queryUniqueNode("hprim:adresses", $personnePrevenir);
      $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
      $prevenir->adresse = $xpath->queryTextNode("hprim:ligne", $adresse);
      $prevenir->ville = $xpath->queryTextNode("hprim:ville", $adresse);
      $prevenir->cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
      
      $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $personnePrevenir);
      $prevenir->tel = isset($telephones[0]) ? $telephones[0] : null;
      
      $mbPatient->_ref_correspondants_patient[] = $prevenir;
    }
    
    return $mbPatient;
  }

  /**
   * Vérifier si les patients sont similaires
   *
   * @param CPatient $mbPatient  Patient
   * @param DOMNode  $xmlPatient Patient provenant des données XML
   *
   * @return bool
   */
  function checkSimilarPatient(CPatient $mbPatient, $xmlPatient) {
    $xpath = new CHPrimXPath($this);
        
    // Création de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);

    $nom     = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom  = $prenoms[0];
    
    return $mbPatient->checkSimilar($nom, $prenom);
  }

  /**
   * Get source ID
   *
   * @param string $query_evt  Event
   * @param string $query_type Type
   *
   * @return string
   */
  function getIdSourceObject($query_evt, $query_type) { 
    $xpath = new CHPrimXPath($this);
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $typeEvenement    = $xpath->queryUniqueNode($query_evt, $evenementPatient);
    
    $object = $xpath->queryUniqueNode($query_type, $typeEvenement);

    return $this->getIdSource($object);
  }

  /**
   * Mapping admit
   *
   * @param DOMNode $node    Node
   * @param CSejour $mbVenue Admit
   * @param bool    $cancel  Cancel
   *
   * @return CSejour
   */
  function mappingVenue(DOMNode $node, CSejour $mbVenue, $cancel = false) {
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
  
  static function getAttributesVenue(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
        
    $attributes = array();
    $attributes['confidentiel'] = $xpath->getValueAttributNode($node, "confidentiel"); 
    $attributes['etat'] = $xpath->getValueAttributNode($node, "etat"); 
    $attributes['facturable'] = $xpath->getValueAttributNode($node, "facturable"); 
    $attributes['declarationMedecinTraitant'] = $xpath->getValueAttributNode($node, "declarationMedecinTraitant"); 
    
    return $attributes;
  }
  
  static function getEtatVenue(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->getValueAttributNode($node, "etat"); 
  }
  
  function getNatureVenue(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    if ((CAppUI::conf("dPpmsi passage_facture") == "reception") && self::getEtatVenue($node) == "clôturée") {
      $mbVenue->facture = 1;
    }
        
    $sender = $this->_ref_echange_hprim->_ref_sender;

    // Obligatoire pour MB
    $nature = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur", "", false);
    $attrNatureVenueHprim = array (
      "hsp"  => "comp",
      "cslt" => "consult",
      "sc"   => "seances",
      "ambu" => "ambu",
      "exte" => "exte",
    );

    // Détermine le type de venue depuis la config des numéros de dossier 
    $type_config = self::getVenueType($sender, $mbVenue->_NDA);
    if ($type_config) {
      $mbVenue->type = $type_config;
    }
      
    if (!$mbVenue->type) {
      if ($nature) {
        $mbVenue->type = $attrNatureVenueHprim[$nature];
      }
    } 

    if (!$mbVenue->type) {      
      $mbVenue->type = "comp";
    }
    
    return $mbVenue;
  }
  
  static function getVenueType($sender, $nda) {
    $types = array(
      "type_sej_hospi"   => "comp",
      "type_sej_ambu"    => "ambu",
      "type_sej_urg"     => "urg",
      "type_sej_exte"    => "exte",
      "type_sej_scanner" => "seances",
      "type_sej_chimio"  => "seances",
      "type_sej_dialyse" => "seances",
    );
    
    foreach($types as $config => $type) {
      if (!$sender->_configs[$config]) {
        continue;
      }
      
      if (preg_match($sender->_configs[$config], $nda)) {
        return $type;
      }
    }
  }
  
  static function getEntree(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $entree = $xpath->queryUniqueNode("hprim:entree", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $entree);
    $heure = CMbDT::transform($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $entree), null , "%H:%M:%S");
    $modeEntree = $xpath->queryAttributNode("hprim:modeEntree", $entree, "valeur");
    
    $dateHeure = "$date $heure";

    if ($mbVenue->entree_reelle && CAppUI::conf("hprimxml notifier_entree_reelle")) {
      $mbVenue->entree_reelle = $dateHeure;
    }
    else {
      $mbVenue->entree_prevue = $dateHeure;
    }
       
    return $mbVenue;
  }
  
  static function isVenuePraticien(DOMNode $node) {
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
  
  function getMedecins(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $sender = $this->_ref_echange_hprim->_ref_sender;
    $medecins = $xpath->queryUniqueNode("hprim:medecins", $node);
    if ($medecins instanceof DOMElement) {
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
      $user->user_last_name = CAppUI::conf("hprimxml medecinIndetermine")." $sender->group_id";
      if (!$user->loadMatchingObject()) {
        $mediuser->_user_last_name = $user->user_last_name;
        $mediuser->_id = $this->createPraticien($mediuser);
      }
      else {
        $user->loadRefMediuser();
        $mediuser = $user->_ref_mediuser;
      }
      $mbVenue->praticien_id = $mediuser->_id;
    }

    return $mbVenue;
  }
  
  function getMedecin(DOMNode $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
        
    $code = $xpath->queryTextNode("hprim:identification/hprim:code", $node);
    $mediuser = new CMediusers();
    $id400 = new CIdSante400();
    $id400->object_class = "CMediusers";
    $id400->tag = $this->_ref_echange_hprim->_ref_sender->_tag_mediuser;
    $id400->id400 = $code;
    if ($id400->loadMatchingObject()) {
      $mediuser->_id = $id400->object_id;
    }
    else {
      // Récupération du typePersonne
      // Obligatoire pour MB
      $personne =  $xpath->queryUniqueNode("hprim:personne", $node, false);
      $mediuser = self::getPersonne($personne, $mediuser);
      
      $mediuser->_id = $this->createPraticien($mediuser);
      
      $id400->object_id = $mediuser->_id;
      $id400->last_update = CMbDT::dateTime();
      $id400->store(); 
    }
    
    return $mediuser->_id;
  }
  
  function createPraticien(CMediusers $mediuser) {
    $sender = $this->_ref_echange_hprim->_ref_sender;
    
    $functions = new CFunctions();
    $functions->text = CAppUI::conf("hprimxml functionPratImport");
    $functions->group_id = $sender->group_id;
    $functions->loadMatchingObject();
    if (!$functions->loadMatchingObject()) {
      $functions->type = "cabinet";
      $functions->compta_partagee = 0;
      $functions->store();
    }
    $mediuser->function_id = $functions->_id;
    $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name, null, true);
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

  static function getPlacement(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $placement = $xpath->queryUniqueNode("hprim:Placement", $node);
    
    if ($placement) {
      $mbVenue->modalite = $xpath->queryAttributNode("hprim:modePlacement", $placement, "modaliteHospitalisation");
    }
    
    return $mbVenue;
  }
  
  static function getSortie(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $sortie = $xpath->queryUniqueNode("hprim:sortie", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $sortie);
    $heure = CMbDT::transform($xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $sortie), null , "%H:%M:%S");
    if ($date) {
      $dateHeure = "$date $heure";
    }
    elseif (!$date && !$mbVenue->sortie_prevue) {
      $dateHeure = CMbDT::addDateTime(CAppUI::conf("dPplanningOp CSejour sortie_prevue ".$mbVenue->type).":00:00",
                    $mbVenue->entree_reelle ? $mbVenue->entree_reelle : $mbVenue->entree_prevue);
    } 
    else {
      $dateHeure = $mbVenue->sortie_reelle ? $mbVenue->sortie_reelle : $mbVenue->sortie_prevue;
    }
    
    // Cas dans lequel on ne récupère pas de sortie tant que l'on a pas la sortie réelle
    if ($mbVenue->sortie_reelle && CAppUI::conf("hprimxml notifier_sortie_reelle")) {
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
          $mbVenue = self::getEtablissementTransfert($destination, $mbVenue);
        }
      } 
      //retour au domicile
      else {
        $mbVenue->mode_sortie = "normal";
      }
    }
    
    return $mbVenue;
  }
  
  static function getEtablissementTransfert(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $code = $xpath->queryUniqueNode("hprim:code", $node);
    
    $etabExterne = new CEtabExterne();

    return $mbVenue->etablissement_sortie_id;
  }
  
  function mappingMouvements($node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    /* @FIXME Penser a parcourir tous les mouvements par la suite */
    $mouvement = $xpath->queryUniqueNode("hprim:mouvement", $node);

    if (!CAppUI::conf("hprimxml mvtComplet")) {
      //$mbVenue = $this->getMedecinResponsable($mouvement, $mbVenue);
    }
    
    return $mbVenue;
  } 
  
  function getMedecinResponsable(DOMNode $node, CSejour $mbVenue) {
    $xpath = new CHPrimXPath($node->ownerDocument);    
    
    $medecinResponsable = $xpath->queryUniqueNode("hprim:medecinResponsable", $node);
    
    if ($medecinResponsable) {
      $mbVenue->praticien_id = $this->getMedecin($medecinResponsable);
    }
    
    return $mbVenue;
  }
  
  function mappingDebiteurs(DOMNode $node, CPatient $mbPatient) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    /* @FIXME Penser a parcourir tous les debiteurs par la suite */
    $debiteur = $xpath->queryUniqueNode("hprim:debiteur", $node);

    $mbPatient = $this->getAssurance($debiteur, $mbPatient);
     
    return $mbPatient;
  }
  
  static function getAssurance(DOMNode $node, CPatient $mbPatient) {
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
  
  static function getAssure(DOMNode $node, CPatient $mbPatient) {
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
  
  function doNotCancelVenue(CSejour $venue, $dom_acq, $echg_hprim) {
    // Impossible d'annuler un séjour en cours 
    if ($venue->entree_reelle) {
      $commentaire = "La venue $venue->_id que vous souhaitez annuler est impossible.";
      return $echg_hprim->setAckError($dom_acq, "E108", $commentaire, $venue);
    }
    
    // Impossible d'annuler un dossier ayant une intervention
    $where = array();
    $where['annulee'] = " = '0'";
    $venue->loadRefsOperations($where);
    if (count($venue->_ref_operations) > 0) {
      $commentaire = "La venue $venue->_id que vous souhaitez annuler est impossible.";
      return $echg_hprim->setAckError($dom_acq, "E109", $commentaire, $venue);
    }  
  }
  
  function trashNDA(CSejour $venue, CInteropSender $sender) {
    if (isset($sender->_configs["type_sej_pa"])) {
      if ($venue->_NDA && preg_match($sender->_configs["type_sej_pa"], $venue->_NDA)) {
        // Passage en pa_ de l'id externe
        $num_pa = new CIdSante400();
        $num_pa->object_class = "CSejour";
        $num_pa->tag = $sender->_tag_sejour;
        $num_pa->id400 = $venue->_NDA;
        if ($num_pa->loadMatchingObject()) {
          $num_pa->tag = CAppUI::conf('dPplanningOp CSejour tag_dossier_pa').$sender->_tag_sejour;
          $num_pa->last_update = CMbDT::dateTime();
          $num_pa->store();
        }
        return false;
      }
    }
    if ($venue->_NDA) {
      return true;
    }
    return false;
  }
}