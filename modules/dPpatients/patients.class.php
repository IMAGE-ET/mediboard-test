<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CPatient Class
 */
class CPatient extends CMbObject {
  static $dossier_cabinet_prefix = array (
    "dPcabinet" => "?m=dPcabinet&tab=vw_dossier&patSel=",
    "dPpatients" => "?m=dPpatients&tab=vw_full_patients&patient_id="
  );
  
  // DB Table key
  var $patient_id = null;

  // DB Fields
  var $nom              = null;
  var $nom_jeune_fille  = null;
  var $prenom           = null;
  var $prenom_2         = null;
  var $prenom_3         = null;
  var $prenom_4         = null;
  var $nom_soundex2     = null;
  var $nomjf_soundex2   = null;
  var $prenom_soundex2  = null;
  var $naissance        = null;
  var $sexe             = null;
  var $adresse          = null;
  var $ville            = null;
  var $cp               = null;
  var $tel              = null;
  var $tel2             = null;
  var $email            = null;
  var $medecin_traitant = null;
  var $incapable_majeur = null;
  var $ATNC             = null;
  var $matricule        = null;
  
  var $code_regime      = null;
  var $caisse_gest      = null;
  var $centre_gest      = null;
  var $regime_sante     = null;
  var $rques            = null;
  var $cmu              = null;
  var $ald              = null;
  var $code_exo         = null;
  var $notes_amo        = null;
  var $deb_amo          = null;
  var $fin_amo          = null;
  var $code_sit         = null;
  var $regime_am        = null;
  
  var $rang_beneficiaire= null;
  var $rang_naissance   = null;
  var $fin_validite_vitale = null;
  
  var $pays                 = null;
  var $pays_insee           = null;
  var $nationalite          = null;
  var $lieu_naissance       = null;
  var $cp_naissance         = null;
  var $pays_naissance_insee = null;
  var $profession           = null;

  var $employeur_nom     = null;
  var $employeur_adresse = null;
  var $employeur_cp      = null;
  var $employeur_ville   = null;
  var $employeur_tel     = null;
  var $employeur_urssaf  = null;

  var $prevenir_nom     = null;
  var $prevenir_prenom  = null;
  var $prevenir_adresse = null;
  var $prevenir_cp      = null;
  var $prevenir_ville   = null;
  var $prevenir_tel     = null;
  var $prevenir_parente = null;

  // Assuré
  var $assure_nom                   = null;
  var $assure_nom_jeune_fille       = null;
  var $assure_prenom                = null;
  var $assure_prenom_2              = null;
  var $assure_prenom_3              = null;
  var $assure_prenom_4              = null;
  var $assure_naissance             = null;
  var $assure_sexe                  = null;
  var $assure_adresse               = null;
  var $assure_ville                 = null;
  var $assure_cp                    = null;
  var $assure_tel                   = null;
  var $assure_tel2                  = null;
  var $assure_pays                  = null;
  var $assure_pays_insee            = null;
  var $assure_nationalite           = null;
  var $assure_cp_naissance          = null;
  var $assure_lieu_naissance        = null;
  var $assure_pays_naissance_insee  = null;
  var $assure_profession            = null;
  var $assure_rques                 = null;
  var $assure_matricule             = null;
  
  // Other fields
  var $_static_cim10                = null;
  var $_pays_naissance_insee        = null;
  var $_assure_pays_naissance_insee = null;
  
  // Form fields
  var $_age         = null;
  var $_article     = null;
  var $_longview    = null;
  
  // Vitale
  var $_bind_vitale = null;
  var $_id_vitale   = null;
  
  // Navigation Fields
  var $_dossier_cabinet_url = null;

  // HPRIM Fields
  var $_prenoms        = null; // multiple
  var $_nom_naissance  = null; // +/- = nom_jeune_fille
  var $_adresse_ligne2 = null;
  var $_adresse_ligne3 = null;
  var $_pays           = null;
  var $_IPP            = null;
  
