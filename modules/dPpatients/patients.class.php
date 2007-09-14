<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPpatients", "dossierMedical"));

/**
 * The CPatient Class
 */
class CPatient extends CDossierMedical {
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
  var $cmu              = null;
  var $ald              = null;
  var $rang_beneficiaire= null;
  var $rang_naissance   = null;
  
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

  // Assuré
  var $assure_nom              = null;
  var $assure_nom_jeune_fille  = null;
  var $assure_prenom           = null;
  var $assure_naissance        = null;
  var $assure_sexe             = null;
  var $assure_adresse          = null;
  var $assure_ville            = null;
  var $assure_cp               = null;
  var $assure_tel              = null;
  var $assure_tel2             = null;
  var $assure_pays             = null;
  var $assure_nationalite      = null;
  var $assure_lieu_naissance   = null;
  var $assure_profession       = null;
  var $assure_rques            = null;
  var $assure_matricule        = null;
  
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
  
  // Assuré
  var $_assure_naissance   = null;
  var $_assure_jour        = null;
  var $_assure_mois        = null;
  var $_assure_annee       = null;
  var $_assure_tel1        = null;
  var $_assure_tel2        = null;
  var $_assure_tel3        = null;
  var $_assure_tel4        = null;
  var $_assure_tel5        = null;
  var $_assure_tel21       = null;
  var $_assure_tel22       = null;
  var $_assure_tel23       = null;
  var $_assure_tel24       = null;
  var $_assure_tel25       = null;
  
  
  // Navigation Fields
  var $_dossier_cabinet_url = null;

  // HPRIM Fields
  var $_prenoms        = null; // multiple
  var $_nom_naissance  = null; // +/- = nom_jeune_fille
  var $_adresse_ligne2 = null;
  var $_adresse_ligne3 = null;
  var $_pays           = null;
  var $_IPP            = null;
  
  // DHE Fields
  var $_urlDHE       = null;
  var $_urlDHEParams = null;

  // Object References
  var $_nb_docs              = null;
  var $_ref_sejours          = null;
  var $_ref_consultations    = null;
  var $_ref_prescriptions    = null;
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
  	
    $specs = parent::getSpecs();
    global $dPconfig;
    
    $specs["nom"]               = "notNull str confidential";
    $specs["prenom"]            = "notNull str";
    $specs["nom_jeune_fille"]   = "str confidential";
    $specs["nom_soundex2"]      = "str";
    $specs["prenom_soundex2"]   = "str";
    $specs["nomjf_soundex2"]    = "str";
    $specs["medecin_traitant"]  = "ref class|CMedecin";
    $specs["medecin1"]          = "ref class|CMedecin";
    $specs["medecin2"]          = "ref class|CMedecin";
    $specs["medecin3"]          = "ref class|CMedecin";
    $specs["matricule"]         = "code insee confidential";
    $specs["regime_sante"]      = "str";
    $specs["SHS"]               = "numchar length|8";
    $specs["sexe"]              = "enum list|m|f|j default|m";
    $specs["adresse"]           = "text confidential";
    $specs["ville"]             = "str confidential";
    $specs["cp"]                = "numchar minLength|4 maxLength|5 confidential";
    $specs["tel"]               = "numchar length|10 confidential";
    $specs["tel2"]              = "numchar length|10 confidential";
    $specs["incapable_majeur"]  = "bool";
    $specs["ATNC"]              = "bool";
    $specs["naissance"]         = "date confidential";
    $specs["rques"]             = "text";
    $specs["cmu"]               = "date";
    $specs["ald"]               = "text";
    $specs["rang_beneficiaire"] = "enum list|01|02|11|12|13";
    $specs["rang_naissance"]    = "enum list|1|2|3|4|5|6 default|1";
    
    $specs["pays"]              = "str";
    $specs["nationalite"]       = "notNull enum list|local|etranger default|local";
    $specs["lieu_naissance"]    = "str";
    $specs["profession"]        = "str";
      
