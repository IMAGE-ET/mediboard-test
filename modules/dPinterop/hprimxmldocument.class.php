<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Thomas Despoix
*/

CAppUI::requireModuleClass("dPinterop", "mbxmldocument");

if (!class_exists("CMbXMLDocument")) {
  return;
}

class CHPrimXMLDocument extends CMbXMLDocument {
  var $finalpath = "files/hprim";
  var $documentfinalprefix    = null;
  var $documentfinalfilename  = null;
  var $sentFiles = array();
  
  var $_identifiant           = null;
  var $_date_production       = null;
  var $_emetteur              = null;
  var $_identifiant_emetteur  = null;
  var $_destinataire          = null;
  var $_destinataire_libelle  = null;
   
  function __construct($schemaname, $schemafilename = null, $module = null) {
    parent::__construct();
    
    $this->patharchiveschema = $module ? "modules/".$module."/hprim" : "modules/dPinterop/hprim";
    $this->schemapath = "$this->patharchiveschema/$schemaname";
    $this->schemafilename = ($schemafilename) ? "$this->schemapath/$schemafilename.xsd" : "$this->schemapath/schema.xml";
    $this->documentfilename = "$this->schemapath/document.xml";
    $this->finalpath .= "/$schemaname";
    
    $this->now = time();
  }
  
