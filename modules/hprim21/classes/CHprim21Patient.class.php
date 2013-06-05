<?php

/**
 * The HPRIM 2.1 patient class declaration
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * The HPRIM 2.1 patient class
 */
class CHprim21Patient extends CHprim21Object {
  // DB Table key
  public $hprim21_patient_id;
  
  // DB references
  public $patient_id;
  
  // Patient DB Fields
  public $nom;
  public $prenom;
  public $prenom2;
  public $alias;
  public $civilite;
  public $diplome;
  public $nom_jeune_fille;
  public $nom_soundex2;
  public $prenom_soundex2;
  public $nomjf_soundex2;
  public $naissance;
  public $sexe;
  public $adresse1;
  public $adresse2;
  public $ville;
  public $departement;
  public $cp;
  public $pays;
  public $telephone1;
  public $telephone2;
  public $traitement_local1;
  public $traitement_local2;
  public $taille;
  public $poids;
  public $diagnostic;
  public $traitement;
  public $regime;
  public $commentaire1;
  public $commentaire2;
  public $classification_diagnostic;
  public $situation_maritale;
  public $precautions;
  public $langue;
  public $statut_confidentialite;
  public $date_derniere_modif;
  public $date_deces;
  
  // Assuré primaire DB Fields
  public $nature_assurance;
  public $debut_validite;
  public $fin_validite;
  public $matricule;
  public $rang_beneficiaire;
  public $rang_naissance;
  public $code_regime;
  public $caisse_gest;
  public $centre_gest;
  public $origine_droits;
  public $nature_exoneration;
  public $nom_assure;
  public $prenom_assure;
  public $nom_jeune_fille_assure;
  public $taux_PEC;
  public $numero_AT;
  public $AT_par_tiers;
  public $fin_droits;
  public $date_accident;
  public $nom_employeur;
  public $adresse1_employeur;
  public $adresse2_employeur;
  public $ville_employeur;
  public $departement_employeur;
  public $cp_employeur;
  public $pays_employeur;
  public $date_debut_grossesse;
  
  public $_ref_patient;
  public $_ref_hprim21_sejours;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_patient';
    $spec->key   = 'hprim21_patient_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specsParent = parent::getProps();
    $specs = array (
      // Patient
      "patient_id"                => "ref class|CPatient",
      "nom"                       => "str notNull seekable",
      "prenom"                    => "str seekable",
      "prenom2"                   => "str",
      "alias"                     => "str",
      "civilite"                  => "enum list|M|Mme|Mlle",
      "diplome"                   => "str",
      "nom_jeune_fille"           => "str",
      "nom_soundex2"              => "str",
      "prenom_soundex2"           => "str",
      "nomjf_soundex2"            => "str",
      "naissance"                 => "birthDate",
      "sexe"                      => "enum list|M|F|U",
      "adresse1"                  => "str",
      "adresse2"                  => "str",
      "ville"                     => "str",
      "departement"               => "str",
      "cp"                        => "str",
      "pays"                      => "str",
      "telephone1"                => "str",
      "telephone2"                => "str",
      "traitement_local1"         => "str",
      "traitement_local2"         => "str",
      "taille"                    => "num",
      "poids"                     => "num",
      "diagnostic"                => "str",
      "traitement"                => "str",
      "regime"                    => "str",
      "commentaire1"              => "str",
      "commentaire2"              => "str",
      "classification_diagnostic" => "str",
      "situation_maritale"        => "enum list|M|S|D|W|A|U",
      "precautions"               => "str",
      "langue"                    => "str",
      "statut_confidentialite"    => "str",
      "date_derniere_modif"       => "dateTime",
      "date_deces"                => "date",
      // Assuré primaire
      "nature_assurance"       => "str",
      "debut_validite"         => "date",
      "fin_validite"           => "date",
      "matricule"              => "code insee",
      "rang_beneficiaire"      => "enum list|01|02|09|11|12|13|14|15|16|31",
      "rang_naissance"         => "enum list|1|2|3|4|5|6 default|1",
      "code_regime"            => "numchar maxLength|3",
      "caisse_gest"            => "numchar length|3",
      "centre_gest"            => "numchar length|4",
      "origine_droits"         => "str",
      "nature_exoneration"     => "str",
      "nom_assure"             => "str",
      "prenom_assure"          => "str",
      "nom_jeune_fille_assure" => "str",
      "taux_PEC"               => "float",
      "numero_AT"              => "num",
      "AT_par_tiers"           => "bool",
      "fin_droits"             => "date",
      "date_accident"          => "date",
      "nom_employeur"          => "str",
      "adresse1_employeur"     => "str",
      "adresse2_employeur"     => "str",
      "ville_employeur"        => "str",
      "departement_employeur"  => "str",
      "cp_employeur"           => "str",
      "pays_employeur"         => "str",
      "date_debut_grossesse"   => "date",
    );
    return array_merge($specsParent, $specs);
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["hprim21_complementaires"] = "CHprim21Complementaire hprim21_patient_id";
    $backProps["hprim21_sejours"]         = "CHprim21Sejour hprim21_patient_id";
    return $backProps;
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    
    parent::updatePlainFields();
     
    $soundex2 = new soundex2;
    if ($this->nom) {
      $this->nom = strtoupper($this->nom);
      $this->nom_soundex2 = $soundex2->build($this->nom);
    }
    