    $specs["employeur_nom"]      = "str confidential";
    $specs["employeur_adresse"]  = "text";
    $specs["employeur_cp"]       = "numchar length|5";
    $specs["employeur_ville"]    = "str confidential";
    $specs["employeur_tel"]      = "numchar length|10 confidential";
    $specs["employeur_urssaf"]   = "numchar length|11 confidential";

    $specs["prevenir_nom"]       = "str confidential";
    $specs["prevenir_prenom"]    = "str";
    $specs["prevenir_adresse"]   = "text";
    $specs["prevenir_cp"]        = "numchar length|5";
    $specs["prevenir_ville"]     = "str confidential";
    $specs["prevenir_tel"]       = "numchar length|10 confidential";
    $specs["prevenir_parente"]   = "enum list|conjoint|enfant|ascendant|colateral|divers";
    
    $specs["assure_nom"]               = "str confidential";
    $specs["assure_prenom"]            = "str";
    $specs["assure_nom_jeune_fille"]   = "str confidential";
    $specs["assure_sexe"]              = "enum list|m|f|j default|m";
    $specs["assure_naissance"]         = "date confidential";
    $specs["assure_adresse"]           = "text confidential";
    $specs["assure_ville"]             = "str confidential";
    $specs["assure_cp"]                = "numchar minLength|4 maxLength|5 confidential";
    $specs["assure_tel"]               = "numchar length|10 confidential";
    $specs["assure_tel2"]              = "numchar length|10 confidential";
    $specs["assure_pays"]              = "str";
    $specs["assure_nationalite"]       = "notNull enum list|local|etranger default|local";
    $specs["assure_lieu_naissance"]    = "str";
    $specs["assure_profession"]        = "str";
    $specs["assure_rques"]             = "text";
    $specs["assure_matricule"]         = "code insee confidential";
    
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
    
    global $dPconfig;

    $this->nom = strtoupper($this->nom);
    $this->nom_jeune_fille = strtoupper($this->nom_jeune_fille);
    $this->prenom = ucwords(strtolower($this->prenom));
    
    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom; 
    $this->_prenoms = array($this->prenom);
    
    $aNaissance = split("-", $this->naissance);
    
    $this->_jour  = $aNaissance[2];
    $this->_mois  = $aNaissance[1];
    $this->_annee = $aNaissance[0];
    
    $this->_naissance = mbDateToLocale($this->naissance);

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
    
    // Assuré
    if($this->assure_naissance) {
      $aNaissance = split("-", $this->assure_naissance);
    
      $this->_assure_jour  = $aNaissance[2];
      $this->_assure_mois  = $aNaissance[1];
      $this->_assure_annee = $aNaissance[0];
    
      $this->_assure_naissance = sprintf("%02d/%02d/%04d", $this->_jour, $this->_mois, $this->_annee);
    }

    $this->_assure_tel1 = substr($this->assure_tel, 0, 2);
    $this->_assure_tel2 = substr($this->assure_tel, 2, 2);
    $this->_assure_tel3 = substr($this->assure_tel, 4, 2);
    $this->_assure_tel4 = substr($this->assure_tel, 6, 2);
    $this->_assure_tel5 = substr($this->assure_tel, 8, 2);
    $this->_assure_tel21 = substr($this->assure_tel2, 0, 2);
    $this->_assure_tel22 = substr($this->assure_tel2, 2, 2);
    $this->_assure_tel23 = substr($this->assure_tel2, 4, 2);
    $this->_assure_tel24 = substr($this->assure_tel2, 6, 2);
    $this->_assure_tel25 = substr($this->assure_tel2, 8, 2);
    
    if($this->_age != "??" && $this->_age <= 15)
      $this->_shortview = "Enf.";
    elseif($this->sexe == "m")
      $this->_shortview = "M.";
    elseif($this->sexe == "f")
      $this->_shortview = "Mme.";
    else
      $this->_shortview = "Mlle.";
    $this->_view = $this->_shortview." $this->nom $this->prenom";
    
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
  
  function updateAssureField($field) {
    if ($this->rang_beneficiaire == "01") {
      $assure_field = "assure_$field";
      $this->$assure_field = $this->$field;
    }
  }
  
