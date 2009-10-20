<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLDocument extends CMbXMLDocument {
  var $finalpath = null;
  var $documentfinalprefix    = null;
  var $documentfinalfilename  = null;
  var $sentFiles = array();
  
  var $_identifiant           = null;
  var $_date_production       = null;
  var $_emetteur              = null;
  var $_identifiant_emetteur  = null;
  var $_destinataire          = null;
  var $_destinataire_libelle  = null;
   
  function __construct($dirschemaname, $schemafilename = null) {
    parent::__construct();
    
    $this->patharchiveschema = "modules/hprimxml/xsd";
    $this->schemapath = "$this->patharchiveschema/$dirschemaname";
    $this->schemafilename = ($schemafilename) ? "$this->schemapath/$schemafilename.xsd" : "$this->schemapath/schema.xml";
    $this->documentfilename = "$this->schemapath/document.xml";
    $this->finalpath = CFile::$directory . "/hprim/$dirschemaname";
    
    $this->now = time();
  }
  
  function schemaValidate($filename = null, $returnErrors = false) {
    if (!CAppUI::conf("hprimxml evt_serveuractes validation") || 
        !CAppUI::conf("hprimxml evt_pmsi validation") ||
        !CAppUI::conf("hprimxml evt_patients validation")) {
      return true;
    }
    return parent::schemaValidate($filename, $returnErrors);
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
		mbTrace($this->finalpath);
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
  
  function addPatient($elParent, $mbPatient, $addPat = null, $referent = null, $light = false) {
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
    
    // Ajout typePersonnePhysique
    $this->addPersonnePhysique($elParent, $mbPatient, $light);
  }
  
  function addPersonnePhysique($elParent, $mbPatient, $light = false) {
    $personnePhysique = $this->addElement($elParent, "personnePhysique");
    
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
    );
    
    $this->addAttribute($personnePhysique, "sexe", $sexeConversion[$mbPatient->sexe]);
    
    // Ajout typePersonne
    $this->addPersonne($personnePhysique, $mbPatient ,$light);
    
    $dateNaissance = $this->addElement($personnePhysique, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->naissance);
    
    $lieuNaissance = $this->addElement($personnePhysique, "lieuNaissance");
    $this->addElement($lieuNaissance, "ville", $mbPatient->lieu_naissance);
    $this->addElement($lieuNaissance, "pays", str_pad($mbPatient->pays_naissance_insee, 3, '0', STR_PAD_LEFT));
    $this->addElement($lieuNaissance, "codePostal", $mbPatient->cp_naissance);
  }
  
  function addPersonnePhysiqueLight($elParent, $mbPatient) {
    
  }
  
  function addPersonne($elParent, $mbPersonne, $light = false) {
    $personne = array();
    $civiliteHprimConversion = array (
      "mme"   => "mme",
      "melle" => "mlle",
      "m"     => "mr",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
      
    if ($mbPersonne instanceof CPatient) {
      $personne['nom'] = $mbPersonne->nom;
      $personne['nomNaissance'] = $mbPersonne->_nom_naissance;
      if (isset($mbPersonne->_prenoms)) {
        foreach ($mbPersonne->_prenoms as $mbKey => $mbPrenom) {
          if ($mbKey < 3) {
            $personne['prenoms'][] = $mbPrenom; 
          }
        }
      }
      if (!$light) {
        $personne['civilite'] = $mbPersonne->civilite;
      }
      $personne['ligne'] = $mbPersonne->adresse;
      $personne['ville'] = $mbPersonne->ville;
      $personne['pays'] = $mbPersonne->pays_insee;
      $personne['codePostal'] = $mbPersonne->cp;
      $personne['tel'] = $mbPersonne->tel;
      $personne['tel2'] = $mbPersonne->tel2;
      if (!$light) {
        $personne['email'] = $mbPersonne->email;
      }
    } else if ($mbPersonne instanceof CMedecin) {
      $personne['nom'] = $mbPersonne->nom;
      $personne['nomNaissance'] = $mbPersonne->jeunefille;
      if (!$light) {
        $personne['civilite'] = "";
      }
      $personne['prenoms'][] = $mbPersonne->prenom;
      $personne['ligne'] = $mbPersonne->adresse;
      $personne['ville'] = $mbPersonne->ville;
      $personne['codePostal'] = $mbPersonne->cp;
      $personne['pays'] = "";
      $personne['tel'] = $mbPersonne->tel;
      $personne['tel2'] = $mbPersonne->portable;
      if (!$light) {
        $personne['email'] = $mbPersonne->email;
      }
    } else if ($mbPersonne instanceof CMediusers) {
      $personne['nom'] = $mbPersonne->_user_last_name;
      $personne['nomNaissance'] = "";
      if (!$light) {
        $personne['civilite'] = "";
      }
      $personne['prenoms'][] = $mbPersonne->_user_first_name;
      $personne['ligne'] = $mbPersonne->_user_adresse;
      $personne['ville'] = $mbPersonne->_user_ville;
      $personne['codePostal'] = $mbPersonne->_user_cp;
      $personne['pays'] = "";
      $personne['tel'] = $mbPersonne->_user_phone;
      $personne['tel2'] = "";
      if (!$light) {
        $personne['email'] = $mbPersonne->_user_email;
      }
    }
    
    $this->addTexte($elParent, "nomUsuel", $personne['nom']);
    $this->addTexte($elParent, "nomNaissance", $personne['nomNaissance']);
    $prenoms = $this->addElement($elParent, "prenoms");
    foreach ($personne['prenoms'] as $prenom) {
      $this->addTexte($prenoms, "prenom", $prenom);
    }
    if (!$light) {
      if ($personne['civilite']) {
        $civiliteHprim = $this->addElement($elParent, "civiliteHprim");
        $this->addAttribute($civiliteHprim, "valeur", $civiliteHprimConversion[$personne['civilite']]);    
      }
    }
    $adresses = $this->addElement($elParent, "adresses");
    $adresse = $this->addElement($adresses, "adresse");
    $this->addTexte($adresse, "ligne", substr($personne['ligne'], 0, 35));
    $this->addTexte($adresse, "ville", $personne['ville']);
    $this->addElement($adresse, "pays", str_pad($personne['pays'], 3, '0', STR_PAD_LEFT));
    $this->addElement($adresse, "codePostal", $personne['codePostal']);

    $telephones = $this->addElement($elParent, "telephones");
    $this->addElement($telephones, "telephone", $personne['tel']);
    $this->addElement($telephones, "telephone", $personne['tel2']);
    
    if (!$light) {
      $emails = $this->addElement($elParent, "emails");
      $this->addElement($emails, "email", $personne['email']);
    }
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
  
  function addMedecin($elParent, $praticien, $lien) {
    $medecin = $this->addElement($elParent, "medecin");
    $this->addAttribute($medecin, "lien", $lien);
    $this->addElement($medecin, "numeroAdeli", $praticien->adeli);
    $personne = $this->addElement($medecin, "personne");
    $this->addPersonne($personne, $praticien);
  }
}

?>