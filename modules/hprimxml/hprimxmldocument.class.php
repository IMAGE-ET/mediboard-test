<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLDocument extends CMbXMLDocument {
  var $evenement              = null;
  var $finalpath              = null;
  var $documentfinalprefix    = null;
  var $documentfinalfilename  = null;
  var $sentFiles              = array();
  
  var $identifiant           = null;
  var $date_production       = null;
  var $emetteur              = null;
  var $identifiant_emetteur  = null;
  var $destinataire          = null;
  var $destinataire_libelle  = null;
  var $group_id              = null;
  
  var $type                  = null;
  var $sous_type             = null;
  
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
    if (!CAppUI::conf("hprimxml ".$this->evenement." validation")) {
      return true;
    }
    return parent::schemaValidate($filename, $returnErrors);
  }
  
  function checkSchema() {
    if (!is_dir($this->schemapath)) {
      trigger_error("HPRIMXML schemas are missing. Please extract them from archive in '$this->schemapath/' directory", E_USER_WARNING);
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
  
  function addEnteteMessage($elParent) {
    global $AppUI;

    $enteteMessage = $this->addElement($elParent, "enteteMessage");
    $this->addElement($enteteMessage, "identifiantMessage", $this->identifiant ? $this->identifiant : "ES{$this->now}");
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction", $this->date_production ? $this->date_production : mbDateTime());
    
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "acteur", "user$AppUI->user_id", "$AppUI->user_first_name $AppUI->user_last_name");
    $this->addAgent($agents, "système", $this->emetteur ? $this->emetteur : CAppUI::conf('mb_id'), $group->text);
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $this->destinataire, $this->destinataire_libelle);
    $this->addAgent($agents, "système", $group->_id, $group->text);
  }
  
  function generateTypeEvenement($mbObject, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $this->date_production    = $echg_hprim->date_production = mbDateTime();
    $echg_hprim->emetteur     = $this->emetteur;
    $echg_hprim->group_id     = $this->group_id;
    $echg_hprim->destinataire = $this->destinataire;
    $echg_hprim->type         = $this->type;
    $echg_hprim->sous_type    = $this->sous_type;
    $echg_hprim->message      = utf8_encode($this->saveXML());
    if ($mbObject instanceof CPatient) {
      if ($mbObject->_IPP) {
        $echg_hprim->id_permanent = $mbObject->_IPP;
      }
    }
    if ($mbObject instanceof CSejour) {
      if ($mbObject->_num_dossier) {
        $echg_hprim->id_permanent = $mbObject->_num_dossier;
      }
    }
    if ($initiateur) {
      $echg_hprim->initiateur_id = $initiateur;
    }
    
    $echg_hprim->store();
    
    $this->identifiant = str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT);
            
    $this->generateEnteteMessageEvenementsPatients();
    $this->generateFromOperation($mbObject, $referent);

    $doc_valid = $this->schemaValidate();
    $echg_hprim->message_valide = $doc_valid ? 1 : 0;

    $this->saveTempFile();
    $msg = utf8_encode($this->saveXML()); 
    
    $echg_hprim->message = $msg;
    $echg_hprim->store();
    
    return $msg;
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
  
  function addUniteFonctionnelleResponsable($elParent, $mbOp) {
    $this->addCodeLibelle($elParent, "uniteFonctionnelleResponsable", $mbOp->code_uf, $mbOp->libelle_uf);
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

    $mbOpDebut = CValue::first(
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
      $this->addIdentifiantPart($identifiant, "emetteur",  $pat.$mbPatient->_id, $referent);
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
    $sexe = $mbPatient->sexe ? $sexeConversion[$mbPatient->sexe] : "I";
    $this->addAttribute($personnePhysique, "sexe", $sexe);
    
    // Ajout typePersonne
    $this->addPersonne($personnePhysique, $mbPatient ,$light);
    
    $dateNaissance = $this->addElement($personnePhysique, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->naissance);
    
    $lieuNaissance = $this->addElement($personnePhysique, "lieuNaissance");
    $this->addElement($lieuNaissance, "ville", $mbPatient->lieu_naissance);
    if ($mbPatient->pays_naissance_insee)
    	$this->addElement($lieuNaissance, "pays", str_pad($mbPatient->pays_naissance_insee, 3, '0', STR_PAD_LEFT));
    $this->addElement($lieuNaissance, "codePostal", $mbPatient->cp_naissance);
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
    foreach ($personne['prenoms'] as $key => $prenom) {
      if ($key == 0) {
        $this->addTexte($prenoms, "prenom", $prenom ? $prenom : $personne['nom']);
      } else {
        $this->addTexte($prenoms, "prenom", $prenom);
      }
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
    if ($personne['pays'])
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
     
    $dateHeureEvenementConcerne =  $this->addElement($erreurAvertissement, "dateHeureEvenementConcerne");
    $this->addElement($dateHeureEvenementConcerne, "date", mbDate());
    $this->addElement($dateHeureEvenementConcerne, "heure", mbTime());
    
    $evenementPatients = $this->addElement($erreurAvertissement, $this->_sous_type_evt);
    $identifiantPatient = $this->addElement($evenementPatients, "identifiantPatient");
    
    if ($this->_sous_type_evt == "fusionPatient") {
      $identifiantPatientElimine = $this->addElement($evenementPatients, "identifiantPatientElimine");
    }
    
    if ($this->_sous_type_evt == "venuePatient") {
      $identifiantVenue = $this->addElement($evenementPatients, "identifiantVenue");
    }
    
    if ($this->_sous_type_evt == "debiteursVenue") {
      $identifiantVenue = $this->addElement($evenementPatients, "identifiantVenue");
      $debiteurs = $this->addElement($evenementPatients, "debiteurs");
      $debiteur = $this->addElement($debiteurs, "debiteur");
      $identifiantParticulier = $this->addElement($debiteur, "identifiantParticulier");
    }
    
    if ($this->_sous_type_evt == "mouvementPatient") {
      $identifiantVenue = $this->addElement($evenementPatients, "identifiantVenue");
      $identifiantMouvement = $this->addElement($evenementPatients, "identifiantMouvement");
    }
    
    if ($this->_sous_type_evt == "fusionVenue") {
      $identifiantVenue = $this->addElement($evenementPatients, "identifiantVenue");
      $identifiantVenueEliminee = $this->addElement($evenementPatients, "identifiantVenueEliminee");
    }
    
    $observations = $this->addElement($erreurAvertissement, "observations");
    $observation = $this->addObservation($observations, $code, $libelle, $commentaires);   
  }
  
  function addObservation($elParent, $code, $libelle, $commentaires = null) {
    $observation = $this->addElement($elParent, "observation");
    
    $this->addElement($observation, "code", substr($code, 0, 17));
    $this->addElement($observation, "libelle", substr($libelle, 0, 80));
    $this->addElement($observation, "commentaire", substr($commentaires, 0, 4000)); 
  }
  
  function getTypeEvenementPatient() {
    $xpath = new CMbXPath($this);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );
        
    $evenementPatient = $xpath->query("/hprim:evenementsPatients/hprim:evenementPatient/*");
    $type = null;
    $evenements = CHPrimXMLEvenementsPatients::$evenements;
    foreach ($evenementPatient as $_evenementPatient) {
      if (array_key_exists($_evenementPatient->tagName, $evenements)) {
        $type = $_evenementPatient->tagName;
      }
    }

    return $type;
  }
  
  function addMedecin($elParent, $praticien, $lien) {
    $medecin = $this->addElement($elParent, "medecin");
    $this->addAttribute($medecin, "lien", $lien);
    $this->addElement($medecin, "numeroAdeli", $praticien->adeli);
    $identification = $this->addElement($medecin, "identification");
    $id400 = new CIdSante400();
    $id400->object_class = "CMediusers";
    $id400->object_id    = $praticien->_id;
    $id400->tag          = $this->destinataire;
    if ($id400->loadMatchingObject()) {
      $this->addElement($identification, "code", $id400->id400);
    } else {
      $this->addElement($identification, "code", $praticien->_id);
    }
    $this->addElement($identification, "libelle", $praticien->_view);
    $personne = $this->addElement($medecin, "personne");
    $this->addPersonne($personne, $praticien);
  }
  
  function addVenue($elParent, $mbVenue, $referent = null, $light = false) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    if(!$referent) {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->sejour_id, $referent);
      if($mbVenue->_num_dossier)
        $this->addIdentifiantPart($identifiant, "recepteur", $mbVenue->_num_dossier, $referent);
    } else {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->_num_dossier, $referent);
      
      if(isset($mbVenue->_id400))
        $this->addIdentifiantPart($identifiant, "recepteur", $mbVenue->_id400, $referent);
    }  
    
    $natureVenueHprim = $this->addElement($elParent, "natureVenueHprim");
    $attrNatureVenueHprim = array (
      "comp"    => "hsp",
      "ambu"    => "hsp",
      "urg"     => "hsp",
      "psy"     => "hsp",
      "ssr"     => "hsp",
      "exte"    => "hsp",
      "consult" => "cslt",
      "seances" => "sc"
    );
    $this->addAttribute($natureVenueHprim, "valeur", $attrNatureVenueHprim[$mbVenue->type]);
    
    $entree = $this->addElement($elParent, "entree");
    
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->_entree));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->_entree));
    
    $modeEntree = $this->addElement($entree, "modeEntree");
    // mode d'entrée inconnu
    $mode = "09";
    // admission après consultation d'un médecin de l'établissement
    if ($mbVenue->_ref_consult_anesth->_id) {
      $mode = "01";
    }
    // malade envoyé par un médecin extérieur
    if ($mbVenue->_ref_adresse_par_prat->_id) {
      $mode = "02";
    }
    $this->addAttribute($modeEntree, "valeur", $mode);
    
    if (!$light) {
      $medecins = $this->addElement($elParent, "medecins");
    
      // Traitement du medecin traitant du patient
      $_ref_medecin_traitant = $mbVenue->_ref_patient->_ref_medecin_traitant;
      if ($_ref_medecin_traitant->_id) {
        if ($_ref_medecin_traitant->adeli) {
          $this->addMedecin($medecins, $_ref_medecin_traitant, "trt");
        }
      }
      
      // Traitement du medecin adressant
      $_ref_adresse_par_prat = $mbVenue->_ref_adresse_par_prat;
      if ($mbVenue->_adresse_par_prat) {
        if ($_ref_adresse_par_prat->adeli) {
          $this->addMedecin($medecins, $_ref_adresse_par_prat, "adrs");
        }
      }
      
      // Traitement du responsable du séjour
      $this->addMedecin($medecins, $mbVenue->_ref_praticien, "rsp");
      
      // Traitement des prescripteurs
      $_ref_prescripteurs = $mbVenue->_ref_prescripteurs;
      if (is_array($_ref_prescripteurs)) {
        foreach ($_ref_prescripteurs as $prescripteur) {
          $this->addMedecin($medecins, $prescripteur, "prsc");
        }
      }
      
      // Traitement des intervenant (ayant effectués des actes)
      $_ref_actes_ccam = $mbVenue->_ref_actes_ccam;
      if (is_array($_ref_actes_ccam)) {
        foreach ($_ref_actes_ccam as $acte_ccam) {
          $intervenant = $acte_ccam->_ref_praticien;
          $this->addMedecin($medecins, $intervenant, "intv");
        }
      }
    }
    
    $sortie = $this->addElement($elParent, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->_sortie));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->_sortie)); 
    
    if ($mbVenue->mode_sortie) {
      $modeSortieHprim = $this->addElement($sortie, "modeSortieHprim");
      //retour au domicile
      if ($mbVenue->mode_sortie == "normal") {
        $modeSortieEtablissementHprim = "04";
      } 
      // décès
      else if ($mbVenue->mode_sortie == "deces") {
        $modeSortieEtablissementHprim = "05";
      } 
      // autre transfert dans un autre CH
      else if ($mbVenue->mode_sortie == "transfert") {
        $modeSortieEtablissementHprim = "02";
        if ($mbVenue->etablissement_transfert_id) {
          $destination = $this->addElement($elParent, "destination");
          $this->addElement($destination, "libelle", $mbVenue->etablissement_transfert_id);
        }
      }
      $this->addElement($modeSortieHprim, "code", $modeSortieEtablissementHprim);
      $this->addElement($modeSortieHprim, "libelle", $mbVenue->mode_sortie);
      
      $this->addAttribute($modeSortieHprim, "valeur", $modeSortieEtablissementHprim);
    }
    
    if (!$light) {
      $placement = $this->addElement($elParent, "Placement");
      $modePlacement = $this->addElement($placement, "modePlacement");
      $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbVenue->modalite);
      $this->addElement($modePlacement, "libelle", substr($mbVenue->_view, 0, 80));   
      
      $datePlacement = $this->addElement($placement, "datePlacement");
      $this->addElement($datePlacement, "date", mbDate($mbVenue->_entree));
    }
  }
  
  function addDebiteurs($elParent, $mbPatient, $referent = null) {
    $debiteur = $this->addElement($elParent, "debiteur");
    
    $assurance = $this->addElement($debiteur, "assurance");
    $this->addAssurance($assurance, $mbPatient, $referent);
  }
  
  function addAssurance($elParent, $mbPatient, $referent = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    $this->addElement($elParent, "nom", $mbPatient->regime_sante);
    
    $assure = $this->addElement($elParent, "assure");
    $this->addAssure($assure, $mbPatient);
    
    $dates = $this->addElement($elParent, "dates");
    $this->addElement($dates, "dateDebutDroit", mbDate($mbPatient->deb_amo));
    $this->addElement($dates, "dateFinDroit", mbDate($mbPatient->fin_amo));
    
    $obligatoire = $this->addElement($elParent, "obligatoire");
    $this->addElement($obligatoire, "grandRegime", $mbPatient->code_regime);
    $this->addElement($obligatoire, "caisseAffiliation", $mbPatient->caisse_gest);
    $this->addElement($obligatoire, "centrePaiement", $mbPatient->centre_gest);
    
    // Ajout des exonérations 
    $mbPatient->guessExoneration();
    if ($mbPatient->_type_exoneration) {
      $exonerationsTM = $this->addElement($obligatoire, "exonerationsTM");
      $exonerationTM = $this->addElement($exonerationsTM, "exonerationTM");
      $this->addAttribute($exonerationTM, "typeExoneration", $mbPatient->_type_exoneration);  
    }
  }
  
  function addAssure($elParent, $mbPatient) {
    $this->addElement($elParent, "immatriculation", $mbPatient->matricule);     
    
    $personne = $this->addElement($elParent, "personne");
    $sexeConversion = array (
      "m" => "M",
      "f" => "F",
    );
    $sexe = $mbPatient->assure_sexe ? $sexeConversion[$mbPatient->assure_sexe] : "I";
    $this->addAttribute($personne, "sexe", $sexe);  
    $this->addTexte($personne, "nomUsuel", $mbPatient->assure_nom);
    $this->addTexte($personne, "nomNaissance", $mbPatient->assure_nom_jeune_fille);
    $prenoms = $this->addElement($personne, "prenoms");
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom);
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom_2);
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom_3);
    $this->addTexte($prenoms, "prenom", $mbPatient->assure_prenom_4);
    $adresses = $this->addElement($personne, "adresses");
    $adresse = $this->addElement($adresses, "adresse");
    $this->addTexte($adresse, "ligne", substr($mbPatient->assure_adresse, 0, 35));
    $this->addTexte($adresse, "ville", $mbPatient->assure_ville);
    if ($mbPatient->assure_pays_insee)
      $this->addElement($adresse, "pays", str_pad($mbPatient->assure_pays_insee, 3, '0', STR_PAD_LEFT));
    $this->addElement($adresse, "codePostal", $mbPatient->assure_cp);
    $dateNaissance = $this->addElement($personne, "dateNaissance");
    $this->addElement($dateNaissance, "date", $mbPatient->assure_naissance ? $mbPatient->assure_naissance : $mbPatient->naissance);
    
    $this->addElement($elParent, "lienAssure", $mbPatient->rang_beneficiaire);
  }
}

?>