    if ($this->nom_jeune_fille) {
      $this->nom_jeune_fille = strtoupper($this->nom_jeune_fille);
      $this->nomjf_soundex2 = $soundex2->build($this->nom_jeune_fille);
    }

    if ($this->prenom) {
      $this->prenom = ucwords(strtolower($this->prenom));
      $this->prenom_soundex2 = $soundex2->build($this->prenom);
    }
  }
  
  function bindToLine($line, &$reader) {
    $this->setHprim21ReaderVars($reader);
    
    $elements                        = explode($reader->separateur_champ, $line);
  
    if (count($elements) < 34) {
      $reader->error_log[] = "Champs manquant dans le segment patient : ".count($elements)." champs trouvés";
      return false;
    }
    
    $identifiant                     = explode($reader->separateur_sous_champ, $elements[2]);
    if (!$identifiant[0]) {
      $reader->error_log[] = "Identifiant externe manquant dans le segment patient";
      return false;
    }
    $this->external_id               = $identifiant[0];
    $this->loadMatchingObject();
    $identite                        = explode($reader->separateur_sous_champ, $elements[5]);
    $this->nom                       = $identite[0];
    $this->prenom                    = $identite[1];
    $this->prenom2                   = $identite[2];
    $this->alias                     = $identite[3];
    $this->civilite                  = $identite[4];
    $this->diplome                   = $identite[5];
    $this->nom_jeune_fille           = $elements[6];
    $this->naissance                 = $this->getDateFromHprim($elements[7]);
    $this->sexe                      = $elements[8];
    $adresse                         = explode($reader->separateur_sous_champ, $elements[10]);
    $this->adresse1                  = $adresse[0];
    $this->adresse1                  = $adresse[1];
    $this->ville                     = $adresse[2];
    $this->departement               = $adresse[3];
    $this->cp                        = $adresse[4];
    $this->pays                      = $adresse[5];
    $telephone                       = explode($reader->repetiteur, $elements[12]);
    $this->telephone1                = $telephone[0];
    if (isset($telephone[2])) {
      $this->telephone2              = $telephone[1];
    }
    $this->traitement_local1         = $elements[14];
    $this->traitement_local2         = $elements[15];
    $this->taille                    = $elements[16];
    $this->poids                     = $elements[17];
    $this->diagnostic                = $elements[18];
    $this->traitement                = $elements[19];
    $this->regime                    = $elements[20];
    $this->commentaire1              = $elements[21];
    $this->commentaire2              = $elements[22];
    $this->classification_diagnostic = $elements[26];
    $this->situation_maritale        = $elements[28];
    $this->precautions               = $elements[29];
    $this->langue                    = $elements[30];
    $this->statut_confidentialite    = $elements[31];
    $this->date_derniere_modif       = $this->getDateTimeFromHprim($elements[32]);
    $this->date_deces                = $this->getDateFromHprim($elements[33]);
    $reader->nb_patients++;
    return true;
  }
  
  function bindAssurePrimaireToLine($line, &$reader) {
    $elements = explode($reader->separateur_champ, $line);
  
    if (count($elements) < 22) {
      $this->error_log[] = "Champs manquant dans le segment assuré primaire";
      return false;
    }
    
    $this->nature_assurance       = $elements[2];
    $this->debut_validite         = $this->getDateFromHprim($elements[3]);
    $this->fin_validite           = $this->getDateFromHprim($elements[4]);
    $this->matricule              = $elements[5] == "00" ? ""   : $elements[5];
    $this->rang_beneficiaire      = $elements[5] == "00" ? "01" : $elements[5];
    $this->rang_naissance         = $elements[5] == "0"  ? "1"  : $elements[5];
    $this->code_regime            = $elements[8];
    $this->caisse_gest            = $elements[9];
    $this->centre_gest            = $elements[10];
    $this->origine_droits         = $elements[11];
    $this->nature_exoneration     = $elements[12];
    $identite                     = explode($reader->separateur_sous_champ, $elements[13]);
    $this->nom_assure             = $identite[0];
    $this->prenom_assure          = $identite[1];
    $this->nom_jeune_fille_assure = $elements[14];
    $this->taux_PEC               = $elements[15];
    $this->numero_AT              = $elements[16];
    $this->AT_par_tiers           = $elements[17];
    $this->fin_droits             = $this->getDateFromHprim($elements[18]);
    $this->date_accident          = $this->getDateFromHprim($elements[19]);
    $this->nom_employeur          = $elements[20];
    $adresse_employeur            = explode($reader->separateur_sous_champ, $elements[21]);
    // Attention, champs manquants chez Simemens (4 au lieu de 6)
    $this->adresse1_employeur     = $adresse_employeur[0];
    $this->ville_employeur        = $adresse_employeur[1];
    $this->departement_employeur  = $adresse_employeur[2];
    $this->cp_employeur           = $adresse_employeur[3];
    $this->date_debut_grossesse   = $this->getDateFromHprim($elements[22]);
    
    return true;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->civilite $this->nom $this->prenom [$this->external_id]";
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    // Chargement du patient correspondant
    $this->_ref_patient = new CPatient();
    $this->_ref_patient->load($this->patient_id);
  }
  
  function loadRefHprim21Sejours(){
    $sejour = new CHprim21Sejour();
    $where["hprim21_patient_id"] = "= '$this->_id'";
    $order = "date_mouvement";
    $this->_ref_hprim21_sejours = $sejour->loadList($where, $order);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefHprim21Sejours();
  }
}
