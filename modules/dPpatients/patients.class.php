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
  // DB Table key
  var $patient_id = null;

  // DB Fields
  var $nom              = null;
  var $nom_jeune_fille  = null;
  var $prenom           = null;
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
  
  // A rajouter dans la base
  var $pays             = null;
  var $nationalite      = null;
  var $lieu_naissance   = null;
  var $profession       = null;
  
  // A rajouter dans la base
  var $employeur_nom     = null;
  var $employeur_adresse = null;
  var $employeur_cp      = null;
  var $employeur_ville   = null;
  var $employeur_tel     = null;
  var $employeur_urssaf  = null;

  // A rajouter dans la base
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
  
  // HPRIM Fields
  var $_prenoms        = null; // multiple
  var $_nom_naissance  = null; // +/- = nom_jeune_fille
  var $_adresse_ligne2 = null;
  var $_adresse_ligne3 = null;
  var $_pays           = null;

  // Object References
  var $_nb_docs              = null;
  var $_fin_cmu              = null;
  var $_ref_files            = null;
  var $_ref_documents        = null;
  var $_ref_sejours          = null;
  var $_ref_consultations    = null;
  var $_ref_antecedents      = null;
  var $_ref_traitements      = null;
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

  function getSpecs() {
    return array (
      "nom"              => "str|notNull|confidential",
      "prenom"           => "str|notNull",
      "nom_jeune_fille"  => "str|confidential",
      "medecin_traitant" => "ref",
      "medecin1"         => "ref",
      "medecin2"         => "ref",
      "medecin3"         => "ref",
      "matricule"        => "code|insee|confidential",
      "regime_sante"     => "str",
      "SHS"              => "num|length|8",
      "sexe"             => "enum|m|f|j",
      "adresse"          => "str|confidential",
      "ville"            => "str|confidential",
      "cp"               => "num|length|5|confidential",
      "tel"              => "num|length|10|confidential",
      "tel2"             => "num|length|10|confidential",
      "incapable_majeur" => "enum|o|n",
      "ATNC"             => "enum|o|n",
      "naissance"        => "date|confidential",
      "rques"            => "text",
      "listCim10"        => "str",
      "cmu"              => "date",
      "ald"              => "text",
      
      "pays"             => "str",
      "nationalite"      => "enum|local|etranger|notNull",
      "lieu_naissance"   => "str",
      "profession"       => "str",
      
      "employeur_nom"     => "str|confidential",
      "employeur_adresse" => "text",
      "employeur_cp"      => "num|length|5",
      "employeur_ville"   => "str|confidential",
      "employeur_tel"     => "num|length|10|confidential",
      "employeur_urssaf"  => "num|length|11|confidential",

      "prevenir_nom"      => "str|confidential",
      "prevenir_prenom"   => "str",
      "prevenir_adresse"  => "text",
      "prevenir_cp"       => "num|length|5",
      "prevenir_ville"    => "str|confidential",
      "prevenir_tel"      => "num|length|10|confidential",
      "prevenir_parente"  => "enum|conjoint|enfant|ascendant|colateral|divers",
    );
  }
  
  function getSeeks() {
    return array(
      "nom"    => "likeBegin",
      "prenom" => "likeBegin",
      "ville"  => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();

    $this->nom = strtoupper($this->nom);
    $this->nom_jeune_fille = strtoupper($this->nom_jeune_fille);
    $this->prenom = ucwords(strtolower($this->prenom));
    
    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom; 
    $this->_prenoms = array($this->prenom);

    $this->_jour  = substr($this->naissance, 8, 2);
    $this->_mois  = substr($this->naissance, 5, 2);
    $this->_annee = substr($this->naissance, 0, 4);
    
    $this->_naissance = "$this->_jour/$this->_mois/$this->_annee";

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


    if($this->naissance != "0000-00-00") {
      $annais = substr($this->naissance, 0, 4);
      $anjour = date("Y");
      $moisnais = substr($this->naissance, 5, 2);
      $moisjour = date("m");
      $journais = substr($this->naissance, 8, 2);
      $jourjour = date("d");
      $this->_age = $anjour-$annais;
      if($moisjour<$moisnais){$this->_age=$this->_age-1;}
      if($jourjour<$journais && $moisjour==$moisnais){$this->_age=$this->_age-1;}
    } else
      $this->_age = "??";
    
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
  }
  
  function updateDBFields() {
    if ($this->nom) {
  	  $this->nom = strtoupper($this->nom);
    }
    
    if ($this->nom_jeune_fille) {
  	  $this->nom_jeune_fille = strtoupper($this->nom_jeune_fille);
    }

    if ($this->prenom) {
      $this->prenom = ucwords(strtolower($this->prenom));
    }

  	if (($this->_tel1 !== null) && ($this->_tel2 !== null) && ($this->_tel3 !== null) && ($this->_tel4 !== null) && ($this->_tel5 !== null)) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }

  	if(($this->_tel21 !== null) && ($this->_tel22 !== null) && ($this->_tel23 !== null) && ($this->_tel24 !== null) && ($this->_tel25 !== null)) {
      $this->tel2 = 
        $this->_tel21 .
        $this->_tel22 .
        $this->_tel23 .
        $this->_tel24 .
        $this->_tel25;
    }
    
    if(($this->_tel31 !== null) && ($this->_tel32 !== null) && ($this->_tel33 !== null) && ($this->_tel34 !== null) && ($this->_tel35 !== null)) {
      $this->prevenir_tel = 
        $this->_tel31 .
        $this->_tel32 .
        $this->_tel33 .
        $this->_tel34 .
        $this->_tel35;
    }
    
    if(($this->_tel41 !== null) && ($this->_tel42 !== null) && ($this->_tel43 !== null) && ($this->_tel44 !== null) && ($this->_tel45 !== null)) {
      $this->employeur_tel = 
        $this->_tel41 .
        $this->_tel42 .
        $this->_tel43 .
        $this->_tel44 .
        $this->_tel45;
    }

  	if(($this->_annee != null) && ($this->_mois != null) && ($this->_jour != null)) {
      $this->naissance = 
        $this->_annee . "-" .
        $this->_mois  . "-" .
        $this->_jour;
  	}
  }

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label" => "sejour(s)", 
      "name" => "sejour", 
      "idfield" => "sejour_id", 
      "joinfield" => "patient_id"
    );
    $tables[] = array (
      "label" => "consultation(s)", 
      "name" => "consultation", 
      "idfield" => "consultation_id", 
      "joinfield" => "patient_id"
    );
    $tables[] = array (
      "label" => "antécédent(s)", 
      "name" => "antecedent", 
      "idfield" => "antecedent_id", 
      "joinfield" => "patient_id"
    );
    $tables[] = array (
      "label" => "traitement(s)", 
      "name" => "traitement", 
      "idfield" => "traitement_id", 
      "joinfield" => "patient_id"
    );
    $tables[] = array (
      "label" => "fichier(s)", 
      "name" => "files_mediboard", 
      "idfield" => "file_id", 
      "joinfield" => "file_object_id",
      "joinon" => "`file_class`='CPatient'"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  // Backward references
  function loadRefsSejours() {
    $sejour = new CSejour;
    $where = array();
    $where["patient_id"] = "= '$this->patient_id'";
    $order = "entree_prevue DESC";
    $this->_ref_sejours = $sejour->loadList($where, $order);
  }
  
  function loadRefsConsultations() {
    $this->_ref_consultations = new CConsultation();
    $where = array();
    $where["patient_id"] = "= '$this->patient_id'";
    $order = "plageconsult.date DESC";
    $leftjoin = array();
    $leftjoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
    $this->_ref_consultations = $this->_ref_consultations->loadList($where, $order, null, null, $leftjoin);
  }
  
  function loadRefsAntecedents() {
    $this->_ref_antecedents = new CAntecedent;
    $where = array();
    $where["patient_id"] = "= '$this->patient_id'";
    $order = "type ASC";
    $this->_ref_antecedents = $this->_ref_antecedents->loadList($where, $order);
  }

  function loadRefsTraitements() {
    $this->_ref_traitements = new CTraitement;
    $where = array();
    $where["patient_id"] = "= '$this->patient_id'";
    $order = "fin DESC, debut DESC";
    $this->_ref_traitements = $this->_ref_traitements->loadList($where, $order);
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

  function loadRefsFiles() {
    $this->_ref_files = new CFile();
    $this->_ref_files = $this->_ref_files->loadFilesForObject($this);
  }
  
  
  function loadRefsDocs() {
    $this->_ref_documents = array();
    $this->_ref_documents = new CCompteRendu();
    
    $where = array();
    $where["type"] = " = 'patient'";
    $where["object_id"] = "= '$this->patient_id'";
    $order = "nom";
    
    $this->_ref_documents = $this->_ref_documents->loadList($where, $order);
    $docs_valid = 0;
    foreach ($this->_ref_documents as $curr_doc) {
      if ($curr_doc->source) {
        $docs_valid++;
      }
    }
    if($docs_valid)
      $this->_nb_docs .= "$docs_valid";
  }


  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefsConsultations();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
    $this->loadRefsAffectations();
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

  function loadDossierComplet() {
    $this->loadRefs();
    $this->canRead();
    $this->canEdit();
    
    // Affectations courantes
    $affectation =& $this->_ref_curr_affectation;
    if ($affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
    
    $affectation =& $this->_ref_next_affectation;
    if ($affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }
  
    // Consultations
    foreach ($this->_ref_consultations as $keyConsult => $valueConsult) {
      $consult =& $this->_ref_consultations[$keyConsult];
      $consult->loadRefs();
      $consult->getNumDocs();
      $consult->canRead();
      $consult->canEdit();
    }
    
    // Sejours
    foreach ($this->_ref_sejours as $keySejour => $valueSejour) {
      $sejour =& $this->_ref_sejours[$keySejour];
      $sejour->loadRefs();
      $sejour->canRead();
      $sejour->canEdit();
      foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
        $operation =& $sejour->_ref_operations[$keyOp];
        $operation->loadRefsFwd();
        $operation->canRead();
        $operation->canEdit();
      }
    }
  }
  
  function verifCmuEtat($dateref = null){
    if(!$dateref){
      $dateref = mbDate();
    }
    if($dateref <= $this->cmu){
      $this->_fin_cmu = true; 
    }
  }
  
  function getSiblings() {
  	$where = array();
    $where["patient_id"] = db_prepare("!= %", $this->patient_id);
    
    
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
  	$this->loadRefsFwd();
    $template->addProperty("Patient - nom"               , $this->nom        );
    $template->addProperty("Patient - prénom"            , $this->prenom     );
    $template->addProperty("Patient - article"           , $this->_shortview );
    $template->addProperty("Patient - adresse"           , $this->adresse    );
    $template->addProperty("Patient - ville"             , $this->ville      );
    $template->addProperty("Patient - cp"                , $this->cp         );
    $template->addProperty("Patient - âge"               , $this->_age       );
    $template->addProperty("Patient - date de naissance" , $this->_naissance );
    $template->addProperty("Patient - téléphone"         , $this->tel        );
    $template->addProperty("Patient - mobile"            , $this->tel2       );
    if($this->medecin_traitant) {
      $template->addProperty("Patient - médecin traitant"          , "{$this->_ref_medecin_traitant->nom} {$this->_ref_medecin_traitant->prenom}");
      $template->addProperty("Patient - médecin traitant - adresse", "".nl2br($this->_ref_medecin_traitant->adresse)."<br />{$this->_ref_medecin_traitant->cp} {$this->_ref_medecin_traitant->ville}");
    } else {
      $template->addProperty("Patient - médecin traitant");
      $template->addProperty("Patient - médecin traitant - adresse");
    }
    if($this->medecin1) {
      $template->addProperty("Patient - médecin correspondant 1"          , "{$this->_ref_medecin1->nom} {$this->_ref_medecin1->prenom}");
      $template->addProperty("Patient - médecin correspondant 1 - adresse", "".nl2br($this->_ref_medecin1->adresse)."<br />{$this->_ref_medecin1->cp} {$this->_ref_medecin1->ville}");
    } else {
      $template->addProperty("Patient - médecin correspondant 1");
      $template->addProperty("Patient - médecin correspondant 1 - adresse");
    }
    if($this->medecin2) {
      $template->addProperty("Patient - médecin correspondant 2"          , "{$this->_ref_medecin2->nom} {$this->_ref_medecin2->prenom}");
      $template->addProperty("Patient - médecin correspondant 2 - adresse", "".nl2br($this->_ref_medecin2->adresse)."<br />{$this->_ref_medecin2->cp} {$this->_ref_medecin2->ville}");
    } else {
      $template->addProperty("Patient - médecin correspondant 2");
      $template->addProperty("Patient - médecin correspondant 2 - adresse");
    }
    if($this->medecin3) {
      $template->addProperty("Patient - médecin correspondant 3"          , "{$this->_ref_medecin3->nom} {$this->_ref_medecin3->prenom}");
      $template->addProperty("Patient - médecin correspondant 3 - adresse", "".nl2br($this->_ref_medecin3->adresse)."<br />{$this->_ref_medecin3->cp} {$this->_ref_medecin3->ville}");
    } else {
      $template->addProperty("Patient - médecin correspondant 3");
      $template->addProperty("Patient - médecin correspondant 3 - adresse");
    }
  }
}

?>