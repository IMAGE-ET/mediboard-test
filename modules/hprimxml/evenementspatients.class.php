<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsPatients extends CHPrimXMLDocument {
  static function getHPrimXMLEvenementsPatients($messagePatient) {
    $hprimxmldoc = new CHPrimXMLDocument("patient", "msgEvenementsPatients105");
    // Récupération des informations du message XML
    $hprimxmldoc->loadXML(utf8_decode($messagePatient));
    
    $type = $hprimxmldoc->getTypeEvenementPatient();
    // Un événement concernant un patient appartient à l'une des six catégories suivantes
    switch ($type) {
      case "enregistrementPatient" :
        return new CHPrimXMLEnregistrementPatient();
        break;
      case "fusionPatient" :
        return new CHPrimXMLFusionPatient();
        break;
      case "venuePatient" :
        return new CHPrimXMLVenuePatient();
        break;
      case "fusionVenue" :
        return new CHPrimXMLFusionVenue();
        break;
      default;
        return new CHPrimXMLEvenementsPatients();
    }
  }
  
  function __construct() {
    $this->evenement = "evt_patients";
    $this->destinataire_libelle = "";
    $this->type = "patients";
                
    parent::__construct("patients", "msgEvenementsPatients105");
  }

  function generateEnteteMessageEvenementsPatients() {
    $evenementsPatients = $this->addElement($this, "evenementsPatients", null, "http://www.hprim.org/hprimXML");
    // Retourne un message d'acquittement par le récepteur
    $this->addAttribute($evenementsPatients, "acquittementAttendu", "oui");
    
    $this->addEnteteMessage($evenementsPatients);
  }
  
  static function getIdSource($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $emetteur = $xpath->queryUniqueNode("hprim:emetteur", $identifiant);
    $referentEmetteur = $xpath->queryAttributNode("hprim:emetteur", $node, "referent");
    return $xpath->queryTextNode("hprim:valeur", $emetteur);
  }
  
  static function getIdCible($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    $recepteur = $xpath->queryUniqueNode("hprim:recepteur", $identifiant);
    $referentRecepteur = $xpath->queryAttributNode("hprim:recepteur", $node, "referent");
    return $xpath->queryTextNode("hprim:valeur", $recepteur);
  }
  
  function mappingPatient($node, $mbPatient) {    
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $mbPatient;
  }
  
  static function getPersonnePhysique($node, $mbPatient) {
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
    $mbPatient = $this->getPersonne($personnePhysique,$mbPatient);
    
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $xpath->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $xpath->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance = $xpath->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $xpath->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance = $xpath->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }
  
  static function getPersonne($node, $mbPatient) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $civilite = $xpath->queryAttributNode("hprim:civiliteHprim", $node, "valeur");
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "melle" => "mlle",
      "mr"     => "m",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
    $mbPatient->civilite = $civiliteHprimConversion[$civilite];    
    
    $mbPatient->nom = $xpath->queryTextNode("hprim:nomUsuel", $node);
    $mbPatient->_nom_naissance = $xpath->queryTextNode("hprim:nomNaissance", $node);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $node);
    $mbPatient->prenom = $prenoms[0];
    $mbPatient->prenom_2 = isset($prenoms[1]) ? $prenoms[1] : "";
    $mbPatient->prenom_3 = isset($prenoms[2]) ? $prenoms[2] : "";
    
    $adresses = $xpath->queryUniqueNode("hprim:adresses", $node);
    $adresse = $xpath->queryUniqueNode("hprim:adresse", $adresses);
    $mbPatient->adresse = $xpath->queryTextNode("hprim:ligne", $adresse);
    $mbPatient->ville = $xpath->queryTextNode("hprim:ville", $adresse);
    $mbPatient->pays_insee = $xpath->queryTextNode("hprim:pays", $adresse);
    $pays = new CPaysInsee();
    $pays->numerique = $mbPatient->pays_insee;
    $pays->loadMatchingObject();
    $mbPatient->pays = $pays->nom_fr;
    $mbPatient->cp = $xpath->queryTextNode("hprim:codePostal", $adresse);
    
    $telephones = $xpath->getMultipleTextNodes("hprim:telephones/*", $node);
    $mbPatient->tel = isset($telephones[0]) ? $telephones[0] : "";
    $mbPatient->tel2 = isset($telephones[1]) ? $telephones[1] : "";
    
    $emails = $xpath->getMultipleTextNodes("hprim:emails/*", $node);
    $mbPatient->email = isset($emails[0]) ? $emails[0] : "";
    
    return $mbPatient;
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
  
  static function checkSimilarPatient($mbPatient, $xmlPatient) {
    $xpath = new CMbXPath($this, true);
        
    // Création de l'element personnePhysique
    $personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $xmlPatient);
    $nom = $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $prenom = $prenoms[0];
    
    return $mbPatient->checkSimilar($nom, $prenom);
  }
  
  static function getIPPPatient($query) { 
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $query_evt = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query_evt);
    $typeEvenement = $xpath->queryUniqueNode($query, $evenementPatient);
    
    $patient = $xpath->queryUniqueNode("hprim:patient", $typeEvenement);

    return $this->getIdSource($patient);
  }
  
  function getEvenementPatientXML() { 
    $xpath = new CMbXPath($this, true);
    
    $data['acquittement'] = $xpath->queryAttributNode("/hprim:evenementsPatients", null, "acquittementAttendu");

    $query = "/hprim:evenementsPatients/hprim:enteteMessage";

    $entete = $xpath->queryUniqueNode($query);

    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);
    
    return $data;
  }
  
  static function getActionEvenement($query, $node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    return $xpath->queryAttributNode($query, $node, "action");    
  }
  
  function mappingVenue($node, $mbVenue) {    
    $mbVenue = $this->getNatureVenue($node, $mbVenue);
    $mbVenue = $this->getEntree($node, $mbVenue);
    $mbVenue = $this->getMedecins($node, $mbVenue);
    $mbVenue = $this->getPlacement($node, $mbVenue);
    $mbVenue = $this->getSortie($node, $mbVenue);
    
    return $mbVenue;
  }
  
  static function getAttributesVenue($node, $mbVenue) {
  	$xpath = new CMbXPath($node->ownerDocument, true);
    
    $attributes = array();
    $attributes['confidentiel'] = $xpath->queryAttributNode($query, $node, "confidentiel"); 
    $attributes['etat'] = $xpath->queryAttributNode($query, $node, "etat"); 
    $attributes['facturable'] = $xpath->queryAttributNode($query, $node, "facturable"); 
    $attributes['declarationMedecinTraitant'] = $xpath->queryAttributNode($query, $node, "declarationMedecinTraitant"); 
    
    return $attributes;
  }
  
  static function getNatureVenue($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $attributes = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur");
    mbTrace($attributes, "attributes", true);
    $attrNatureVenueHprim = array (
      "hsp"  => "comp",
      "cslt" => "consult",
      "sc" => "seances",
    );
    
    $mbVenue->type =  $attrNatureVenueHprim[$nature];
    
    return $mbVenue;
  }
  
  static function getEntree($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $entree = $xpath->queryUniqueNode("hprim:entree", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $node);
    $heure = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $node);
    $modeEntree = $xpath->queryAttributNode("hprim:modeEntree", $entree, "valeur");
    
    $mbVenue->entree_prevue = "$date $heure";
    
    return $mbVenue;
  }
  
  static function getMedecins($node, $mbVenue) {
    
  }
  
  static function getPlacement($node, $mbVenue) {
    
  }
  
  static function getSortie($node, $mbVenue) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $entree = $xpath->queryUniqueNode("hprim:sortie", $node);
  
    $date = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:date", $node);
    $heure = $xpath->queryTextNode("hprim:dateHeureOptionnelle/hprim:heure", $node);
    
    $attributes = $xpath->queryAttributNode("hprim:natureVenueHprim", $node, "valeur");
    if (($attributes['etat'] == "préadmission") || ($attributes['etat'] == "encours")) {
      $mbVenue->sortie_prevue = "$date $heure";
    } else if ($attributes['etat'] == "clôturée") {
      $mbVenue->sortie_reelle = "$date $heure";
    }
    
    
    return $mbVenue;
  }
}
?>