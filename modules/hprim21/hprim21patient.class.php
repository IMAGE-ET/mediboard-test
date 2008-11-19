<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPinterop
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

CAppUI::requireModuleClass("hprim21", "hprim21object");

/**
 * The HPRIM 2.1 patient class
 */
class CHprim21Patient extends CHprim21Object {
  // DB Table key
	var $hprim21_patient_id = null;
  
  // DB references
  var $patient_id = null;
	
  // Patient DB Fields
  var $nom                       = null;
  var $prenom                    = null;
  var $prenom2                   = null;
  var $alias                     = null;
  var $civilite                  = null;
  var $diplome                   = null;
  var $nom_jeune_fille           = null;
  var $nom_soundex2              = null;
  var $prenom_soundex2           = null;
  var $nomjf_soundex2            = null;
  var $naissance                 = null;
  var $sexe                      = null;
  var $adresse1                  = null;
  var $adresse2                  = null;
  var $ville                     = null;
  var $departement               = null;
  var $cp                        = null;
  var $pays                      = null;
  var $telephone1                = null;
  var $telephone2                = null;
  var $traitement_local1         = null;
  var $traitement_local2         = null;
  var $taille                    = null;
  var $poids                     = null;
  var $diagnostic                = null;
  var $traitement                = null;
  var $regime                    = null;
  var $commentaire1              = null;
  var $commentaire2              = null;
  var $classification_diagnostic = null;
  var $situation_maritale        = null;
  var $precautions               = null;
  var $langue                    = null;
  var $statut_confidentialite    = null;
  var $date_derniere_modif       = null;
  var $date_deces                = null;
  
  // Assuré primaire DB Fields
  var $nature_assurance       = null;
  var $debut_validite         = null;
  var $fin_validite           = null;
  var $matricule              = null;
  var $rang_beneficiaire      = null;
  var $rang_naissance         = null;
  var $code_regime            = null;
  var $caisse_gest            = null;
  var $centre_gest            = null;
  var $origine_droits         = null;
  var $nature_exoneration     = null;
  var $nom_assure             = null;
  var $prenom_assure          = null;
  var $nom_jeune_fille_assure = null;
  var $taux_PEC               = null;
  var $numero_AT              = null;
  var $AT_par_tiers           = null;
  var $fin_droits             = null;
  var $date_accident          = null;
  var $nom_employeur          = null;
  var $adresse1_employeur     = null;
  var $adresse2_employeur     = null;
  var $ville_employeur        = null;
  var $departement_employeur  = null;
  var $cp_employeur           = null;
  var $pays_employeur         = null;
  var $date_debut_grossesse   = null;
  
  var $_ref_patient = null;
  var $_ref_hprim21_sejours = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'hprim21_patient';
    $spec->key   = 'hprim21_patient_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      // Patient
      "patient_id"                => "ref class|CPatient",
      "nom"                       => "notNull str",
      "prenom"                    => "str",
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
    
	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["hprim21_complementaires"] = "CHprim21Complementaire hprim21_patient_id";
	  $backRefs["hprim21_sejours"]         = "CHprim21Sejour hprim21_patient_id";
	  return $backRefs;
	}

  function updateDBFields() {
  	
  	parent::updateDBFields();
  	 
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
    $this->setEmetteur($reader);
    
    $elements                        = explode($reader->separateur_champ, $line);
  
    if(count($elements) < 34) {
      $reader->error_log[] = "Champs manquant dans le segment patient : ".count($elements)." champs trouvés";
      return false;
    }
    
    $identifiant                     = explode($reader->separateur_sous_champ, $elements[2]);
    if(!$identifiant[0]) {
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
    if(isset($telephone[2])) {
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
  
    if(count($elements) < 22) {
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
  
  function getSeeks() {
    return array (
      "nom"    => "like",
      "prenom" => "like",
    );
  }
  
  function updateFormFields() {
    $this->_view = $this->civilite." ".$this->nom." ".$this->prenom." [".$this->external_id."]";
  }
  
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
  
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefHprim21Sejours();
  }
}
?>