  // Object References
  var $_nb_docs              = null;
  var $_ref_sejours          = null;
  var $_ref_consultations    = null;
  var $_ref_prescriptions    = null;
  var $_ref_curr_affectation = null;
  var $_ref_next_affectation = null;
  var $_ref_medecin_traitant = null;
  var $_ref_medecins_correspondants = null;
  var $_ref_dossier_medical  = null;
  var $_ref_IPP              = null;
  var $_ref_constantes_medicales = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'patients';
    $spec->key   = 'patient_id';
    $spec->measureable = true;
    return $spec;
  }

	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["constantes"]            = "CConstantesMedicales patient_id";
	  $backRefs["consultations"]         = "CConsultation patient_id";
	  $backRefs["correspondants"]        = "CCorrespondant patient_id";
	  $backRefs["hprim21_patients"]      = "CHprim21Patient patient_id";
	  $backRefs["prescriptions_labo"]    = "CPrescriptionLabo patient_id";
	  $backRefs["product_deliveries"]    = "CProductDelivery patient_id";
	  $backRefs["sejours"]               = "CSejour patient_id";
	  return $backRefs;
	}  
  
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["nom"]               = "str notNull confidential";
    $specs["prenom"]            = "str notNull";
    $specs["prenom_2"]          = "str";
    $specs["prenom_3"]          = "str";
    $specs["prenom_4"]          = "str";
    $specs["nom_jeune_fille"]   = "str confidential";
    $specs["nom_soundex2"]      = "str";
    $specs["prenom_soundex2"]   = "str";
    $specs["nomjf_soundex2"]    = "str";
    $specs["medecin_traitant"]  = "ref class|CMedecin";
    $specs["matricule"]         = "code insee confidential mask|9S99S99S99S999S999S99";
    $specs["code_regime"]       = "numchar length|2";
    $specs["caisse_gest"]       = "numchar length|3";
    $specs["centre_gest"]       = "numchar length|4";
    $specs["regime_sante"]      = "str";
    $specs["sexe"]              = "enum list|m|f|j default|m";
    $specs["adresse"]           = "text confidential";
    $specs["ville"]             = "str confidential";
    $specs["cp"]                = "numchar minLength|4 maxLength|5 confidential";
    $specs["tel"]               = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["tel2"]              = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["email"]             = "str confidential";
    $specs["incapable_majeur"]  = "bool";
    $specs["ATNC"]              = "bool";
    
    
    if(CAppUI::conf("dPpatients CPatient date_naissance")){
      $specs["naissance"]       = "birthDate notNull mask|99/99/9999 format|$3-$2-$1";
    } else {
      $specs["naissance"]       = "birthDate mask|99/99/9999 format|$3-$2-$1";  
    }
    
    $specs["rques"]             = "text";
    $specs["cmu"]               = "bool";
    $specs["ald"]               = "bool";
    $specs["code_exo"]          = "enum list|0|5|9 default|0";
    $specs["deb_amo"]           = "date";
    $specs["fin_amo"]           = "date";
    $specs["notes_amo"]         = "text";
    $specs["rang_beneficiaire"] = "enum list|01|02|09|11|12|13|14|15|16|31";
    $specs["rang_naissance"]    = "enum list|1|2|3|4|5|6 default|1";
    $specs["fin_validite_vitale"] = "date";
    $specs["code_sit"]          = "numchar length|4";
    $specs["regime_am"]         = "bool default|0";
    
    $specs["pays"]                 = "str";
    $specs["pays_insee"]           = "str";
    $specs["nationalite"]          = "enum notNull list|local|etranger default|local";
    $specs["lieu_naissance"]       = "str";
    $specs["cp_naissance"]         = "str";
    $specs["pays_naissance_insee"] = "str";
    $specs["profession"]           = "str";
      
    $specs["employeur_nom"]     = "str confidential";
    $specs["employeur_adresse"] = "text";
    $specs["employeur_cp"]      = "numchar length|5";
    $specs["employeur_ville"]   = "str confidential";
    $specs["employeur_tel"]     = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["employeur_urssaf"]  = "numchar length|11 confidential";

    $specs["prevenir_nom"]      = "str confidential";
    $specs["prevenir_prenom"]   = "str";
    $specs["prevenir_adresse"]  = "text";
    $specs["prevenir_cp"]       = "numchar length|5";
    $specs["prevenir_ville"]    = "str confidential";
    $specs["prevenir_tel"]      = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["prevenir_parente"]  = "enum list|conjoint|enfant|ascendant|colateral|divers";
    
    $specs["assure_nom"]                  = "str confidential";
    $specs["assure_prenom"]               = "str";
    $specs["assure_prenom_2"]             = "str";
    $specs["assure_prenom_3"]             = "str";
    $specs["assure_prenom_4"]             = "str";
    $specs["assure_nom_jeune_fille"]      = "str confidential";
    $specs["assure_sexe"]                 = "enum list|m|f|j default|m";
    $specs["assure_naissance"]            = "birthDate confidential mask|99/99/9999 format|$3-$2-$1";
    $specs["assure_adresse"]              = "text confidential";
    $specs["assure_ville"]                = "str confidential";
    $specs["assure_cp"]                   = "numchar minLength|4 maxLength|5 confidential";
    $specs["assure_tel"]                  = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["assure_tel2"]                 = "numchar confidential length|10 mask|99S99S99S99S99";
    $specs["assure_pays"]                 = "str";
    $specs["assure_pays_insee"]           = "str";
    $specs["assure_lieu_naissance"]       = "str";
    $specs["assure_nationalite"]          = "enum notNull list|local|etranger default|local";
    $specs["assure_lieu_naissance"]       = "str";
    $specs["assure_cp_naissance"]         = "str";
    $specs["assure_pays_naissance_insee"] = "str";
    $specs["assure_profession"]           = "str";
    $specs["assure_rques"]                = "text";
    $specs["assure_matricule"]            = "code insee confidential mask|9S99S99S99S999S999S99";
    
    $specs["_id_vitale"]                  = "num";
    $specs["_pays_naissance_insee"]       = "str";
    $specs["_assure_pays_naissance_insee"]       = "str";
    return $specs;
  }
  
  function getSeeks() {
    return array(
      "nom"    => "likeBegin",
      "prenom" => "likeBegin",
      "ville"  => "like"
    );
  }

  function getHelpedFields(){
    return array(
    );
  }
  
  function checkMerge($patients = array()/*<CPatient>*/) {
    if ($msg = parent::checkMerge($patients)) {
      return $msg;
    }
    foreach($patients as &$patient1) {
      if (!$patient1->_ref_sejours)
        $patient1->loadRefsSejours();
      
      //FIXME: parfois _ref_sejours est NULL, pas array()
      if (!$patient1->_ref_sejours) continue;
      
      foreach($patients as &$patient2) {
        if ($patient1->_id == $patient2->_id) continue;
        
        if (!$patient2->_ref_sejours)
          $patient2->loadRefsSejours();

        if (!$patient2->_ref_sejours) continue;
          
        foreach($patient1->_ref_sejours as $sej_pat1) {
          foreach($patient2->_ref_sejours as $sej_pat2) {
            if($sej_pat1->collides($sej_pat2)) {
              return "Conflit de séjours entre $patient1->_view et $patient2->_view";
            }
          }
        }
      }
    }
    return null;
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    // Bind vitale
    if ($this->_bind_vitale && $this->_id) {
      return $this->bindVitale();
    }
  }
       
  function bindVitale() {
    // Make id400
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }
    
    $vitale = $intermax["VITALE"];
    $vitNumero = $vitale["VIT_NUMERO_LOGICMAX"];
    $id_vitale = new CIdSante400();
    $id_vitale->object_class = $this->_class_name;
    $id_vitale->id400 = $vitNumero;
    $id_vitale->tag = "LogicMax VitNumero";
    $id_vitale->loadMatchingObject();
    
    // Autre association ?
    if ($id_vitale->object_id && $id_vitale->object_id != $this->_id) {
      $id_vitale->loadTargetObject();
      $patOther =& $id_vitale->_ref_object;
      return "Bénéficiaire Vitale déjà associé au patient " . $patOther->_view .
        " né le " . mbDateToLocale($patOther->naissance);
    }
    
    $id_vitale->object_id = $this->_id;
    $id_vitale->last_update = mbDateTime();
    return $id_vitale->store();
  }

  function loadIdVitale() {
    if (!$this->_id) {
      return;
    }
    
    $id_vitale = new CIdSante400();
    if (!$id_vitale->_ref_module) {
      return;
    }
    
    $id_vitale->setObject($this);
    $id_vitale->tag = "LogicMax VitNumero";
    $id_vitale->loadMatchingObject();
    $this->_id_vitale = $id_vitale->id400;
  }
  
  /**
   * Load exact patient associated with id vitale
   */
  function loadFromIdVitale() {
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }
    
    $vitale = $intermax["VITALE"];
    $vitNumero = $vitale["VIT_NUMERO_LOGICMAX"];
    
    // Make id vitale
    $id_vitale = new CIdSante400();
    $id_vitale->object_class = $this->_class_name;
    $id_vitale->id400 = $vitNumero;
    $id_vitale->tag = "LogicMax VitNumero";
    $id_vitale->loadMatchingObject();
    
    // Load patient from found id vitale
    if ($id_vitale->object_id) {
      $this->load($id_vitale->object_id);
    }
  }
  
  function getValuesFromVitale() {
    if (null == $intermax = mbGetAbsValueFromPostOrSession("intermax")) {
      return;
    }
    
    $vitale = $intermax["VITALE"];
    $this->nom    = $vitale["VIT_NOM"];
    $this->prenom = $vitale["VIT_PRENOM"];
    $this->naissance = mbDateFromLocale($vitale["VIT_DATE_NAISSANCE"]);
    
    $this->rang_naissance = $vitale["VIT_RANG_GEMELLAIRE"];
    
    // Adresse
    $this->adresse = join("\n", array(
      $vitale["VIT_ADRESSE_1"],
      $vitale["VIT_ADRESSE_2"],
      $vitale["VIT_ADRESSE_3"],
      $vitale["VIT_ADRESSE_4"])
    );
    
    $this->adresse = trim($this->adresse);     
    
    // CP et ville
    $cpville = split(" ", $vitale["VIT_ADRESSE_5"], 2);
    $this->cp = @$cpville[0];
    $this->ville = @$cpville[1];
    
    // Matricules
    $this->assure_matricule = join("", array($vitale["VIT_NUMERO_SS"], $vitale["VIT_CLE_SS"]));
    $this->matricule = $this->assure_matricule;
    if ($vitale["VIT_NUMERO_SS_INDIV"]) {
      $this->matricule = join("", array($vitale["VIT_NUMERO_SS_INDIV"], $vitale["VIT_CLE_SS_INDIV"]));
    }
    
    $sexeMatrix = array (
      "1" => "m",
      "2" => "f",
    );
    
    // Sexe récupéré que quand le bénéficiaire est l'assuré
    if ($vitale["VIT_CODE_QUALITE"] == "00") {
      $this->sexe = $sexeMatrix[$this->matricule[0]];
    }

    // Assuré
    $this->assure_nom          = $vitale["VIT_NOM_ASSURE"];
    $this->assure_prenom       = $vitale["VIT_PRENOM_ASSURE"];
    $this->fin_validite_vitale = mbDateFromLocale($vitale["VIT_FIN_VALID_VITALE"]);
    
    // Régime
    $this->code_regime  = $vitale["VIT_CODE_REGIME"];
    $this->caisse_gest  = $vitale["VIT_CAISSE_GEST"];
    $this->centre_gest  = $vitale["VIT_CENTRE_GEST"];
    $this->regime_sante = $vitale["VIT_NOM_AMO"];
    
    // Rang bénéficiaire
    $codeRangMatrix = array(
			"00"=> "01", // Assuré
			"01"=> "31", // Ascendant, descendant, collatéraux ascendants
			"02"=> "02", // Conjoint
			"03"=> "02", // Conjoint divorcé
			"04"=> "02", // Concubin
			"05"=> "02", // Conjoint séparé
			"06"=> "11", // Enfant
			"07"=> "02", // Bénéficiaire hors article 313
			"08"=> "02", // Conjoint veuf
			"09"=> "02", // Autre ayant droit
    );
    
    $this->rang_beneficiaire = $codeRangMatrix[$vitale["VIT_CODE_QUALITE"]];
    
    // Recherche de la période AMO courante
    foreach ($intermax as $category => $periode) {
      if (preg_match("/PERIODE_AMO_(\d)+/i", $category)) {
        $deb_amo = mbDateFromLocale($periode["PER_AMO_DEBUT"]);
        $fin_amo = mbGetValue(mbDateFromLocale($periode["PER_AMO_FIN"]), "2015-01-01");
        if (in_range(mbDate(), $deb_amo, $fin_amo)) {
          $this->deb_amo  = $deb_amo;
          $this->fin_amo  = $fin_amo;
          $this->ald      = $periode["PER_AMO_ALD"];
          $this->code_exo = $periode["PER_AMO_CODE_EXO"];
          $this->code_sit = $periode["PER_AMO_CODE_SIT"];
          if ($vitale["VIT_CMU"]) {
            $this->cmu = 1;
          }
        }
      }
    }
    
    $this->regime_am = $vitale["VIT_REGIME_AM"];
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Noms
    $this->nom = strtoupper($this->nom);
    $this->nom_jeune_fille = strtoupper($this->nom_jeune_fille);
    $this->prenom = ucwords(strtolower($this->prenom));
    
    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom; 
    $this->_prenoms = array($this->prenom, $this->prenom_2, $this->prenom_3, $this->prenom_4);
  
    $this->evalAge();
    
    // Attention 0 != "??" est faux !
    if ($this->_age !== "??" && $this->_age <= 15) {
      $this->_shortview = "Enf.";
      if($this->sexe == "m"){
        $this->_article = "le jeune";
      }
      if($this->sexe == "j"){
        $this->_article = "la jeune";
      }
    } 
    elseif($this->sexe == "m"){
      $this->_shortview = "M.";
      $this->_article = "Monsieur";
    } 
    elseif($this->sexe == "f"){
      $this->_shortview = "Mme.";
      $this->_article = "Madame";
    } 
    else { 
      $this->_shortview = "Mlle.";
      $this->_article = "Mademoiselle";
    }  
      
    $this->_view     = "$this->_shortview $this->nom $this->prenom";
    $this->_longview = "$this->_article $this->nom $this->prenom";
        
    // Navigation fields
    global $AppUI;
    $this->_dossier_cabinet_url = self::$dossier_cabinet_prefix[$AppUI->user_prefs["DossierCabinet"]] . $this->_id;
  }
  
  /**
   * Calcul l'âge du patient en années
   */
  function evalAge($date = null){
    $annee = $date ? substr($date, 0, 4) : date("Y");
    $mois  = $date ? substr($date, 5, 2) : date("m");
    $jour  = $date ? substr($date, 8, 2) : date("d");
    
    if ($this->naissance == "0000-00-00" || !$this->naissance) {
      $this->_age = "??";
      return;
    }
    
    $naissance = explode("-", $this->naissance);
    $jourN  = $naissance[2];
    $moisN  = $naissance[1];
    $anneeN = $naissance[0];

    $this->_age = $annee - $anneeN;

    // Ajustement en fonction des mois et des jours
    if ($mois < $moisN || ($jour < $jourN && $mois == $moisN)) {
      $this->_age--;
    }
  }
  
  /**
   * Calcul l'âge du patient en mois
   */
  function evalAgeMois($date = null){
    $annee = $date ? substr($date, 0, 4) : date("Y");
    $mois  = $date ? substr($date, 5, 2) : date("m");
    $jour  = $date ? substr($date, 8, 2) : date("d");
    
    if ($this->naissance == "0000-00-00" || !$this->naissance) {
      return 0;
    }
    
    $naissance = explode("-", $this->naissance);
    $jourN  = $naissance[2];
    $moisN  = $naissance[1];
    $anneeN = $naissance[0];

    $nbMois = 12*($annee - $anneeN);
    $nbMois += $mois - $moisN;
    if($jour < $jourN && $mois == $moisN) {
      $nbMois--;
    }
    return $nbMois;
  }
  
  /**
   * Calcul l'âge du patient en semaines
   */
  function evalAgeSemaines($date = null){
    $jours = evalAgeJours($date);
    return intval($jours/7);
  }
  
  /**
   * Calcul l'âge du patient en jours
   */
  function evalAgeJours($date = null){
    $date = $date ? $date : mbDate();
    if ($this->naissance == "0000-00-00" || !$this->naissance) {
      return 0;
    }
    return mbDaysRelative($this->naissance, $date);
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
    
    if ($this->cp == "00000") {
      $this->cp = "";
    }
  	
    if ($this->assure_nom) {
  	  $this->assure_nom = strtoupper($this->assure_nom);
    }
    
    if ($this->assure_nom_jeune_fille) {
  	  $this->assure_nom_jeune_fille = strtoupper($this->assure_nom_jeune_fille);
    }

    if ($this->assure_prenom) {
      $this->assure_prenom = ucwords(strtolower($this->assure_prenom));
    }
  	
    if ($this->assure_cp == "00000") {
      $this->assure_cp = "";
    }
    
    if ($this->_pays_naissance_insee) {
      $this->pays_naissance_insee = $this->updatePatNumPaysInsee($this->_pays_naissance_insee);
    }
    
    if ($this->pays) {
      $this->pays_insee = $this->updatePatNumPaysInsee($this->pays);
    }
    
    if ($this->_assure_pays_naissance_insee) {
      $this->assure_pays_naissance_insee = $this->updatePatNumPaysInsee($this->_assure_pays_naissance_insee);
    }
    
    if ($this->assure_pays) {
      $this->assure_pays_insee = $this->updatePatNumPaysInsee($this->assure_pays);
    }
  }
  
  // Backward references
  function loadRefsSejours($where = null) {
    $sejour = new CSejour;
    if ($this->patient_id){
      if ($where === null) {
        $where = array();
      }
      $where["patient_id"] = "= '$this->patient_id'";
      $order = "entree_prevue DESC";
      $this->_ref_sejours = $sejour->loadList($where, $order);
    }
  }
  
  /**
   * Get an associative array of uncancelled sejours and their dates
   * @return array Sejour ID => array("entree_prevue" => DATE, "sortie_prevue" => DATE)
   */
  function getSejoursCollisions() {
    $sejours_collision = array();
    if ($this->_ref_sejours) {
			foreach ($this->_ref_sejours as $_sejour) {
			  if (!$_sejour->annule) {
				  $sejours_collision[$_sejour->_id] = array (
				    "entree_prevue" => mbDate($_sejour->entree_prevue),
				    "sortie_prevue" => mbDate($_sejour->sortie_prevue)
				  );
			  }
			}
    }
    
    return $sejours_collision;
  }
  
  /**
   * Charge le patient ayant les traits suivants :
   * - Même nom à la casse et aux séparateurs près
   * - Même prénom à la casse et aux séparateurs près
   * - Strictement la même date de naissance
   * @return Nombre d'occurences trouvées 
   */
  function loadMatchingPatient() {
    $ds = $this->_spec->ds;
    $where["nom"]       = $ds->prepare("LIKE %", preg_replace("/\W/", "%", $this->nom));
    $where["prenom"]    = $ds->prepare("LIKE %", preg_replace("/\W/", "%", $this->prenom));
    $where["naissance"] = $ds->prepare("= %", $this->naissance);
    
    $this->loadObject($where);
    
    return $this->countList($where);
  }
  
  function loadRefsConsultations($where = null) {
    $this->_ref_consultations = new CConsultation();
    if ($this->patient_id){
      if ($where === null) {
        $where = array();
      }
      $where["patient_id"] = "= '$this->patient_id'";
      $order = "plageconsult.date DESC";
      $leftjoin = array();
      $leftjoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
      $this->_ref_consultations = $this->_ref_consultations->loadList($where, $order, null, null, $leftjoin);
    }
  }
  
  function loadRefDossierMedical(){
    $this->_ref_dossier_medical = new CDossierMedical();
    $where["object_id"] = "= '$this->_id'";
    $where["object_class"] = "= 'CPatient'";
    $this->_ref_dossier_medical->loadObject($where);
    $this->_ref_dossier_medical->loadRefsBack();
  }
  
  function loadRefsAffectations() {
    $this->loadRefsSejours();
    // Affectation actuelle et prochaine affectation
    $where["sejour_id"] = $this->_spec->ds->prepareIn(array_keys($this->_ref_sejours));
    $order = "entree";
    $now = mbDateTime();
    
    $this->_ref_curr_affectation = new CAffectation();
    if($this->_ref_curr_affectation->_ref_module) {
      $where["entree"] = "< '$now'";
      $where["sortie"] = ">= '$now'";
      $this->_ref_curr_affectation->loadObject($where, $order);
    } else {
      $this->_ref_curr_affectation = null;
    }
    
    $this->_ref_next_affectation = new CAffectation();
    if($this->_ref_next_affectation->_ref_module) {
      $where["entree"] = "> '$now'";
      $this->_ref_next_affectation->loadObject($where, $order);
    } else {
      $this->_ref_next_affectation = null;
    }
  }
  
  function loadRefsDocs() {
    $docs_valid = parent::loadRefsDocs();
    if ($docs_valid)
      $this->_nb_docs .= "$docs_valid";
  }
  
  function loadRefsPrescriptions($perm = null) {
    if (CModule::getInstalled("dPlabo")) {
      $prescription = new CPrescriptionLabo();
      $where = array("patient_id" => "= '$this->_id'");
      $order = "date DESC";
      $this->_ref_prescriptions = $prescription->loadListWithPerms($perm, $where, $order);
    }
  }
  
  function loadRefConstantesMedicales() {
    list($this->_ref_constantes_medicales, $dates) = CConstantesMedicales::getLatestFor($this);
    $this->_ref_constantes_medicales->updateFormFields();
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefsConsultations();
    $this->loadRefsAffectations();
    $this->loadRefsPrescriptions();
  }

  // Forward references
  function loadRefsFwd() {
    // medecin_traitant
    $this->_ref_medecin_traitant = new CMedecin();
    $this->_ref_medecin_traitant->load($this->medecin_traitant);

    $this->loadRefsCorrespondants();
    $this->loadIdVitale();
  }
  
  // Liste statique des codes CIM10 initiaux
  function loadStaticCIM10($user = 0) {
    if (!CAppUI::conf("dPpatients CDossierMedical diags_static_cim")) {
      return;
    }
    
    // Liste des favoris
    if($user) {
      $favoris = new CFavoricim10;
      $where = array();
      $where["favoris_user"] = "= '$user'";
      $order = "favoris_code";
      $favoris = $favoris->loadList($where, $order);
      foreach($favoris as $key => $value) {
        $this->_static_cim10["favoris"][$value->favoris_code] = new CCodeCIM10($value->favoris_code, 1);
      }
      $ds = CSQLDataSource::get("std");
      $sql = "SELECT DP, count(DP) as nb_code
        FROM `sejour`
        WHERE sejour.praticien_id = '$user'
        AND DP IS NOT NULL
        AND DP != ''
        GROUP BY DP
        ORDER BY nb_code DESC
        LIMIT 20;";
      $cimStat = $ds->loadlist($sql);
      foreach($cimStat as $key => $value) {
        $this->_static_cim10["favoris"][$value["DP"]] = new CCodeCIM10($value["DP"], 1);
        
      }
    }
    
    // Liste statique
    //$this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I20", 1);       // Angor
    //$this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I21", 1);       // Infarctus
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("(I20-I25)", 1); // Cardiopathies ischemiques
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("J81", 1);       // O.A.P ?
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("R60", 1);       // Oedemes
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I776", 1);      // Artérite
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("R943", 1);      // ECG
    $this->_static_cim10["cardiovasculaire"][] = new CCodeCIM10("I10", 1);       // HTA
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("A15", 1);       // Pleurésie1
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("A16", 1);       // Pleurésie2
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("(J10-J18)", 1); // Pneumonie
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("J45", 1);       // Asthme
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("J180", 1);      // BPCO
    $this->_static_cim10["respiratoire"][]     = new CCodeCIM10("R230", 1);      // Cyanose
    $this->_static_cim10["divers"][]           = new CCodeCIM10("Z88", 1);       // Allergies
    $this->_static_cim10["divers"][]           = new CCodeCIM10("(B15-B19)", 1); // Hepatite
    $this->_static_cim10["divers"][]           = new CCodeCIM10("(E10-E14)", 1); // Diabete
    $this->_static_cim10["divers"][]           = new CCodeCIM10("H40", 1)      ; // Glaucome
    
    // Sommaire complet
    $sommaire = new CCodeCIM10();
    $sommaire = $sommaire->getSommaire();
    foreach($sommaire as $key => $value) {
      $this->_static_cim10["sommaire"][] = new CCodeCIM10($value["code"], 1);
    }
  }
    
  function loadComplete(){
    parent::loadComplete();
    $this->loadIPP();
    $this->loadRefPhotoIdentite();
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->loadRefsAntecedents();
    $this->_ref_dossier_medical->loadRefsTraitements();  
  }
  
  function loadDossierComplet($permType = null) {
    $pat_id = $this->loadRefs();
    
    $this->canRead();
    $this->canEdit();
    $this->getNumDocsAndFiles($permType);
    $this->loadRefPhotoIdentite();
    
    // Affectations courantes
    $affectation =& $this->_ref_curr_affectation;
    if ($affectation && $affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    $affectation =& $this->_ref_next_affectation;
    if ($affectation && $affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    // Si le loadRef n'a pas fonctionné, on arrete la
    if(!$pat_id) {
      return;
    }
  
    // Consultations
    foreach ($this->_ref_consultations as $keyConsult => $valueConsult) {
      $consult =& $this->_ref_consultations[$keyConsult];
      
      $consult->loadRefConsultAnesth();
      $consult->loadRefsFichesExamen();
      $consult->loadExamsComp();
      $consult->getNumDocsAndFiles($permType);
      
      $consult->loadRefsFwd(1);
      $consult->canRead();
      $consult->canEdit();
    }
    
    // Sejours
    foreach ($this->_ref_sejours as $keySejour => $valueSejour) {
      $sejour =& $this->_ref_sejours[$keySejour];
      $sejour->loadNumDossier();
      $sejour->loadRefsAffectations();
      $sejour->loadRefsOperations();
      $sejour->getNumDocsAndFiles($permType);
      
      $sejour->loadRefsFwd(1);
      $sejour->canRead();
      $sejour->canEdit();
      $sejour->getNumDocsAndFiles($permType);
      foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
        $operation =& $sejour->_ref_operations[$keyOp];
        $operation->loadRefsFwd(1);
        $operation->getNumDocsAndFiles($permType);
        $operation->canRead();
        $operation->canEdit();
      }
      $sejour->loadRefRPU();
      if($sejour->_ref_rpu && $sejour->_ref_rpu->_id) {
        $sejour->_ref_rpu->getNumDocsAndFiles($permType);
      }
    }
  }
  
  function loadRefsCorrespondants() {
    $this->_ref_medecins_correspondants = array();
  	if ($this->_id) {
	  	$correspondant = new CCorrespondant();
	  	$correspondant->patient_id = $this->_id;
  	  $this->_ref_medecins_correspondants = $correspondant->loadMatchingList();
  	  foreach ($this->_ref_medecins_correspondants as &$corresp) {
  	  	$corresp->loadRefs();
  	  }
  	}
  	return $this->_ref_medecins_correspondants;
  }

  /**
   * Charge l'IPP du patient pour l'établissement courant
   * @param $group_id Permet de charger l'IPP pour un établissement donné si non null
   */
  function loadIPP($group_id = null) {
    if (!$this->_id) {
      return;
    }
    
    // Prevent loading twice
    if ($this->_IPP) {
      return;
    }
    
  	// Pas de tag IPP => pas d'affichage d'IPP
  	if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
  		$this->_IPP = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
    	return;
    }

    // Permettre des IPP en fonction de l'établissement
    if (!$group_id) {
      global $g;
      $group_id = $g;
  	}
  	
    $tag_ipp = str_replace('$g',$group_id, $tag_ipp);

    // Récupération du premier IPP créé, utile pour la gestion des doublons
    $order = "id400 ASC";
  	
  	// Recuperation de la valeur de l'id400
  	$id400 = new CIdSante400();
  	$id400->setObject($this);
  	$id400->tag = $tag_ipp;
  	$id400->loadMatchingObject($order);
  	
    // Stockage de la valeur de l'id400
    $this->_ref_IPP = $id400;
    $this->_IPP     = $id400->id400;
    
    // Si pas d'id400 correspondant, on stocke "_"
    if (!$this->_IPP){
    	$this->_IPP = "-";
    }
  }
  
  function checkSimilar($nom, $prenom) {
    $soundex2 = new soundex2;
    $testNom    = $this->nom_soundex2    == $soundex2->build($nom);
    $testPrenom = $this->prenom_soundex2 == $soundex2->build($prenom);
    return($testNom && $testPrenom);
  }
  
  /**
   * Finds patient siblings with at least two exact matching traits out of 
   * nom, prenom, naissance
   * Optimized version with split queries for index usage forcing
   * @return array[CPatient] Array of siblings
   */
  function getSiblings() {
    $ds =& $this->_spec->ds;

    $where = array (
      "nom"    => $ds->prepare(" = %", $this->nom   ),
      "prenom" => $ds->prepare(" = %", $this->prenom),
      "patient_id" => "!= '$this->_id'",
    );
	  $siblings = $this->loadList($where);
    
    if ($this->naissance != "0000-00-00") {
	    $where = array (
	      "nom"       => $ds->prepare(" = %", $this->nom      ),
	      "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
	    $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where));

	    $where = array (
	      "prenom"    => $ds->prepare(" = %", $this->prenom   ),
	      "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
	    );
	    $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where));
    }
    
    return $siblings;
  }
  
  function loadRefPhotoIdentite() {
 	  $file = new CFile();
 	  $file->setObject($this);
 	  $file->file_name = 'identite.jpg';
 	  $file->loadMatchingObject();
 	  $this->_ref_photo_identite = $file;
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd();
    $this->loadRefConstantesMedicales();
    
    $template->addProperty("Patient - article"           , $this->_shortview );
    $template->addProperty("Patient - article long"      , $this->_article   );
    $template->addProperty("Patient - nom"               , $this->nom        );
    $template->addProperty("Patient - prénom"            , $this->prenom     );
    $template->addProperty("Patient - adresse"           , $this->adresse    );
    $template->addProperty("Patient - ville"             , $this->ville      );
    $template->addProperty("Patient - cp"                , $this->cp         );
    $template->addProperty("Patient - âge"               , $this->_age       );
    $template->addDateProperty("Patient - date de naissance", $this->naissance);
    $template->addProperty("Patient - numéro d'assuré"   , $this->matricule  );
    $template->addProperty("Patient - téléphone"         , $this->tel        );
    $template->addProperty("Patient - mobile"            , $this->tel2       );
    $template->addProperty("Patient - profession"        , $this->profession );
    
    if ($this->sexe == "m"){
      $template->addProperty("Patient - il/elle"         , "il"              );
      $template->addProperty("Patient - le/la"           , "le"              );
      $template->addProperty("Patient - accord genre"    , ""                );
    } else {
      $template->addProperty("Patient - il/elle"         , "elle"            );
      $template->addProperty("Patient - le/la"           , "la"              );
      $template->addProperty("Patient - accord genre"    , "e"               );
    }
    
    if ($this->medecin_traitant) {
      $template->addProperty("Patient - médecin traitant"          , "{$this->_ref_medecin_traitant->nom} {$this->_ref_medecin_traitant->prenom}");
      $template->addProperty("Patient - médecin traitant - adresse", "{$this->_ref_medecin_traitant->adresse}\n{$this->_ref_medecin_traitant->cp} {$this->_ref_medecin_traitant->ville}");
    } else {
      $template->addProperty("Patient - médecin traitant");
      $template->addProperty("Patient - médecin traitant - adresse");
    }
    
    // Vider les anciens holders
    if ($this->_id) {
	    for ($i = 1; $i < 4; $i++) {
	      $template->addProperty("Patient - médecin correspondant $i");
	      $template->addProperty("Patient - médecin correspondant $i - adresse");
	    }
    }
    
    $this->loadRefsCorrespondants();
    $i = 0;
    $noms = array();
    foreach ($this->_ref_medecins_correspondants as $corresp) {
      $i++;
      $corresp->loadRefsFwd();
      $medecin = $corresp->_ref_medecin;
      $nom = "{$medecin->nom} {$medecin->prenom}";
      $noms[] = $nom;
      $template->addProperty("Patient - médecin correspondant $i", $nom);
      $template->addProperty("Patient - médecin correspondant $i - adresse", "{$medecin->adresse}\n{$medecin->cp} {$medecin->ville}");
    }
    
    $template->addProperty("Patient - médecin correspondants", join($noms, " - "));
    
    $const_med = $this->_ref_constantes_medicales;
    $template->addProperty("Patient - poids",  $const_med->poids." kg");
    $template->addProperty("Patient - taille", $const_med->taille." cm");
    $template->addProperty("Patient - Pouls",  $const_med->pouls);
    $template->addProperty("Patient - IMC",    $const_med->_imc);
    $template->addProperty("Patient - VST",    $const_med->_vst);
    $template->addProperty("Patient - TA",     ($const_med->ta ? $const_med->_ta_systole." / ".$const_med->_ta_diastole : ''));
  }
  
  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);
    $this->loadRefDossierMedical();
    // Dossier médical
    $this->_ref_dossier_medical->fillTemplate($template);
  }
  
  function updateNomPaysInsee() {
  	$pays = new CPaysInsee();
  	if ($this->pays_naissance_insee) {
	  	$pays->numerique = $this->pays_naissance_insee;
	    $pays->loadMatchingObject();
	    $this->_pays_naissance_insee = $pays->nom_fr;
  	}
  	if ($this->assure_pays_naissance_insee) {
  		$pays->numerique = $this->assure_pays_naissance_insee;
      $pays->loadMatchingObject();
      $this->_assure_pays_naissance_insee = $pays->nom_fr;
  	}
  }
  
  function updatePatNumPaysInsee($nomPays) {
    $pays = new CPaysInsee();
    $pays->nom_fr = $nomPays;
    $pays->loadMatchingObject();
    return $pays->numerique;
  }
}

?>