  function updateDBFields() {
  	 global $dPconfig;
  	 
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
  	
  	// Assuré

    // Assuré = patient ssi rang du bénéficiaire vaut 1
    $this->updateAssureField("nom");
    $this->updateAssureField("nom_jeune_fille");
    $this->updateAssureField("prenom");
    $this->updateAssureField("naissance");
    $this->updateAssureField("sexe");
    $this->updateAssureField("adresse");
    $this->updateAssureField("ville");
    $this->updateAssureField("cp");
    $this->updateAssureField("tel");
    $this->updateAssureField("tel2");
    $this->updateAssureField("pays");
    $this->updateAssureField("nationalite");
    $this->updateAssureField("lieu_naissance");
    $this->updateAssureField("profession");
    $this->updateAssureField("rques");
    $this->updateAssureField("matricule");

    if ($this->assure_nom) {
  	  $this->assure_nom = strtoupper($this->assure_nom);
    }
    
    if ($this->assure_nom_jeune_fille) {
  	  $this->assure_nom_jeune_fille = strtoupper($this->assure_nom_jeune_fille);
    }

    if ($this->assure_prenom) {
      $this->assure_prenom = ucwords(strtolower($this->assure_prenom));
    }

  	if (($this->_assure_tel1 !== null) && ($this->_assure_tel2 !== null) && ($this->_assure_tel3 !== null) && ($this->_assure_tel4 !== null) && ($this->_assure_tel5 !== null)) {
      $this->assure_tel = 
        $this->_assure_tel1 .
        $this->_assure_tel2 .
        $this->_assure_tel3 .
        $this->_assure_tel4 .
        $this->_assure_tel5;
    }
    if ($this->assure_tel == "0000000000") {
      $this->assure_tel = "";
    }

  	if (($this->_assure_tel21 !== null) && ($this->_assure_tel22 !== null) && ($this->_assure_tel23 !== null) && ($this->_assure_tel24 !== null) && ($this->_assure_tel25 !== null)) {
      $this->assure_tel2 = 
        $this->_assure_tel21 .
        $this->_assure_tel22 .
        $this->_assure_tel23 .
        $this->_assure_tel24 .
        $this->_assure_tel25;
    }
    if ($this->assure_tel2 == "0000000000") {
      $this->assure_tel2 = "";
    }

    if ($this->assure_cp == "00000") {
      $this->assure_cp = "";
    }

  	if(($this->_assure_annee != null) && ($this->_assure_mois != null) && ($this->_assure_jour != null)) {
      $this->assure_naissance = 
        $this->_assure_annee . "-" .
        $this->_assure_mois  . "-" .
        $this->_assure_jour;
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
  
  
  function loadIPP(){
  	global $dPconfig, $g;
  	// Récuperation du tag de l'id Externe (ex: sherpa group:10)
    
  	// si pas de fichier de config ==> IPP = id mediboard
  	if(!$dPconfig["dPpatients"]["CPatient"]["tag_ipp"]){
  		$this->_IPP = "";
    	return;
    }
    
    // sinon, $_IPP = valeur id400
    // creation du tag de l'id Externe
  	$tag = str_replace('$g',$g, $dPconfig["dPpatients"]["CPatient"]["tag_ipp"]);

  	// Recuperation de la valeur de l'id400
  	$id400 = new CIdSante400();
    $id400->loadLatestFor($this, $tag);
  	
    // Stockage de la valeur de l'id400
    $this->_IPP = $id400->id400;
    
    // Si pas d'id400 correspondant, on stocke "_"
    if(!$this->_IPP){
    	$this->_IPP = "-";
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
    
    $where[] = $this->_spec->ds->prepare("((nom = %1 AND prenom = %2) " .
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
    $siblings = $this->_spec->ds->loadlist($sql);
    return $siblings;
  }
  
  function fillTemplate(&$template) {
    $this->loadRefsFwd();

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
    
    // Dossier médical
    parent::fillTemplate($template);
  }
}

?>