  function checkSchema() {
    if (!is_dir($this->schemapath)) {
      trigger_error("ServeurActe schemas are missing. Please extract them from archive in '$this->schemapath/' directory", E_USER_WARNING);
      return false;
    }
    
    
    if (!is_file($this->schemafilename)) {
      $schema = new CHPrimXMLSchema();
      $schema->importSchemaPackage($this->schemapath);
      $schema->purgeIncludes();
      $schema->purgeImportedNamespaces();
      $schema->save($this->schemafilename);
    }
    
    return true;
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = "http://www.hprim.org/hprimXML") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }
  
  function addNameSpaces() {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "http://www.hprim.org/hprimXML schema.xml");
  }
  
  function saveTempFile() {
    parent::save(utf8_encode($this->documentfilename));
  }
  
  function saveFinalFile() {
    $this->documentfinalfilename = "$this->finalpath/$this->documentfinalprefix-$this->now.xml";
    CMbPath::forceDir(dirname($this->documentfinalfilename));
    parent::save($this->documentfinalfilename);
  }
  
  function getSentFiles() {
    $pattern = "$this->finalpath/$this->documentfinalprefix-*.xml";
    foreach(glob($pattern) as $sentFile) {
      $baseName = basename($sentFile);
      $matches = null;
      preg_match("`^[[:alpha:]]{2,3}[[:digit:]]{6}-([[:digit:]]*)\.xml$`", $baseName, $matches);
      $timeStamp = $matches[1];
      $this->sentFiles[] = array (
        "name" => $baseName,
        "path" => $sentFile,
        "datetime" => strftime("%Y-%m-%d %H:%M:%S", $timeStamp)
      );
    }
  }
  
  function addTexte($elParent, $elName, $elValue, $elMaxSize = 35) {
    $elValue = substr($elValue, 0, $elMaxSize);
    return $this->addElement($elParent, $elName, $elValue);
  }
  
  function addDateHeure($elParent, $dateTime = null) {
    $this->addElement($elParent, "date", mbDate(null, $dateTime));
    $this->addElement($elParent, "heure", mbTime(null, $dateTime));
  }
  
  function addCodeLibelle($elParent, $nodeName, $code, $libelle) {
    $codeLibelle = $this->addElement($elParent, $nodeName);
    $this->addTexte($codeLibelle, "code", $code, 10);
    $this->addTexte($codeLibelle, "libelle", $libelle, 35);
    return $codeLibelle;
  }
  
  function addAgent($elParent, $categorie, $code, $libelle) {
    $agent = $this->addCodeLibelle($elParent, "agent", $code, $libelle);
    $this->addAttribute($agent, "categorie", $categorie);
    return $agent;
    
  }
  
  function addIdentifiantPart($elParent, $partName, $partValue, $referent = null) {
    $part = $this->addElement($elParent, $partName);
    $this->addTexte($part, "valeur", $partValue, 17);
    $this->addAttribute($part, "etat", "permanent");
    $this->addAttribute($part, "portee", "local");
    $ref = ($referent) ? "oui" : "non";
    $this->addAttribute($part, "referent", $ref);
  }
    
  function addUniteFonctionnelle($elParent, $mbOp) {
    $this->addCodeLibelle($elParent, "uniteFonctionnelle", $mbOp->code_uf, $mbOp->libelle_uf);
  }
  
  function addProfessionnelSante($elParent, $mbMediuser) {
    $medecin = $this->addElement($elParent, "medecin");
    $this->addElement($medecin, "numeroAdeli", $mbMediuser->adeli);
    $identification = $this->addElement($medecin, "identification");
    $this->addElement($identification, "code", "prat$mbMediuser->user_id");
    $this->addElement($identification, "libelle", $mbMediuser->_user_username);
    $personne = $this->addElement($medecin, "personne");
    $this->addElement($personne, "nomUsuel", $mbMediuser->_user_last_name);
    $prenoms = $this->addElement($personne, "prenoms");
    $this->addElement($prenoms, "prenom", $mbMediuser->_user_first_name);
    return $medecin;
  }
  
  function addActeCCAM($elParent, $mbActeCCAM, $mbOp) {
    $acteCCAM = $this->addElement($elParent, "acteCCAM");
    $this->addAttribute($acteCCAM, "action", "création");
    $this->addAttribute($acteCCAM, "facturable", "oui");
    $this->addAttribute($acteCCAM, "valide", "oui");
    $this->addAttribute($acteCCAM, "documentaire", "non");
    $this->addAttribute($acteCCAM, "gratuit", "non");
    $this->addAttribute($acteCCAM, "remboursementExceptionnel", $mbActeCCAM->_rembex ? "oui" : "non");
    
    $identifiant = $this->addElement($acteCCAM, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", "acte{$mbActeCCAM->_id}");
    $this->addElement($acteCCAM, "codeActe", $mbActeCCAM->code_acte);
    $this->addElement($acteCCAM, "codeActivite", $mbActeCCAM->code_activite);
    $this->addElement($acteCCAM, "codePhase", $mbActeCCAM->code_phase);

    $mbOpDebut = mbGetValue(
      $mbOp->debut_op, 
      $mbOp->entree_salle, 
      $mbOp->time_operation
    );
    
    $execute = $this->addElement($acteCCAM, "execute");
    $this->addElement($execute, "date", $mbOp->_ref_plageop->date);
    $this->addElement($execute, "heure", $mbOpDebut);

    $mbExecutant = $mbActeCCAM->_ref_executant;
    $executant = $this->addElement($acteCCAM, "executant");
    $medecins = $this->addElement($executant, "medecins");
    $medecinExecutant = $this->addElement($medecins, "medecinExecutant");
    $this->addAttribute($medecinExecutant, "principal", "oui");
    $this->addProfessionnelSante($medecinExecutant, $mbExecutant);
    $this->addUniteFonctionnelle($executant, $mbOp);
    
    $modificateurs = $this->addElement($acteCCAM, "modificateurs");
    foreach ($mbActeCCAM->_modificateurs as $mbModificateur) {
      $this->addElement($modificateurs, "modificateur", $mbModificateur);
    }
    
    if ($mbActeCCAM->code_association) {
      $this->addElement($acteCCAM, "codeAssociationNonPrevue", $mbActeCCAM->code_association);
    }
    
    $montant = $this->addElement($acteCCAM, "montant");
    if ($mbActeCCAM->montant_depassement > 0) {
      $montantDepassement = $this->addElement($montant, "montantDepassement", sprintf("%.2f", $mbActeCCAM->montant_depassement));
    }
    
    return $acteCCAM;
  }
  
  function addPatient($elParent, $mbPatient, $addPat = null, $referent = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    $pat = $addPat ? "pat" : "";
    
    if(!$referent) {
      $this->addIdentifiantPart($identifiant, "emetteur",  $pat.$mbPatient->patient_id, $referent);
      if($mbPatient->_IPP)
        $this->addIdentifiantPart($identifiant, "recepteur", $mbPatient->_IPP, $referent);
    } else {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbPatient->_IPP, $referent);
      
      if(isset($mbPatient->_id400))
        $this->addIdentifiantPart($identifiant, "recepteur", $pat.$mbPatient->_id400, $referent);
    }  
    
    $personnePhysique = $this->addElement($elParent, "personnePhysique");
    
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
      "j" => "F"
    );
    
    $this->addAttribute($personnePhysique, "sexe", $sexeConversion[$mbPatient->sexe]);
    $this->addTexte($personnePhysique, "nomUsuel", $mbPatient->nom);
    $this->addTexte($personnePhysique, "nomNaissance", $mbPatient->_nom_naissance);
    
    $prenoms = $this->addElement($personnePhysique, "prenoms");
    foreach ($mbPatient->_prenoms as $mbKey => $mbPrenom) {
      if ($mbKey < 3) {
        $this->addTexte($prenoms, "prenom", $mbPrenom);
      }
    }
    
    $adresses = $this->addElement($personnePhysique, "adresses");
    $adresse = $this->addElement($adresses, "adresse");
    $this->addTexte($adresse, "ligne", substr($mbPatient->adresse, 0, 35));
    $this->addTexte($adresse, "ville", $mbPatient->ville);
    $this->addElement($adresse, "pays", str_pad($mbPatient->pays_insee, 3, '0', STR_PAD_LEFT));
    $this->addElement($adresse, "codePostal", $mbPatient->cp);
    
    $telephones = $this->addElement($personnePhysique, "telephones");
    $this->addElement($telephones, "telephone", $mbPatient->tel);
    $this->addElement($telephones, "telephone", $mbPatient->tel2);
    
    $emails = $this->addElement($personnePhysique, "emails");
    $this->addElement($emails, "email", $mbPatient->email);
    
    $dateNaissance = $this->addElement($personnePhysique, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->naissance);
    
    $lieuNaissance = $this->addElement($personnePhysique, "lieuNaissance");
    $this->addElement($lieuNaissance, "ville", $mbPatient->lieu_naissance);
    $this->addElement($lieuNaissance, "pays", str_pad($mbPatient->pays_naissance_insee, 3, '0', STR_PAD_LEFT));
    $this->addElement($lieuNaissance, "codePostal", $mbPatient->cp_naissance);
  }
  
  function addErreurAvertissement($elParent, $statut, $code, $libelle, $commentaires = null, $mbObject = null) {
    $erreurAvertissement = $this->addElement($elParent, "erreurAvertissement");
    $this->addAttribute($erreurAvertissement, "statut", $statut);
    
    $enregistrementPatient = $this->addElement($erreurAvertissement, "enregistrementPatient");
    $identifiantPatient = $this->addElement($enregistrementPatient, "identifiantPatient");
    
    if ($mbObject) {
      $this->addIdentifiantPart($identifiantPatient, "emetteur",  $mbObject->_id);
    }
     
    $observations = $this->addElement($erreurAvertissement, "observations");
    $observation = $this->addObservation($observations, $code, $libelle, $commentaires);   
  }
  
  function addObservation($elParent, $code, $libelle, $commentaires = null) {
    $observation = $this->addElement($elParent, "observation");
    
    $this->addElement($observation, "code", $code);
    $this->addElement($observation, "libelle", $libelle);
    $this->addElement($observation, "commentaire", substr($commentaires, 0, 4000)); 
  }
  
  function getTypeEvenementPatient() {
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
    
    $query = "/hprim:evenementsPatients/hprim:evenementPatient/*";
    
    $evenementPatient = $xpath->queryUniqueNode($query);
    
    return $evenementPatient->tagName;
  }
  
  function addVenue($elParent, $mbVenue, $referent = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->sejour_id, $referent);
    
    $natureVenueHprim = $this->addElement($elParent, "natureVenueHprim");
    $attrNatureVenueHprim = array (
      "CSejour" => "hsp",
      "CConsultation" => "cslt",
    );
    $this->addAttribute($natureVenueHprim, "valeur", $attrNatureVenueHprim[$mbVenue->_class_name]);
    
    $entree = $this->addElement($elParent, "entree");
    
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
  	$this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->_entree));
  	$this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->_entree));
  	
  	$modeEntree = $this->addElement($entree, "modeEntree");
  	$this->addAttribute($modeEntree, "valeur", $attrNatureVenueHprim[$mbVenue->_class_name]);
  	
  	$medecins = $this->addElement($elParent, "medecins");
  	$medecin = $this->addElement($medecins, "medecin");
  	$this->addAttribute($medecin, "lien", "");
  	
  }
}

?>