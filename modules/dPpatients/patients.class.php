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
  var $medecin_traitant = null;
  var $medecin1         = null;
  var $medecin2         = null;
  var $medecin3         = null;
  var $incapable_majeur = null;
  var $ATNC             = null;
  var $matricule        = null;
  var $SHS              = null;
  var $regime_sante     = null;
  var $rques            = null;
  var $listCim10        = null;
  var $cmu              = null;
  var $ald              = null;
  var $rang_beneficiaire= null;

  var $pays             = null;
  var $nationalite      = null;
  var $lieu_naissance   = null;
  var $profession       = null;

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
  
  // Other fields
  var $_static_cim10 = null;

  // Form fields
  var $_naissance   = null;
  var $_jour        = null;
  var $_mois        = null;
  var $_annee       = null;
  var $_tel1        = null;
  var $_tel2        = null;
  var $_tel3        = null;
  var $_tel4        = null;
  var $_tel5        = null;
  var $_tel21       = null;
  var $_tel22       = null;
  var $_tel23       = null;
  var $_tel24       = null;
  var $_tel25       = null;
  var $_tel31       = null;
  var $_tel32       = null;
  var $_tel33       = null;
  var $_tel34       = null;
  var $_tel35       = null;
  var $_tel41       = null;
  var $_tel42       = null;
  var $_tel43       = null;
  var $_tel44       = null;
  var $_tel45       = null;
  var $_age         = null;
  var $_codes_cim10 = null;
  
  // Navigation Fields
  var $_dossier_cabinet_url = null;

  // HPRIM Fields
  var $_prenoms        = null; // multiple
  var $_nom_naissance  = null; // +/- = nom_jeune_fille
  var $_adresse_ligne2 = null;
  var $_adresse_ligne3 = null;
  var $_pays           = null;
  
  // DHE Fields
  var $_urlDHE       = null;
  var $_urlDHEParams = null;

  // Object References
  var $_nb_docs              = null;
  var $_ref_sejours          = null;
  var $_ref_consultations    = null;
  var $_ref_antecedents      = null;
  var $_ref_traitements      = null;
  var $_ref_addictions       = null;
  var $_ref_prescriptions    = null;
  var $_ref_types_addiction  = null;
  var $_ref_curr_affectation = null;
  var $_ref_next_affectation = null;
  var $_ref_medecin_traitant = null;
  var $_ref_medecin1         = null;
  var $_ref_medecin2         = null;
  var $_ref_medecin3         = null;

	function CPatient() {
		$this->CMbObject("patients", "patient_id");    
    $this->loadRefModule(basename(dirname(__FILE__)));
 	}
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["consultations"] = "CConsultation patient_id";
      $backRefs["prescriptions_labo"] = "CPrescriptionLabo patient_id";
      $backRefs["sejours"] = "CSejour patient_id";
     return $backRefs;
  }
 
  function getSpecs() {
    return array (
      "nom"              => "notNull str confidential",
      "prenom"           => "notNull str",
      "nom_jeune_fille"  => "str confidential",
      "nom_soundex2"     => "str",
      "prenom_soundex2"  => "str",
      "nomjf_soundex2"   => "str",
      "medecin_traitant" => "ref class|CMedecin",
      "medecin1"         => "ref class|CMedecin",
      "medecin2"         => "ref class|CMedecin",
      "medecin3"         => "ref class|CMedecin",
      "matricule"        => "code insee confidential",
      "regime_sante"     => "str",
      "SHS"              => "numchar length|8",
      "sexe"             => "enum list|m|f|j default|m",
      "adresse"          => "text confidential",
      "ville"            => "str confidential",
      "cp"               => "numchar length|5 confidential",
      "tel"              => "numchar length|10 confidential",
      "tel2"             => "numchar length|10 confidential",
      "incapable_majeur" => "bool",
      "ATNC"             => "bool",
      "naissance"        => "date confidential",
      "rques"            => "text",
      "listCim10"        => "text",
      "cmu"              => "date",
      "ald"              => "text",
      "rang_beneficiaire"=> "enum list|1|2|11|12|13",
      
      "pays"             => "str",
      "nationalite"      => "notNull enum list|local|etranger default|local",
      "lieu_naissance"   => "str",
      "profession"       => "str",
      
      "employeur_nom"     => "str confidential",
      "employeur_adresse" => "text",
      "employeur_cp"      => "numchar length|5",
      "employeur_ville"   => "str confidential",
      "employeur_tel"     => "numchar length|10 confidential",
      "employeur_urssaf"  => "numchar length|11 confidential",

      "prevenir_nom"      => "str confidential",
      "prevenir_prenom"   => "str",
      "prevenir_adresse"  => "text",
      "prevenir_cp"       => "numchar length|5",
      "prevenir_ville"    => "str confidential",
      "prevenir_tel"      => "numchar length|10 confidential",
      "prevenir_parente"  => "enum list|conjoint|enfant|ascendant|colateral|divers"
    );
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
      "remarques" => null
    );
  }
    
  function getValuesFromVitale() {
    if (null == $propsVitale = mbGetValueFromGetOrSession("vitale")) {
      return;
    }

    foreach ($propsVitale as $propVitale => $valVitale) {
      $this->$propVitale = $valVitale;
    }
    
    $this->updateFormFields();
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->nom = strtoupper($this->nom);
    $this->nom_jeune_fille = strtoupper($this->nom_jeune_fille);
    $this->prenom = ucwords(strtolower($this->prenom));
    
    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom; 
    $this->_prenoms = array($this->prenom);
    
    $aNaissance = split("-", $this->naissance);
    
    $this->_jour  = $aNaissance[2];
    $this->_mois  = $aNaissance[1];
    $this->_annee = $aNaissance[0];
    
    $this->_naissance = sprintf("%02d/%02d/%04d", $this->_jour, $this->_mois, $this->_annee);

    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);
    $this->_tel21 = substr($this->tel2, 0, 2);
    $this->_tel22 = substr($this->tel2, 2, 2);
    $this->_tel23 = substr($this->tel2, 4, 2);
    $this->_tel24 = substr($this->tel2, 6, 2);
    $this->_tel25 = substr($this->tel2, 8, 2);
    $this->_tel31 = substr($this->prevenir_tel, 0, 2);
    $this->_tel32 = substr($this->prevenir_tel, 2, 2);
    $this->_tel33 = substr($this->prevenir_tel, 4, 2);
    $this->_tel34 = substr($this->prevenir_tel, 6, 2);
    $this->_tel35 = substr($this->prevenir_tel, 8, 2);
    $this->_tel41 = substr($this->employeur_tel, 0, 2);
    $this->_tel42 = substr($this->employeur_tel, 2, 2);
    $this->_tel43 = substr($this->employeur_tel, 4, 2);
    $this->_tel44 = substr($this->employeur_tel, 6, 2);
    $this->_tel45 = substr($this->employeur_tel, 8, 2);

    $this->evalAge();
    
    if($this->_age != "??" && $this->_age <= 15)
      $this->_shortview = "Enf.";
    elseif($this->sexe == "m")
      $this->_shortview = "M.";
    elseif($this->sexe == "f")
      $this->_shortview = "Mme.";
    else
      $this->_shortview = "Mlle.";
    $this->_view = $this->_shortview." $this->nom $this->prenom";
    
    // Codes CIM10
    $this->_codes_cim10 = array();
    $arrayCodes = array();
    if($this->listCim10)
      $arrayCodes = explode("|", $this->listCim10);
    foreach($arrayCodes as $value) {
      $this->_codes_cim10[] = new CCodeCIM10($value, 1);
    }
    
    // Navigation fields
    global $AppUI;
    $this->_dossier_cabinet_url = self::$dossier_cabinet_prefix[$AppUI->user_prefs["DossierCabinet"]] . $this->_id;
  }
  
  
  function evalAge($date = null){
    if(!$date){
      $anjour   = date("Y");
      $moisjour = date("m");
      $jourjour = date("d");
    }else{
      $anjour   = substr($date, 0, 4);
      $moisjour = substr($date, 5, 2);
      $jourjour = substr($date, 8, 2);      
    }
    
    if($this->naissance != "0000-00-00") {
      $annais   = substr($this->naissance, 0, 4);
      $moisnais = substr($this->naissance, 5, 2);
      $journais = substr($this->naissance, 8, 2);
      $this->_age = $anjour-$annais;
      if($moisjour<$moisnais){$this->_age=$this->_age-1;}
      if($jourjour<$journais && $moisjour==$moisnais){$this->_age=$this->_age-1;}
    } else {
      $this->_age = "??";
    }
  }
  
  
  function updateDBFields() {
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

  	if (($this->_tel1 !== null) && ($this->_tel2 !== null) && ($this->_tel3 !== null) && ($this->_tel4 !== null) && ($this->_tel5 !== null)) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }
    if ($this->tel == "0000000000") {
      $this->tel = "";
    }

  	if (($this->_tel21 !== null) && ($this->_tel22 !== null) && ($this->_tel23 !== null) && ($this->_tel24 !== null) && ($this->_tel25 !== null)) {
      $this->tel2 = 
        $this->_tel21 .
        $this->_tel22 .
        $this->_tel23 .
        $this->_tel24 .
        $this->_tel25;
    }
    if ($this->tel2 == "0000000000") {
      $this->tel2 = "";
    }
    
    if (($this->_tel31 !== null) && ($this->_tel32 !== null) && ($this->_tel33 !== null) && ($this->_tel34 !== null) && ($this->_tel35 !== null)) {
      $this->prevenir_tel = 
        $this->_tel31 .
        $this->_tel32 .
        $this->_tel33 .
        $this->_tel34 .
        $this->_tel35;
    }
    if ($this->prevenir_tel == "0000000000") {
      $this->prevenir_tel = "";
    }

    if ($this->prevenir_tel == "0000000000") {
      $this->prevenir_tel = "";
    }
    
    if(($this->_tel41 !== null) && ($this->_tel42 !== null) && ($this->_tel43 !== null) && ($this->_tel44 !== null) && ($this->_tel45 !== null)) {
      $this->employeur_tel = 
        $this->_tel41 .
        $this->_tel42 .
        $this->_tel43 .
        $this->_tel44 .
        $this->_tel45;
    }
    if ($this->employeur_tel == "0000000000") {
      $this->employeur_tel = "";
    }

    if ($this->cp == "00000") {
      $this->cp = "";
    }

  	if(($this->_annee != null) && ($this->_mois != null) && ($this->_jour != null)) {
      $this->naissance = 
        $this->_annee . "-" .
        $this->_mois  . "-" .
        $this->_jour;
  	}
  }
  
  // Backward references
  function loadRefsSejours($where = null) {
    $sejour = new CSejour;
    if($this->patient_id){
      if ($where === null) {
        $where = array();
      }
      $where["patient_id"] = "= '$this->patient_id'";
      $order = "entree_prevue DESC";
      $this->_ref_sejours = $sejour->loadList($where, $order);
    }
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

  function loadRefsAddictions() {
    global $dPconfig;
    if ($this->patient_id && $dPconfig["dPcabinet"]["addictions"]) {
      $addiction = new CAddiction();
      
      // Chargement des addictions
      $where = array();
      $where["object_id"]    = "= '$this->patient_id'";
      $where["object_class"] = "= 'CPatient'";
      $order = "type ASC";
      $this->_ref_addictions = $addiction->loadList($where, $order);

      // Classement des addictions
      $this->_ref_types_addiction = array();
      
      foreach($this->_ref_addictions as $keyAddict => &$currAddict){
        $this->_ref_types_addiction[$currAddict->type][$keyAddict] = $currAddict;
      }
    }
  }
  
  function loadRefsAntecedents() {
    if ($this->patient_id) {
      $antecedent = new CAntecedent();
      
      // Chargement des antécédents
      $where = array();
      $where["object_id"]    = "= '$this->patient_id'";
      $where["object_class"] = "= 'CPatient'";
      $order = "type ASC";
      $antecedents = $antecedent->loadList($where, $order);

      // Classements des antécédants
      foreach ($antecedents as &$_antecedent) {
        $this->_ref_antecedents[$_antecedent->type][$_antecedent->_id] = $_antecedent;
      }
    }
  }

  function loadRefsTraitements() {
    if($this->patient_id){
      $this->_ref_traitements = new CTraitement;
      $where = array();
      $where["object_id"]    = "= '$this->patient_id'";
      $where["object_class"] = "= 'CPatient'";
      $order = "fin DESC, debut DESC";
      $this->_ref_traitements = $this->_ref_traitements->loadList($where, $order);
    }
  }
  
  function loadRefsAffectations() {
    $this->loadRefsSejours();
    // Affectation actuelle et prochaine affectation
    $where["sejour_id"] = db_prepare_in(array_keys($this->_ref_sejours));
    $order = "entree";
    $now = mbDateTime();
    
    $this->_ref_curr_affectation = new CAffectation();
    $where["entree"] = "< '$now'";
    $where["sortie"] = ">= '$now'";
    $this->_ref_curr_affectation->loadObject($where, $order);
    
    $this->_ref_next_affectation = new CAffectation();
    $where["entree"] = "> '$now'";
    $this->_ref_next_affectation->loadObject($where, $order);
  }
  
  
  function loadRefsDocs() {
    $docs_valid = parent::loadRefsDocs();
    if($docs_valid)
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

  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefsConsultations();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
    $this->loadRefsAffectations();
    $this->loadRefsAddictions();
    $this->loadRefsPrescriptions();
  }

  // Forward references
  function loadRefsFwd() {
    // medecin_traitant
    $this->_ref_medecin_traitant = new CMedecin();
    $this->_ref_medecin_traitant->load($this->medecin_traitant);

    // medecin1
    $this->_ref_medecin1 = new CMedecin();
    $this->_ref_medecin1->load($this->medecin1);

    // medecin2
    $this->_ref_medecin2 = new CMedecin();
    $this->_ref_medecin2->load($this->medecin2);

    // medecin3
    $this->_ref_medecin3 = new CMedecin();
    $this->_ref_medecin3->load($this->medecin3);
  }
  
  // Liste statique des codes CIM10 initiaux
  function loadStaticCIM10($user = 0) {
    
    // Liste des favoris
    if($user) {
      $favoris = new CFavoricim10;
      $where = array();
      $where["favoris_user"] = "= '$user'";
      $order = "favoris_code";
      $favoris = $favoris->loadList($where, $order);
      foreach($favoris as $key => $value) {
        $this->_static_cim10["favoris"][] = new CCodeCIM10($value->favoris_code, 1);
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
  
  function makeDHEUrl() {
    global $dPconfig;
    $this->_urlDHE       = "#";
    $this->_urlDHEParams = array();
    // Construction de l'URL
    if(CModule::getInstalled("dPsante400") && ($dPconfig["interop"]["mode_compat"] == "medicap")) {
	    $this->_urlDHE = $dPconfig["interop"]["base_url"];
	    $this->_urlDHEParams["logineCap"]       = "";
	    $this->_urlDHEParams["codeAppliExt"]    = "mediboard";
	    $this->_urlDHEParams["patIdentLogExt"]  = $this->patient_id;
	    $this->_urlDHEParams["patNom"]          = $this->nom;
	    $this->_urlDHEParams["patPrenom"]       = $this->prenom;
	    $this->_urlDHEParams["patNomJF"]        = $this->nom_jeune_fille;
	    $this->_urlDHEParams["patSexe"]         = $this->sexe == "m" ? "1" : "2";
	    $this->_urlDHEParams["patDateNaiss"]    = $this->_naissance;
	    $this->_urlDHEParams["patAd1"]          = $this->adresse;
	    $this->_urlDHEParams["patCP"]           = $this->cp;
	    $this->_urlDHEParams["patVille"]        = $this->ville;
	    $this->_urlDHEParams["patTel1"]         = $this->tel;
	    $this->_urlDHEParams["patTel2"]         = $this->tel2;
	    $this->_urlDHEParams["patTel3"]         = "";
	    $this->_urlDHEParams["patNumSecu"]      = substr($this->matricule, 0, 13);
	    $this->_urlDHEParams["patCleNumSecu"]   = substr($this->matricule, 13, 2);
	    $this->_urlDHEParams["interDatePrevue"] = "";
    }
  }

  function loadDossierComplet($permType = null) {
    $pat_id = $this->loadRefs();
    
    $this->canRead();
    $this->canEdit();
    $this->getNumDocsAndFiles($permType);
    
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
      $consult->loadRefsExamAudio();
      $consult->loadExamsComp();
      $consult->getNumDocsAndFiles($permType);
      
      $consult->loadRefsFwd();
      $consult->canRead();
      $consult->canEdit();
    }
    
    // Sejours
    foreach ($this->_ref_sejours as $keySejour => $valueSejour) {
      $sejour =& $this->_ref_sejours[$keySejour];
      
      $sejour->loadRefsAffectations();
      $sejour->loadRefsOperations();
      $sejour->getNumDocsAndFiles($permType);
      
      $sejour->loadRefsFwd();
      $sejour->canRead();
      $sejour->canEdit();
      $sejour->getNumDocsAndFiles($permType);
      foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
        $operation =& $sejour->_ref_operations[$keyOp];
        $operation->loadRefsFwd();
        $operation->getNumDocsAndFiles($permType);
        $operation->canRead();
        $operation->canEdit();
      }
    }
  }
  
  function checkSimilar($nom, $prenom) {
    $soundex2 = new soundex2;
    $testNom    = $this->nom_soundex2    == $soundex2->build($nom);
    $testPrenom = $this->prenom_soundex2 == $soundex2->build($prenom);
    return($testNom && $testPrenom);
  }
  
  function getSiblings() {
  	$where = array();
    
    if($this->patient_id) {
      $where["patient_id"] = "!= '$this->patient_id'";
    }
    
    $where[] = db_prepare("((nom = %1 AND prenom = %2) " .
                            "OR (nom = %1 AND naissance = %3 AND naissance != '0000-00-00') " .
                            "OR (prenom = %2 AND naissance = %3 AND naissance != '0000-00-00'))",
                          $this->nom, $this->prenom, $this->naissance);

    $siblings = new CPatient;
    $siblings = $siblings->loadList($where);
    return $siblings;
  }

  function getExactSiblings() {
    $sql = "SELECT patient_id, nom, prenom, naissance, adresse, ville, CP " .
      		"FROM patients WHERE " .
      		"patient_id != '$this->patient_id' " .
      		"AND nom    = '".addslashes($this->nom)."'" .
      		"AND prenom = '".addslashes($this->prenom)."'" .
      		"AND naissance = '$this->naissance'";
    $siblings = db_loadlist($sql);
    return $siblings;
  }
  
  function fillTemplate(&$template) {
  	global $AppUI;
    $this->loadRefsFwd();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
    $template->addProperty("Patient - article"           , $this->_shortview );
    $template->addProperty("Patient - nom"               , $this->nom        );
    $template->addProperty("Patient - prénom"            , $this->prenom     );
    $template->addProperty("Patient - adresse"           , $this->adresse    );
    $template->addProperty("Patient - ville"             , $this->ville      );
    $template->addProperty("Patient - cp"                , $this->cp         );
    $template->addProperty("Patient - âge"               , $this->_age       );
    $template->addProperty("Patient - date de naissance" , $this->_naissance );
    $template->addProperty("Patient - téléphone"         , $this->tel        );
    $template->addProperty("Patient - mobile"            , $this->tel2       );
    if($this->medecin_traitant) {
      $template->addProperty("Patient - médecin traitant"          , "{$this->_ref_medecin_traitant->nom} {$this->_ref_medecin_traitant->prenom}");
      $template->addProperty("Patient - médecin traitant - adresse", "{$this->_ref_medecin_traitant->adresse}\n{$this->_ref_medecin_traitant->cp} {$this->_ref_medecin_traitant->ville}");
    } else {
      $template->addProperty("Patient - médecin traitant");
      $template->addProperty("Patient - médecin traitant - adresse");
    }
    if($this->medecin1) {
      $template->addProperty("Patient - médecin correspondant 1"          , "{$this->_ref_medecin1->nom} {$this->_ref_medecin1->prenom}");
      $template->addProperty("Patient - médecin correspondant 1 - adresse", "{$this->_ref_medecin1->adresse}\n{$this->_ref_medecin1->cp} {$this->_ref_medecin1->ville}");
    } else {
      $template->addProperty("Patient - médecin correspondant 1");
      $template->addProperty("Patient - médecin correspondant 1 - adresse");
    }
    if($this->medecin2) {
      $template->addProperty("Patient - médecin correspondant 2"          , "{$this->_ref_medecin2->nom} {$this->_ref_medecin2->prenom}");
      $template->addProperty("Patient - médecin correspondant 2 - adresse", "{$this->_ref_medecin2->adresse}\n{$this->_ref_medecin2->cp} {$this->_ref_medecin2->ville}");
    } else {
      $template->addProperty("Patient - médecin correspondant 2");
      $template->addProperty("Patient - médecin correspondant 2 - adresse");
    }
    if($this->medecin3) {
      $template->addProperty("Patient - médecin correspondant 3"          , "{$this->_ref_medecin3->nom} {$this->_ref_medecin3->prenom}");
      $template->addProperty("Patient - médecin correspondant 3 - adresse", "{$this->_ref_medecin3->adresse}\n{$this->_ref_medecin3->cp} {$this->_ref_medecin3->ville}");
    } else {
      $template->addProperty("Patient - médecin correspondant 3");
      $template->addProperty("Patient - médecin correspondant 3 - adresse");
    }
    
    if(is_array($this->_ref_antecedents)){
      // Réécritude des antécédents
      $sAntecedents = null;
      foreach($this->_ref_antecedents as $keyAnt=>$currTypeAnt){
        if($currTypeAnt){
          if($sAntecedents){$sAntecedents.="<br />";}
          $sAntecedents .= $AppUI->_("CAntecedent.type.".$keyAnt)."\n";
          foreach($currTypeAnt as $currAnt){
            $sAntecedents .= " &bull; ";
            if($currAnt->date){
              $sAntecedents .= substr($currAnt->date, 8, 2) ."/";
              $sAntecedents .= substr($currAnt->date, 5, 2) ."/";
              $sAntecedents .= substr($currAnt->date, 0, 4) ." : ";
            }
            $sAntecedents .= $currAnt->rques;
          }
        }
      }
      $template->addProperty("Patient - antécédents", $sAntecedents);
    }else{
      $template->addProperty("Patient - antécédents");
    }
    
    if($this->_ref_traitements){
      $sTrmt = null;
      foreach($this->_ref_traitements as $curr_trmt){
        if($sTrmt){$sTrmt.=" &bull; ";}
        if ($curr_trmt->fin){
          $sTrmt .= "Du ";
          $sTrmt .= substr($curr_trmt->debut, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 0, 4) ." au ";
          $sTrmt .= substr($curr_trmt->fin, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->fin, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->fin, 0, 4) ." : ";
        }elseif($curr_trmt->debut){
          $sTrmt .= "Depuis le ";
          $sTrmt .= substr($curr_trmt->debut, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 0, 4) ." : ";
        }
        $sTrmt .= $curr_trmt->traitement;
      }
      $template->addProperty("Patient - traitements", $sTrmt);
    }else{
      $template->addProperty("Patient - traitements");
    }
    
    if($this->_codes_cim10){
      $sCim10 = null;
      foreach($this->_codes_cim10 as $curr_code){
        if($sCim10){$sCim10.=" &bull; ";}
        $sCim10 .= $curr_code->code . " : " . $curr_code->libelle;
      }
      $template->addProperty("Patient - diagnostics", $sCim10);
    }else{
      $template->addProperty("Patient - diagnostics");
    }
  }
}

?>