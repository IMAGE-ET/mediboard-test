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
  
  var $group_id  = null;
  
  var $type      = null;
  var $sous_type = null;
  
  // Behaviour fields
  var $_ref_sender      = null;
  var $_ref_receiver  = null;
  var $_ref_echange_hprim = null;
  
  function __construct($dirschemaname, $schemafilename = null) {
    parent::__construct();

    $this->formatOutput = false;
    
    $this->patharchiveschema = "modules/hprimxml/xsd";
    $this->schemapath        = "$this->patharchiveschema/$dirschemaname";
    $this->schemafilename    = ($schemafilename) ? 
                                ((!CAppUI::conf("hprimxml concatenate_xsd")) ? 
                                  "$this->schemapath/$schemafilename.xsd" : 
                                  "$this->schemapath/$schemafilename.xml") :
                                "$this->schemapath/schema.xml";
    $this->documentfilename  = "$this->schemapath/document.xml";
    $this->finalpath         = CFile::$directory . "/hprim/$dirschemaname";
    
    $this->now               = time();
  }
  
  function schemaValidate($filename = null, $returnErrors = false, $display_errors = true) {
    if (!CAppUI::conf("hprimxml ".$this->evenement." validation")) {
      return true;
    }
    return parent::schemaValidate($filename, $returnErrors, $display_errors);
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
  
  function getAttSysteme() {
    $systeme = "système";
    
    return (CAppUI::conf("hprimxml ".$this->evenement." version") < "1.07") ? 
      $systeme : CMbString::removeDiacritics($systeme);
  }
  
  function addEnteteMessage($elParent) {
    $echg_hprim      = $this->_ref_echange_hprim;
    $dest            = $this->_ref_receiver;
    $identifiant     = $echg_hprim->_id ? str_pad($echg_hprim->_id, 6, '0', STR_PAD_LEFT) : "ES{$this->now}";
    $date_production = $echg_hprim->_id ? $echg_hprim->date_production : mbXMLDateTime();    
    
    $this->addAttribute($elParent, "acquittementAttendu", $dest->_configs["receive_ack"] ? "oui" : "non");
    
    $enteteMessage = $this->addElement($elParent, "enteteMessage");
    $this->addElement($enteteMessage, "identifiantMessage", $identifiant);
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction", $date_production);
    
    /* @todo MB toujours l'emetteur ? */
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $user = CAppUI::$user;
    $this->addAgent($agents, "acteur", "user$user->_id", $user->_view);
    $code_systeme = (CAppUI::conf('hprimxml code_transmitter_sender') == "finess") ? $group->finess : CAppUI::conf('mb_id');
    $this->addAgent($agents, $this->getAttSysteme(), $code_systeme, $group->text);
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    if ($dest->code_appli)
      $this->addAgent($agents, "application", $dest->code_appli, "");
    if ($dest->code_acteur)
      $this->addAgent($agents, "acteur", $dest->code_acteur, "");
    $this->addAgent($agents, $this->getAttSysteme(), $dest->code_syst, $dest->libelle);
  }
  
  function generateTypeEvenement($mbObject, $referent = null, $initiateur = null) {
    $echg_hprim = new CEchangeHprim();
    $echg_hprim->date_production = mbDateTime();
    $echg_hprim->sender_id       = $this->_ref_sender ? $this->_ref_sender->_id : null;
    $echg_hprim->receiver_id     = $this->_ref_receiver->_id;
    $echg_hprim->group_id        = $this->_ref_receiver->group_id;
    $echg_hprim->type            = $this->type;
    $echg_hprim->sous_type       = $this->sous_type;
    $echg_hprim->object_id       = $mbObject->_id;
    $echg_hprim->_message        = utf8_encode($this->saveXML());
    $echg_hprim->initiateur_id   = $initiateur;
    $echg_hprim->setObjectClassIdPermanent($mbObject);
    $echg_hprim->store();
    
    // Chargement des configs du destinataire
    $dest = $this->_ref_receiver;
    $dest->loadConfigValues();
    
    $this->_ref_echange_hprim = $echg_hprim;
            
    $this->generateEnteteMessage();
    $this->generateFromOperation($mbObject, $referent);

    $doc_valid = $this->schemaValidate();
    $echg_hprim->message_valide = $doc_valid ? 1 : 0;

    $this->saveTempFile();
    $msg = $this->saveXML();
    
    // On sauvegarde toujours en base le message en UTF-8
    $echg_hprim->_message = utf8_encode($msg);

    $echg_hprim->store();
    
    // On envoie le contenu et NON l'entête en UTF-8 si le destinataire est en UTF-8
    return ($dest->_configs["encoding"] == "UTF-8") ? utf8_encode($msg) : $msg;
  }
  
  function getIdSource($node, $valeur = true) {
    $xpath = new CHPrimXPath($this);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    
    if ($valeur) {
      // Obligatoire pour MB
      $emetteur = $xpath->queryUniqueNode("hprim:emetteur", $identifiant, false);
  
      return $xpath->queryTextNode("hprim:valeur", $emetteur);
    } else {
      return $xpath->queryTextNode("hprim:emetteur", $identifiant);
    }
  }
  
  function getIdCible($node, $valeur = true) {
    $xpath = new CHPrimXPath($this);
    
    $identifiant = $xpath->queryUniqueNode("hprim:identifiant", $node);
    
    if ($valeur) {
      $recepteur = $xpath->queryUniqueNode("hprim:recepteur", $identifiant);
      
      return $xpath->queryTextNode("hprim:valeur", $recepteur);
    } else {
      return $xpath->queryTextNode("hprim:recepteur", $identifiant);
    }
  }
  
  function getTagMediuser() {
    $this->_ref_echange_hprim->loadRefsInteropActor();
    
    return $this->_ref_echange_hprim->_ref_receiver->_tag_mediuser;
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
    $code = str_replace(" ", "", $code);
    $this->addTexte($codeLibelle, "code", $code, 10);
    if ($libelle) {
      $this->addTexte($codeLibelle, "libelle", $libelle, 35);
    }
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
    $this->addElement($elParent, "numeroAdeli", $mbMediuser->adeli);
    $identification = $this->addElement($elParent, "identification");
    $id400 = new CIdSante400();
    $id400->object_class = "CMediusers";
    $id400->object_id    = $mbMediuser->_id;
    $id400->tag          = $this->getTagMediuser();
    if ($id400->tag) {
      $id400->loadMatchingObject();
    }
    $this->addElement($identification, "code", $id400->_id ? $id400->id400 : "prat$mbMediuser->user_id");
    $this->addElement($identification, "libelle", $mbMediuser->_view);
    $personne = $this->addElement($elParent, "personne");
    $this->addElement($personne, "nomUsuel", $mbMediuser->_user_last_name);
    $prenoms = $this->addElement($personne, "prenoms");
    $this->addElement($prenoms, "prenom", $mbMediuser->_user_first_name);
  }
  
  function addActeCCAM($elParent, CActeCCAM $mbActeCCAM, CCodable $codable) {
    $acteCCAM = $this->addElement($elParent, "acteCCAM");
    $this->addAttribute($acteCCAM, "action", "création");
    $this->addAttribute($acteCCAM, "facturable", "oui");
    $this->addAttribute($acteCCAM, "valide", "oui");
    $this->addAttribute($acteCCAM, "documentaire", "non");
    $this->addAttribute($acteCCAM, "gratuit", "non");
    if($mbActeCCAM->_rembex) {
      $this->addAttribute($acteCCAM, "remboursementExceptionnel", "oui");
    }
    
    $identifiant = $this->addElement($acteCCAM, "identifiant");
    $emetteur    = $this->addElement($identifiant, "emetteur", "acte{$mbActeCCAM->_id}");
    
    $this->addElement($acteCCAM, "codeActe"    , $mbActeCCAM->code_acte);
    $this->addElement($acteCCAM, "codeActivite", $mbActeCCAM->code_activite);
    $this->addElement($acteCCAM, "codePhase"   , $mbActeCCAM->code_phase);
    
    // Date et heure de l'opération
    if ((CAppUI::conf("hprimxml date_heure_acte") == "operation") && $codable instanceof COperation) {
      $date  = $codable->date ? $codable->date : $codable->_ref_plageop->date;
      
      $time_operation = ($codable->time_operation == "00:00:00") ? null : $codable->time_operation;
      $heure = CValue::first(
                $codable->debut_op, 
                $codable->entree_salle, 
                $time_operation,
                $codable->horaire_voulu
              );
      
      $sejour = $codable->_ref_sejour;
      if ("$date $heure" < $sejour->entree) {
        $date  = mbDate($sejour->entree);
        $heure = mbTime($sejour->entree);
      }
      if ("$date $heure" > $sejour->sortie) {
        $date  = mbDate($sejour->sortie);
        $heure = mbTime($sejour->sortie);
      }
    }
    // Date et heure de l'exécution de l'acte
    else {
      $date  = mbDate($mbActeCCAM->execution);
      $heure = mbTime($mbActeCCAM->execution);
    }
    
    $execute = $this->addElement($acteCCAM, "execute");
    $this->addElement($execute, "date" , $date);
    $this->addElement($execute, "heure", $heure);
    
    $mbExecutant      = $mbActeCCAM->loadRefExecutant();
    $executant        = $this->addElement($acteCCAM, "executant");
    $medecins         = $this->addElement($executant, "medecins");
    $medecinExecutant = $this->addElement($medecins, "medecinExecutant");
    $this->addAttribute($medecinExecutant, "principal", "oui");
    $medecin          = $this->addElement($medecinExecutant, "medecin");
    $this->addProfessionnelSante($medecin, $mbExecutant);
    //$this->addUniteFonctionnelle($executant, $codable);
    
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
      if (CAppUI::conf("dPpmsi systeme_facturation") == "siemens") {
        if (CAppUI::conf("dPsalleOp CActeCCAM envoi_motif_depassement")) {
          $this->addAttribute($montantDepassement, "motif", "d");
        }         
      }
      else {
        if ($mbActeCCAM->motif_depassement) {
          $this->addAttribute($montantDepassement, "motif", $mbActeCCAM->motif_depassement);
        }
      }
    }
    
    return $acteCCAM;
  }
  
  function addActeNGAP($elParent, CActeNGAP $mbActeNGAP, CCodable $codable) {
    $acteNGAP = $this->addElement($elParent, "acteNGAP");
    $this->addAttribute($acteNGAP, "action", "création");
    
    // executionNuit
    if ($mbActeNGAP->complement == "N") {
      // non     non réalisé de nuit
      // 1t      réalisé 1re tranche de nuit
      // 2t      2me tranche
      // @todo Comment gérer ceci ?
      // $this->addAttribute($acteNGAP, "executionNuit", "oui");
    }
    
    // executionDimancheJourFerie
    if ($mbActeNGAP->complement == "F") {
       $this->addAttribute($acteNGAP, "executionDimancheJourFerie", "oui");
    }    
    
    $identifiant = $this->addElement($acteNGAP, "identifiant");
    $emetteur    = $this->addElement($identifiant, "emetteur", "acte{$mbActeNGAP->_id}");
    
    $this->addElement($acteNGAP, "lettreCle"    , $mbActeNGAP->code);
    $this->addElement($acteNGAP, "coefficient"  , $mbActeNGAP->demi ? $mbActeNGAP->coefficient * 0.5 : $mbActeNGAP->coefficient);
    // dénombrement doit être égale à 1 pour les actes ngap (CS, C etc....), 
    // elle varie seulement pour les actes de Biologie "dénombrement = nombre de code affinés"
   // $this->addElement($acteNGAP, "denombrement" , 1);
    $this->addElement($acteNGAP, "quantite"     , $mbActeNGAP->quantite);
    
    $execute = $this->addElement($acteNGAP, "execute");
    $mbActeNGAP->loadExecution();

    $this->addElement($execute, "date" , mbDate($mbActeNGAP->_execution));
    $this->addElement($execute, "heure", mbTime($mbActeNGAP->_execution));
    
    $mbExecutant      = $mbActeNGAP->_ref_executant;
    $prestataire      = $this->addElement($acteNGAP, "prestataire");
    $medecins         = $this->addElement($prestataire, "medecins");
    $medecin          = $this->addElement($medecins, "medecin");
    $this->addProfessionnelSante($medecin, $mbExecutant);
    
    $montant = $this->addElement($acteNGAP, "montant");
    if ($mbActeNGAP->montant_depassement > 0) {
      $montantDepassement = $this->addElement($montant, "montantDepassement", sprintf("%.2f", $mbActeNGAP->montant_depassement));
      if (CAppUI::conf("dPpmsi systeme_facturation") == "siemens") {
        if (CAppUI::conf("dPsalleOp CActeCCAM envoi_motif_depassement")) {
          $this->addAttribute($montantDepassement, "motif", "d");
        }         
      }
      else {
        if ($mbActeNGAP->motif_depassement) {
          $this->addAttribute($montantDepassement, "motif", $mbActeNGAP->motif_depassement);
        }
      }
    }
    
    return $acteNGAP;
  }
    
  function addActeCCAMAcquittement($elParent, $acteCCAM) {
    $mbActeCCAM = $acteCCAM["codeActe"];
    
    $this->addAttribute($elParent, "valide", "oui");
    
    $intervention = $this->addElement($elParent,     "intervention");
    $identifiant  = $this->addElement($intervention, "identifiant");
    $this->addElement($identifiant, "emetteur",  $acteCCAM["idCibleIntervention"]);
    $this->addElement($identifiant, "recepteur", $acteCCAM["idSourceIntervention"]);
    
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur",  $acteCCAM["idSourceActeCCAM"]);
    $this->addElement($identifiant, "recepteur", $acteCCAM["idCibleActeCCAM"]);
    
    
    $this->addElement($elParent, "codeActe",     $mbActeCCAM->code_acte);
    $this->addElement($elParent, "codeActivite", $mbActeCCAM->code_activite);
    $this->addElement($elParent, "codePhase",    $mbActeCCAM->code_phase);
    
    $execute = $this->addElement($elParent, "execute");
    $this->addElement($execute, "date",  mbDate($mbActeCCAM->execution));
    $this->addElement($execute, "heure", mbTime($mbActeCCAM->execution));    
  }
  
  function addPatient($elParent, $mbPatient, $referent = false, $light = false) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    if(!$referent) {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbPatient->_id, $referent);
      if($mbPatient->_IPP) 
        $this->addIdentifiantPart($identifiant, "recepteur", $mbPatient->_IPP, $referent);
    } else {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbPatient->_IPP, $referent);
      
      if(isset($mbPatient->_id400))
        $this->addIdentifiantPart($identifiant, "recepteur", $mbPatient->_id400, $referent);
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
      "mlle"  => "mlle",
      "m"     => "mr",
      "dr"    => "dr",
      "pr"    => "pr",
      "enf"   => "enf",
    );
      
    if ($mbPersonne instanceof CPatient) {
      $personne['nom'] = $mbPersonne->nom;
      $personne['nomNaissance'] = $mbPersonne->nom_jeune_fille;
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
    
    if (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["uppercase_fields"]) {
      $personne['nom']          = CMbString::upper($personne['nom']);
      $personne['nomNaissance'] = CMbString::upper($personne['nomNaissance']);
    }
    
    $this->addTexte($elParent, "nomUsuel", $personne['nom']);
    $this->addTexte($elParent, "nomNaissance", $personne['nomNaissance']);
    $prenoms = $this->addElement($elParent, "prenoms");
    foreach ($personne['prenoms'] as $key => $prenom) {
      if ($key == 0) {
        if (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["uppercase_fields"]) {
          $prenom = CMbString::upper($prenom ? $prenom : $personne['nom']);
        }
        $this->addTexte($prenoms, "prenom", $prenom ? $prenom : $personne['nom']);
      } else {
        if (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["uppercase_fields"]) {
          $prenom = CMbString::upper($prenom);
        }
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
    $this->addTexte($adresse, "ligne", substr(preg_replace("/[^0-9a-zàáâãäåòóôõöøèéêëçìíîïùúûüÿñ-]/i", " ", $personne['ligne']), 0, 35));
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
    $observation = $this->addObservation($observations, $code, $libelle, CMbString::convertHTMLToXMLEntities($commentaires));   
  }
  
  function addObservation($elParent, $code, $libelle, $commentaires = null) {
    $observation = $this->addElement($elParent, "observation");
    
    $this->addElement($observation, "code", substr($code, 0, 17));
    $this->addElement($observation, "libelle", substr($libelle, 0, 80));
    $this->addElement($observation, "commentaire", substr($commentaires, 0, 4000)); 
  }
  
  function addReponse($elParent, $statut, $codes, $mbObject = null, $commentaires = null) {
    if ($statut == "ok") {
      return; 
    }  
    
    $erreur = $this->addElement($elParent, "erreur");
    
    $libelle = null;
    if (is_array($codes)) {
      $code = implode("", $codes);
      foreach ($codes as $_code) {
        $libelle .= CAppUI::tr("hprimxml-error-$_code");
      }
    } 
    else {
      $code    = $codes;
      $libelle = CAppUI::tr("hprimxml-error-$code");
    }
    $this->addElement($erreur, "code"       , substr($code, 0, 17));
    $this->addElement($erreur, "libelle"    , substr($libelle, 0, 80));
    $this->addElement($erreur, "commentaire", substr("$libelle : \"$commentaires\"", 0, 4000)); 
  }
  
  function addReponseCCAM($elParent, $statut, $codes, $acteCCAM, $mbObject = null, $commentaires = null) {
    $reponse = $this->addElement($elParent, "reponse");
    $this->addAttribute($reponse, "statut", $statut);
      
    $elActeCCAM = $this->addElement($reponse, "acteCCAM");
    $this->addActeCCAMAcquittement($elActeCCAM, $acteCCAM);
    
    $this->addReponse($reponse, $statut, $codes);
  }
  
  function addReponseIntervention($elParent, $statut, $codes, $acteCCAM, $mbObject = null, $commentaires = null) {
    $reponse = $this->addElement($elParent, "reponse");
    $this->addAttribute($reponse, "statut", $statut);
      
    $intervention = $this->addElement($reponse, "intervention");
    $this->addInterventionAcquittement($intervention, $mbObject);
    
    $this->addReponse($reponse, $statut, $codes, $mbObject, $commentaires);
  }
  
  function addInterventionAcquittement($elParent, $operation = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", isset($operation->_id) ? $operation->_id : 0);
  }
  
  function getTypeEvenementPatient() {
    $xpath = new CHPrimXPath($this);
        
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
    $id400->tag          = $this->getTagMediuser();
    $this->addElement($identification, "code", $id400->loadMatchingObject() ? $id400->id400 : $praticien->_id);
    $this->addElement($identification, "libelle", $praticien->_view);
    $personne = $this->addElement($medecin, "personne");
    $this->addPersonne($personne, $praticien);
  }
  
  function addMedecinResponsable($elParent, $praticien) {
    $medecinResponsable = $this->addElement($elParent, "medecinResponsable");
    $this->addElement($medecinResponsable, "numeroAdeli", $praticien->adeli);
    $identification = $this->addElement($medecinResponsable, "identification");
    $id400 = new CIdSante400();
    $id400->object_class = "CMediusers";
    $id400->object_id    = $praticien->_id;
    $id400->tag          = $this->getTagMediuser();
    $this->addElement($identification, "code", $id400->loadMatchingObject() ? $id400->id400 : $praticien->_id);
    $this->addElement($identification, "libelle", $praticien->_view);
    $personne = $this->addElement($medecinResponsable, "personne");
    $this->addPersonne($personne, $praticien);
  }
  
  function addVenue($elParent, CSejour $mbVenue, $referent = false, $light = false) {
    if (!$light) {
      // Ajout des attributs du séjour
      $this->addAttribute($elParent, "confidentiel", "non");
      // Etat d'une venue : encours, clôturée ou préadmission
      $etatConversion = array (
        "preadmission" => "préadmission",
        "encours"  => "encours",
        "cloture" => "clôturée"
      );
      $this->addAttribute($elParent, "etat", $etatConversion[$mbVenue->_etat]);
      $this->addAttribute($elParent, "facturable", ($mbVenue->facturable)  ? "oui" : "non");
      $this->addAttribute($elParent, "declarationMedecinTraitant", ($mbVenue->_adresse_par_prat)  ? "oui" : "non");
    }
    
    $identifiant = $this->addElement($elParent, "identifiant");
    
    if (!$referent) {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->sejour_id, $referent);
      if($mbVenue->_NDA)
        $this->addIdentifiantPart($identifiant, "recepteur", $mbVenue->_NDA, $referent);
    } else {
      $this->addIdentifiantPart($identifiant, "emetteur",  $mbVenue->_NDA, $referent);
      
      if(isset($mbVenue->_id400))
        $this->addIdentifiantPart($identifiant, "recepteur", $mbVenue->_id400, $referent);
    }  
    
    $natureVenueHprim = $this->addElement($elParent, "natureVenueHprim");
    
    $attrNatureVenueHprim = array (
      "comp"    => "hsp",
      "ambu"    => ((CAppUI::conf("hprimxml evt_patients version") == "1.053") || 
                    (CAppUI::conf("hprimxml evt_patients version") == "1.07")) ? 
                      "ambu" : "hsp",
      "urg"     => "hsp",
      "psy"     => "hsp",
      "ssr"     => "hsp",
      "exte"    => ((CAppUI::conf("hprimxml evt_patients version") == "1.053") || 
                    (CAppUI::conf("hprimxml evt_patients version") == "1.07")) ?
                      "exte" : "hsp",
      "consult" => "cslt",
      "seances" => "sc"
    );
    $this->addAttribute($natureVenueHprim, "valeur", $attrNatureVenueHprim[$mbVenue->type]);
    
    $entree = $this->addElement($elParent, "entree");
    
    $dateHeureOptionnelle = $this->addElement($entree, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->entree));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->entree));
    
    $modeEntree = $this->addElement($entree, "modeEntree");
    // mode d'entrée inconnu
    $mode = "09";
    // admission après consultation d'un médecin de l'établissement
    if ($mbVenue->_ref_consult_anesth && $mbVenue->_ref_consult_anesth->_id) {
      $mode = "01";
    }
    // malade envoyé par un médecin extérieur
    if ($mbVenue->_ref_adresse_par_prat && $mbVenue->_ref_adresse_par_prat->_id) {
      $mode = "02";
    }
    $this->addAttribute($modeEntree, "valeur", $mode);
    
    if (!$light) {    
      $medecins = $this->addElement($elParent, "medecins");
      
      // Traitement du medecin traitant du patient
      $_ref_medecin_traitant = $mbVenue->_ref_patient->_ref_medecin_traitant;
      if ($_ref_medecin_traitant && $_ref_medecin_traitant->_id) {
        if ($_ref_medecin_traitant->adeli) {
          $this->addMedecin($medecins, $_ref_medecin_traitant, "trt");
        }
      }
      
      // Traitement du medecin adressant
      $_ref_adresse_par_prat = $mbVenue->_ref_adresse_par_prat;
      if ($mbVenue->_adresse_par_prat) {
        if ($_ref_adresse_par_prat && $_ref_adresse_par_prat->adeli) {
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
    
    // Cas dans lequel on transmet pas de sortie tant que l'on a pas la sortie réelle
    if (!$mbVenue->sortie_reelle && (isset($this->_ref_receiver->_id) && $this->_ref_receiver->_configs["send_sortie_prevue"] == 0)) {
      return;
    }  
    
    $sortie = $this->addElement($elParent, "sortie");
    $dateHeureOptionnelle = $this->addElement($sortie, "dateHeureOptionnelle");
    $this->addElement($dateHeureOptionnelle, "date", mbDate($mbVenue->sortie));
    $this->addElement($dateHeureOptionnelle, "heure", mbTime($mbVenue->sortie)); 
    
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
      // mutation
      else if ($mbVenue->mode_sortie == "mutation") {
        $modeSortieEtablissementHprim = "08";
      }
      // autre transfert dans un autre CH
      else if ($mbVenue->mode_sortie == "transfert") {
        $modeSortieEtablissementHprim = "02";
      }
      $this->addElement($modeSortieHprim, "code", $modeSortieEtablissementHprim);
      $this->addElement($modeSortieHprim, "libelle", $mbVenue->mode_sortie);
      
      if ($mbVenue->etablissement_sortie_id) {
        $destination = $this->addElement($modeSortieHprim, "destination");
        $this->addElement($destination, "libelle", $mbVenue->etablissement_sortie_id);
      }
        
      $this->addAttribute($modeSortieHprim, "valeur", $modeSortieEtablissementHprim);
    }      
    
    // @todo Voir comment intégrer le placement pour la v. 1.01 et v. 1.05
    /*
    if (!$light) {
      $placement = $this->addElement($elParent, "Placement");
      $modePlacement = $this->addElement($placement, "modePlacement");
      $this->addAttribute($modePlacement, "modaliteHospitalisation", $mbVenue->modalite);
      $this->addElement($modePlacement, "libelle", substr($mbVenue->_view, 0, 80));   
      
      $datePlacement = $this->addElement($placement, "datePlacement");
      $this->addElement($datePlacement, "date", mbDate($mbVenue->entree));
    }*/
  }
  
  function addIntervention($elParent, COperation $operation, $referent = null, $light = false) {
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", $operation->_id);
    $last_idex = $operation->_ref_last_id400;
    if (isset($last_idex->_id)) {
      $this->addElement($identifiant, "recepteur", $last_idex->id400);
    }
      
    $sejour = $operation->loadRefSejour();
    
    $mbOpDate = CValue::first(
      $operation->_ref_plageop->date,
      $operation->date
    );
    
    $time_operation = ($operation->time_operation == "00:00:00") ? null : $operation->time_operation;
    $mbOpHeureDebut = CValue::first(
      $operation->debut_op, 
      $operation->entree_salle, 
      $time_operation,
      $operation->horaire_voulu
    );
    $mbOpDebut = CMbRange::forceInside($sejour->entree, $sejour->sortie, "$mbOpDate $mbOpHeureDebut");
    
    $mbOpHeureFin = CValue::first(
      $operation->fin_op, 
      $operation->sortie_salle, 
      mbAddTime($operation->temp_operation, $time_operation ? $time_operation : $operation->horaire_voulu)
    );
    $mbOpFin = CMbRange::forceInside($sejour->entree, $sejour->sortie, "$mbOpDate $mbOpHeureFin");

    $debut = $this->addElement($elParent, "debut");
    $this->addElement($debut, "date" , mbDate($mbOpDebut));
    $this->addElement($debut, "heure", mbTime($mbOpDebut));
    
    $fin = $this->addElement($elParent, "fin");
    $this->addElement($fin, "date" , mbDate($mbOpFin));
    $this->addElement($fin, "heure", mbTime($mbOpFin));
    
    $this->addUniteFonctionnelle($elParent, $operation);
    
    if ($light) {
      // Ajout des participants
      $mbParticipants = array();
      foreach($operation->_ref_actes_ccam as $acte_ccam) {
        $acte_ccam->loadRefExecutant();
        $mbParticipant = $acte_ccam->_ref_executant;
        $mbParticipants[$mbParticipant->user_id] = $mbParticipant;
      }
      
      $participants = $this->addElement($elParent, "participants");
      foreach ($mbParticipants as $mbParticipant) {
        $participant = $this->addElement($participants, "participant");
        $medecin     = $this->addElement($participant, "medecin");
        $this->addProfessionnelSante($medecin, $mbParticipant);
      }
        
      // Libellé de l'opération
      $this->addTexte($elParent, "libelle", $operation->libelle, 80);
    }
    else { 
      // Uniquement le responsable de l’'intervention
      $participants = $this->addElement($elParent, "participants");
      $participant  = $this->addElement($participants, "participant");
      $medecin      = $this->addElement($participant, "medecin");
      $this->addProfessionnelSante($medecin, $operation->loadRefChir());
        
      // Libellé de l'opération
      $this->addTexte($elParent, "libelle", $operation->libelle, 80);
    
      // Remarques sur l'opération
      $this->addTexte($elParent, "commentaire", $operation->rques, 4000);
      
      // Conventionnée ?
      $this->addElement($elParent, "convention", $operation->conventionne ? 1 : 0);
      
      // TypeAnesthésie : nomemclature externe (idex)
      if ($operation->type_anesth) {
        $tag_hprimxml   = $this->_ref_receiver->_tag_hprimxml;
        $idexTypeAnesth = CIdSante400::getMatch("CTypeAnesth", $tag_hprimxml, null, $operation->type_anesth);
        $this->addElement($elParent, "typeAnesthesie", $idexTypeAnesth->id400);
      }
      
      // Indicateurs
      $indicateurs = $this->addElement($elParent, "indicateurs");
      $dossier_medical = new CDossierMedical();
      $dossier_medical->object_class = "CPatient";
      $dossier_medical->object_id    = $operation->loadRefPatient()->_id;
      $dossier_medical->loadMatchingObject();

      $antecedents = $dossier_medical->loadRefsAntecedents();
      foreach ($antecedents as $_antecedent) {
        $this->addCodeLibelle($indicateurs, "indicateur", $_antecedent->_id, CMbString::convertHTMLToXMLEntities($_antecedent->rques));
      }
      
      // Recours / Durée USCPO
      $this->addElement($elParent, "recoursUscpo", $operation->duree_uscpo ? 1 : 0);
      $this->addElement($elParent, "dureeUscpo"  , $operation->duree_uscpo ? $operation->duree_uscpo : null);
      
      // Côté (droit|gauche|bilatéral|total|inconnu)
      // D - Droit
      // G - Gauche
      // B - Bilatéral
      // T - Total
      // I - Inconnu
      $cote = array (
        "droit"     => "D",
        "gauche"    => "G",
        "bilatéral" => "B",
        "total"     => "T",
        "inconnu"   => "I"
      );
      
      $this->addCodeLibelle($elParent, "cote", $cote[$operation->cote], CMbString::capitalize($operation->cote));
    }
  }
  
  function addDebiteurs($elParent, CPatient $mbPatient, $referent = null) {
    $debiteur = $this->addElement($elParent, "debiteur");
    
    $assurance = $this->addElement($debiteur, "assurance");
    $this->addAssurance($assurance, $mbPatient, $referent);
  }
  
  function addAssurance($elParent, CPatient $mbPatient, $referent = null) {
    $identifiant = $this->addElement($elParent, "identifiant");
    
    $this->addElement($elParent, "nom", $mbPatient->regime_sante);
    
    $assure = $this->addElement($elParent, "assure");
    $this->addAssure($assure, $mbPatient);
    
    if ($mbPatient->deb_amo && $mbPatient->fin_amo) {
      $dates = $this->addElement($elParent, "dates");
      $this->addElement($dates, "dateDebutDroit", mbDate($mbPatient->deb_amo));
      $this->addElement($dates, "dateFinDroit", mbDate($mbPatient->fin_amo));
    }
    
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
  
  function addAssure($elParent, CPatient $mbPatient) {
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
    $assureNaissance = $mbPatient->assure_naissance ? $mbPatient->assure_naissance : $mbPatient->naissance;
    $this->addElement($dateNaissance, isLunarDate($assureNaissance) ? "dateLunaire" : "date", $assureNaissance);
    
    $this->addElement($elParent, "lienAssure", $mbPatient->rang_beneficiaire);
  }
  
  function addSaisieDelocalisee($elParent, CSejour $mbSejour) {
    $this->addAttribute($elParent, "action", "création");
    $this->addDateTimeElement($elParent, "dateAction");
    $dateHeureOptionnelle = $this->addElement($elParent, "dateHeureReference");
    $this->addDateHeure($dateHeureOptionnelle);

    $mbOp = reset($mbSejour->_ref_operations);
    
    // Identifiant de l'intervention
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", $mbOp->_id);
    
    $this->addUniteFonctionnelleResponsable($elParent, $mbOp);
    
    // Médecin responsable
    $medecinResponsable = $this->addElement($elParent, "medecinResponsable");
    $mbPraticien =& $mbSejour->_ref_praticien;
    $this->addElement($medecinResponsable, "numeroAdeli", $mbPraticien->adeli);
    $this->addAttribute($medecinResponsable, "lien", "rsp");
    $this->addCodeLibelle($medecinResponsable, "identification", "prat$mbPraticien->user_id", $mbPraticien->_user_username);
    
    // Diagnostics RUM
    $diagnosticsRum = $this->addElement($elParent, "diagnosticsRum");
    $diagnosticPrincipal = $this->addElement($diagnosticsRum, "diagnosticPrincipal");
    $this->addElement($diagnosticPrincipal, "codeCim10", strtoupper($mbSejour->DP));
    if ($mbSejour->DR) {
      $diagnosticRelie = $this->addElement($diagnosticsRum, "diagnosticRelie");
      $this->addElement($diagnosticRelie, "codeCim10", strtoupper($mbSejour->DR));
    }
    if (count($mbSejour->_ref_dossier_medical->_codes_cim)) {
      $diagnosticsSignificatifs = $this->addElement($diagnosticsRum, "diagnosticsSignificatifs");
      foreach ($mbSejour->_ref_dossier_medical->_codes_cim as $curr_code) {
        $diagnosticSignificatif = $this->addElement($diagnosticsSignificatifs, "diagnosticSignificatif");
        $this->addElement($diagnosticSignificatif, "codeCim10", strtoupper($curr_code));
      }
    }
  }
  
  function addSsr($elParent, CSejour $mbSejour) {    
    // Identifiant du séjour
    $identifiant = $this->addElement($elParent, "identifiantSSR");
    $this->addElement($identifiant, "emetteur", $mbSejour->_id);
    
    $mbRhss = CRHS::getAllRHSsFor($mbSejour);
    foreach ($mbRhss as $_mbRhs) {
      $_mbRhs->loadRefSejour();
      $rhs = $this->addElement($elParent, "rhs");
      $this->addRhs($rhs, $mbSejour, $_mbRhs);
    }
  }
  
  function addRhs($elParent, CSejour $mbSejour, CRHS $mbRhs) {    
    $this->addAttribute($elParent, "action", "création");
    $this->addAttribute($elParent, "version", "M01");
    
    $this->addElement($elParent, "dateAction", mbXMLDateTime());
    
    // Identifiant du séjour
    $identifiant = $this->addElement($elParent, "identifiant");
    $this->addElement($identifiant, "emetteur", $mbRhs->_id);
    
    $dateHeureOptionnelleLundi = $this->addElement($elParent, "dateHeureOptionnelleLundi");
    $this->addElement($dateHeureOptionnelleLundi, "date", $mbRhs->date_monday);
    
    // @todo Voir pour mettre sur un plateau
    $this->addCodeLibelle($elParent, "uniteMedicale", CGroups::loadCurrent()->_id, CGroups::loadCurrent()->_view); 
    
    $joursPresence = $this->addElement($elParent, "joursPresence");
    if ($mbRhs->_in_bounds) {
      $this->addJoursPresence($joursPresence, $mbRhs);
    }
    
    $diagnostics = $this->addElement($elParent, "diagnostics");
    
    $actesReeducation = $this->addElement($elParent, "actesReeducation");
    $this->addActesReeducation($actesReeducation, $mbRhs);
    
    $dependances = $this->addElement($elParent, "dependances");
    $this->addDependances($dependances, $mbRhs);
  }
  
  function addJoursPresence($elParent, CRHS $mbRhs) {
    if ($mbRhs->_in_bounds_mon) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "lundi");
    }
    if ($mbRhs->_in_bounds_tue) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "mardi");
    }
    if ($mbRhs->_in_bounds_wed) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "mercredi");
    }
    if ($mbRhs->_in_bounds_thu) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "jeudi");
    }
    if ($mbRhs->_in_bounds_fri) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "vendredi");
    }
    if ($mbRhs->_in_bounds_sat) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "samedi");
    }
    if ($mbRhs->_in_bounds_sun) {
      $jourPresence = $this->addElement($elParent, "jourPresence");
      $this->addAttribute($jourPresence, "jour", "dimanche");
    }
  }
  
  function addDependances($elParent, CRHS $mbRhs) {
    $mbRhs->loadRefDependances();
    $dependances = $mbRhs->_ref_dependances;
    $this->addElement($elParent, "habillage"   , $dependances->habillage);
    $this->addElement($elParent, "deplacement" , $dependances->deplacement);
    $this->addElement($elParent, "alimentation", $dependances->alimentation);
    $this->addElement($elParent, "continence"  , $dependances->continence);
    $this->addElement($elParent, "comportement"  , $dependances->comportement);
    $this->addElement($elParent, "relation"    , $dependances->relation);
  }
  
  function addActesReeducation($elParent, CRHS $mbRhs) {
    $mbRhs->loadRefLignesActivites();
    $lignes = $mbRhs->_ref_lignes_activites;
    
    // Ajout des actes de rééducation
    foreach ($lignes as $_ligne) {
      $this->addActeReeducation($elParent, $_ligne);
    }
    
    // Ajout des chapitres de rééducation
    //$this->addChapitreActeReeducation($elParent, $mbRhs);
  }
  
  function addActeReeducation($elParent, CLigneActivitesRHS $ligneActiviteRhs) {
    $acteReeducation = $this->addElement($elParent, "acteReeducation");

    $this->addElement($acteReeducation, "codeCDARR", $ligneActiviteRhs->code_activite_cdarr);
    $this->addElement($acteReeducation, "duree", $ligneActiviteRhs->_qty_total);
  }
  
  function addChapitreActeReeducation($elParent, CRHS $mbRhs) {
    $totauxType = array();
    $totauxType = $mbRhs->countTypeActivite();
    
    foreach ($totauxType as $mnemonique => $_total_type) {
      if ($_total_type) {
        $chapitreActeReeducation = $this->addElement($elParent, "chapitreActeReeducation");
    
        $this->addAttribute($chapitreActeReeducation, "mnemonique", strtolower($mnemonique));
        
        $this->addElement($chapitreActeReeducation, "duree", $_total_type);
        $this->addElement($chapitreActeReeducation, "commentaire", CActiviteCdARR::getLibelle($mnemonique));
      }
    }
  }
  
  function addDiagnosticsEtat($elParent, CSejour $mbSejour) {
    $this->addDiagnosticEtat($elParent, strtoupper($mbSejour->DP), "dp");
    if ($mbSejour->DR) {
      $this->addDiagnosticEtat($elParent, strtoupper($mbSejour->DR), "dr");
    }
    
    $mbSejour->loadRefDossierMedical();
    if (count($mbSejour->_ref_dossier_medical->_codes_cim)) {
      foreach($mbSejour->_ref_dossier_medical->_codes_cim as $_diag_significatif) {
        $this->addDiagnosticEtat($elParent, strtoupper($_diag_significatif), "ds");
      }
    }
  }
  
  function addDiagnosticEtat($elParent, $codeCim10, $typeDiagnostic) {
    $diagnostic = $this->addElement($elParent, "diagnostic");
    $this->addAttribute($diagnostic, "action", "création");
    $this->addAttribute($diagnostic, "type", $typeDiagnostic);
    
    $this->addElement($diagnostic, "codeCim10", $codeCim10);
  }
  
  function addFraisDivers($elParent, CFraisDivers $mbFraisDivers) {
    $fraisDivers = $this->addElement($elParent, "FraisDivers");
    
    // Action réalisée
    $this->addAttribute($fraisDivers, "action", "création");
    //Produit facturable
    $this->addAttribute($fraisDivers, "facturable", "oui");
    
    // Date de l'événement
    $this->addDateTimeElement($fraisDivers, "dateAction");
    
    // Acteur déclencheur de cet action dans l'application créatrice.
    $mbExecutant = $mbFraisDivers->_ref_executant;
    $acteur = $this->addElement($fraisDivers, "acteur");
    $this->addProfessionnelSante($acteur, $mbExecutant);
    
    // Correspond à l'identification de la ligne de saisie
    $identifiant = $this->addElement($fraisDivers, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", "$mbFraisDivers->_id");
    
    // Lettre clé
    $this->addElement($fraisDivers, "lettreCle"  , $mbFraisDivers->_ref_type->code);
    // Coefficient
    $this->addElement($fraisDivers, "coefficient", $mbFraisDivers->coefficient);
    // Quantité de produits
    $this->addElement($fraisDivers, "quantite"   , $mbFraisDivers->quantite);

    // Date d'execution
    $execute = $this->addElement($fraisDivers, "execute");
    $this->addDateHeure($execute, $mbFraisDivers->_execution);

    // Montant des frais
    $montant = $this->addElement($fraisDivers, "montant");
    $this->addTypeMontant($montant, $mbFraisDivers);
  }
  
  function addTypeMontant($elParent, CFraisDivers $mbFraisDivers) {
    $this->addElement($elParent, "total", $mbFraisDivers->montant_base);
  }
  
  function addMouvement($elParent, CAffectation  $mbAffectation, $referent = null) {
    $mouvement = $this->addElement($elParent, "mouvement");
    
    // Correspond à l'identification de l'affectation
    $identifiant = $this->addElement($mouvement, "identifiant");
    $emetteur = $this->addElement($identifiant, "emetteur", "$mbAffectation->_id");
    
    // Traitement du médecin responsable du séjour
    $this->addMedecinResponsable($mouvement, $mbAffectation->_ref_sejour->_ref_praticien);
    
    // Debut de l'affectation
    $debut = $this->addElement($mouvement, "debut");
    $this->addDateHeure($debut, $mbAffectation->entree);
    
    // Fin de l'affectation
    $fin = $this->addElement($mouvement, "fin");
    $this->addDateHeure($fin, $mbAffectation->sortie);

    $unitesFonctionnellesResponsables = $this->addElement($mouvement, "unitesFonctionnellesResponsables");
    $uniteFonctionnelleResponsable = $this->addElement($unitesFonctionnellesResponsables, "uniteFonctionnelleResponsable");
    $this->addAttribute($uniteFonctionnelleResponsable, "responsabilite", "m");
    $service = $mbAffectation->_ref_lit->_ref_chambre->_ref_service;
    $id400Patient = new CIdSante400();
    $id400Patient->loadLatestFor($service, $this->_ref_receiver->_tag_service);
    $this->addTexte($uniteFonctionnelleResponsable, "code", $id400Patient->id400 ? $id400Patient->id400 : $service->_id, 10);
    $this->addTexte($uniteFonctionnelleResponsable, "libelle", $service->_view, 35);
  }
  
}

?>