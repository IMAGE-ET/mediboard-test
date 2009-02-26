<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbProduit extends CBcbObject {
  
  var $code_cip              = null;
  var $code_ucd              = null;
  var $libelle               = null;
  var $nom_commercial        = null;
  var $forme                 = null;
  var $formes                = null;
  var $nb_ucd                = null;
  var $hospitalier           = null;
  var $nom_laboratoire       = null;
  
  var $code_statut           = null;
  var $libelle_statut        = null;
  var $numero_AMM            = null;
  var $date_AMM              = null;
  var $agrement              = null;

  // Others Fields
  var $_referent             = null;
  var $_generique            = null;
  var $_supprime             = null;
  
  var $voies                      = null;
  var $libelle_presentation       = null;
  var $nb_presentation            = null;
  var $libelle_unite_presentation = null;
  var $libelle_conditionnement    = null;
  var $rapport_unite_prise        = null;
  var $dosages                    = null;
  
  var $inLivret = null;
  var $inT2A = true;
  
  // Objects references
  var $_ref_DCI              = null;
  var $_ref_UCD              = null;
  var $_ref_monographie      = null;
  var $_ref_composition      = null;
  var $_ref_economique       = null;
  var $_ref_classes_ATC      = null;
  var $_ref_classes_thera    = null;
  var $_ref_equivalents      = null;
  var $_ref_posologies       = null;
  var $_unite_dispensation   = null;
  var $_unite_administration = null;
  var $_ref_fiches_ATC       = null;
  var $_ref_ATC_2_libelle = null;
  var $_ref_ATC_2_code    = null;
  
	static $loaded = array();
	static $useCount = 0;
    
	/**
	 * Chargement avec mise en cache
	 */
	static function get($code_cip, $full_mode = true) {
		self::$useCount++;
		
    if (!CAppUI::conf("dPmedicament CBcbProduit use_cache") || !$full_mode) {
		  $produit = new CBcbProduit();
		  $produit->load($code_cip, $full_mode);
		  return $produit;
		}

		// On instancie si ca n'existe pas
		if (!isset(self::$loaded[$code_cip])) {
			self::$loaded[$code_cip] = new CBcbProduit();
 	    self::$loaded[$code_cip]->load($code_cip, $full_mode);
		} 
		
  	$produit =& self::$loaded[$code_cip];
   	return $produit->copy();
	}
	
	// Constructeur
  function CBcbProduit(){
    $this->distClass = "BCBProduit";
    parent::__construct();
  }
  
	/**
	 * Should use clone with appropriate behaviour
	 * But a bit complicated to implement
	 */
	function copy() {
	  $obj = unserialize(serialize($this));
	  return $obj;
	  
	}
  
  // Chargement d'un produit
  function load($code_cip, $full_mode = true){
    $this->distObj->SearchInfo($code_cip);
    
    $infoProduit = $this->distObj->DataInfo;

    if ($infoProduit->Charge == 1) {
      $this->code_cip        = $infoProduit->Code_CIP;
      if (isset($infoProduit->Code_Ucd)) {
        $this->code_ucd        = $infoProduit->Code_Ucd;
      }
      $this->libelle         = $infoProduit->Libelle;
      $this->nom_commercial  = $infoProduit->NomCommercial;
      $this->forme           = $infoProduit->Forme;
      $this->formes          = $infoProduit->Formes;
      $this->nb_ucd          = $infoProduit->Nb_UCD;
      $this->hospitalier     = $infoProduit->Hospitalier;
      $this->nom_laboratoire = $infoProduit->Laboratoire;
    }
    
   if ($full_mode){
	    // Chargement de la monographie (permet d'obtenir la date de suppression) 
	    $this->loadRefMonographie();
	
	    // Chargement du statut du produit
	    $this->getStatut();  
	    
	    // Chargement de l'agrement
	    $this->getAgrement();

	    // Produit supprime ?
	    $this->getSuppression();
	
	    // Chargement de la composition du produit
	    $this->loadRefComposition();

	    // Permet de savoir si le produit appartient au livret therapeutique
	    $this->isInLivret();
	    
	    // Produit dans la T2A
	    $this->isInT2A();
	    
	  	// Produit générique ?
	    $this->getGenerique();
	   
	    // Produit référent ?
	    $this->getReferent();

	    $this->loadRefPosologies();
	    $this->loadLibellePresentation();
      $this->loadUnitePresentation();
    }
     
    $this->loadVoies();
  }
  
  
  function loadVoies(){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT IDENT_VOIES.LIBELLEVOIE FROM `IDENT_VOIES`
							LEFT JOIN `IDENT_PRODUITS_VOIES` ON `IDENT_PRODUITS_VOIES`.`CODEVOIE` = `IDENT_VOIES`.`CODEVOIE`
							WHERE `IDENT_PRODUITS_VOIES`.`CODECIP` = '$this->code_cip';";
    $this->voies = $ds->loadColumn($query);
  }
  
  function loadLibellePresentation(){
  	// Chargement du nombre de produit dans la presentation
  	$ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `IDENT_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip';";
    $_presentation = $ds->loadHash($query);
    $code_presentation_id = $_presentation['CODE_PRESENTATION'];

    $query = "SELECT * FROM `IDENT_PRESENTATIONS` WHERE `CODE_PRESENTATION` = '$code_presentation_id';";
    $libelle_presentation = $ds->loadHash($query);
    $this->libelle_presentation = $libelle_presentation['LIBELLE_PRESENTATION'];
  }
  
  function loadUnitePresentation(){
  	// Chargement du nombre de produit dans la presentation
  	$ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `IDENT_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip';";
  	$conditionnement = $ds->loadHash($query);
   	$this->nb_unite_presentation = $conditionnement["NB_UNITE_DE_PRESENTATION"];
    $this->nb_presentation = ($conditionnement["NB_PRESENTATION"]) ? $conditionnement["NB_PRESENTATION"] : "1";  	
  
  	// Libelle de la presentation
  	$code_unite_presentation = $conditionnement['CODE_UNITE_DE_PRESENTATION'];
  	$query = "SELECT * FROM `IDENT_UNITES_DE_PRESENTATION` WHERE `CODE_UNITE_DE_PRESENTATION` = '$code_unite_presentation';";
  	$presentation = $ds->loadHash($query);
  	$this->libelle_unite_presentation = $presentation["LIBELLE_UNITE_DE_PRESENTATION"];
  	
  	return $conditionnement;
  }
  
  function loadConditionnement(){
    $conditionnement = $this->loadUnitePresentation();
    
  	// Dosages
  	$this->dosages = array();
  	$this->loadDosage($conditionnement["DOSAGEUNITE1"],$conditionnement["DOSAGEQTE1"]);
  	$this->loadDosage($conditionnement["DOSAGEUNITE2"],$conditionnement["DOSAGEQTE2"]);
  	$this->loadDosage($conditionnement["DOSAGEUNITE3"],$conditionnement["DOSAGEQTE3"]);	
  	
  	$this->loadRapportUnitePrise($conditionnement["CODE_UNITE_DE_PRISE1"], $conditionnement["CODE_UNITE_DE_CONTENANCE1"], $conditionnement["NB_UP1"]);
  	$this->loadRapportUnitePrise($conditionnement["CODE_UNITE_DE_PRISE2"], $conditionnement["CODE_UNITE_DE_CONTENANCE2"], $conditionnement["NB_UP2"]);
  	
    $this->loadLibelleConditionnement($conditionnement["CODE_CONDITIONNEMENT"]);    
  }
 
  function loadLibelleConditionnement($code_conditionnement){
  	$ds = CSQLDataSource::get("bcb");
  	$query = "SELECT LIBELLE_CONDITIONNEMENT_PLURIEL FROM `IDENT_CONDITIONNEMENTS` WHERE `CODE_CONDITIONNEMENT` = '$code_conditionnement';";
    $this->libelle_conditionnement = $ds->loadResult($query);
  }
  
  function loadRapportUnitePrise($code_unite_prise, $code_unite_contenance, $nb_up){
  	$ds = CSQLDataSource::get("bcb");
    $query = "SELECT LIBELLE_UNITE_DE_PRISE_PLURIEL FROM `POSO_UNITES_PRISE` WHERE `CODE_UNITE_DE_PRISE` = '$code_unite_prise';";
    $unite_prise = $ds->loadResult($query);
    $query = "SELECT LIBELLE_UNITE_DE_CONTENANCE FROM `IDENT_UNITES_DE_CONTENANCE` WHERE `CODE_UNITE_DE_CONTENANCE` = '$code_unite_contenance';";
    $unite_contenance = $ds->loadResult($query);
    $this->rapport_unite_prise[$unite_prise][$unite_contenance] = $nb_up;
  }
  
  function loadDosage($dosage_unite, $dosage_nb){
   $ds = CSQLDataSource::get("bcb");
   if($dosage_unite){
	  	$query = "SELECT UNITE FROM `IDENT_UNITES_DE_DOSAGE` WHERE `CODE_UNITE` = '$dosage_unite';";
	  	$unite = $ds->loadResult($query);
	  	$this->dosages[] = array("nb_dosage" => $dosage_nb, "unite_dosage" => $unite);
  	}
  }
  
  
  // Permet de savoir si le produit est un générique 
  function getGenerique(){
    $this->_generique = $this->distObj->IsGenerique($this->code_cip);
  }
  
  function getSuppression(){
    if($this->_ref_monographie->date_suppression){
      $this->_supprime = 1;
    }
  }
  
  // Permet de savoir si le produit est un referent
  function getReferent(){
    $this->_referent = $this->distObj->IsReferent($this->code_cip);
  }
  
  function getStatut(){
    $this->distObj->SearchStatut($this->code_cip);
    $this->code_statut = $this->distObj->GetStatut(2);
    $this->libelle_statut = $this->distObj->GetStatut(3);
    $this->numero_AMM = $this->distObj->GetStatut(4);
    $this->date_AMM = $this->distObj->GetStatut(5);
  }
  
  function getAgrement(){
    $this->agrement = $this->distObj->GetStatut(15);
  }
  
  // Fonction qui retourne les equivalents d'un produit
  function loadRefsEquivalents(){
    $produitEquivalent = new CBcbEquivalent();
    $this->_ref_equivalents = $produitEquivalent->searchEquivalents($this->code_cip);
  }
  
  function loadRefsEquivalentsInLivret(){
  	$this->loadRefsEquivalents();
  	foreach($this->_ref_equivalents as $key => $produit_equivalent){
      if(!$produit_equivalent->inLivret){
        unset($this->_ref_equivalents[$key]);
      }
    }
  }
  
  function isInLivret(){
  	global $g;
  	$livretThera = new CBcbProduitLivretTherapeutique();
  	$this->inLivret = $livretThera->distObj->isLivret($g, $this->code_cip);
  }

  function isInT2A(){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT `MEDICAMENT_T2A` FROM `PHARMACIE_ADD_ON` WHERE `CODE_CIP` = '$this->code_cip'";
    $t2a = $ds->loadResult($query);
    $this->inT2A = ($t2a == "-1") ? false : true; 
  }
  
  static function getHorsT2ALivret(){
    $produits = array();
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT `CODE_CIP` 
							FROM `PHARMACIE_ADD_ON`
							LEFT JOIN `LIVRETTHERAPEUTIQUE` ON `PHARMACIE_ADD_ON`.`CODE_CIP` = `LIVRETTHERAPEUTIQUE`.`CODECIP` 
							WHERE `MEDICAMENT_T2A` = '-1' 
							AND `LIVRETTHERAPEUTIQUE`.`CODECIP` IS NOT NULL;";
    $codes_cip = $ds->loadColumn($query);

    foreach($codes_cip as $code_cip){
      $produit = new CBcbProduitLivretTherapeutique();
      $produit->load($code_cip);
      $produit->loadRefProduit();
      $produits[$code_cip] = $produit;
    }
    return $produits;
  }
  
  
  // $livretTherapeutique = 1 pour une recherche dans le livret Therapeutique
  function searchProduit($text, $supprime = 1, $type_recherche = "debut", $specialite = 1, $max = 50, $livretTherapeutique = 0, $full_mode = true){   
    // Type_recherche
    // 0 ou 256 => recherche par nom
    // 1: recherche par CIP
    // 2: recherche par UCD
    
    // Affichage des produits supprimes
    if($supprime == "" || $supprime == 0){
      $supprime = 1;
    } else {
      $supprime = 0;
    }
    
    // Position de la recherche
    if($type_recherche == "partout"){
      $type_recherche = 256;
    } 
    if($type_recherche == "debut"){
      $type_recherche = 0;
    }

    $this->distObj->LivretTherapeutique = $livretTherapeutique;
    $this->distObj->Supprime = $supprime;
        
    // 1ere recherche 
    $this->distObj->Specialite = 1;
    $this->distObj->Search($text, 0, $max, $type_recherche);
    
    $_produits_by_type = array();
    $_produits_by_type[] = $this->distObj->TabProduit;
    
    // 2eme recherche (pour les DM possedant un code cip)
    $this->distObj->Specialite = 2;
    $this->distObj->Search($text, 0, $max, $type_recherche);
    
    $_produits_by_type[] = $this->distObj->TabProduit;
    
    $produits = array();
    // Parcours des produits
    foreach($_produits_by_type as $_produits){
	    foreach($_produits as $key => $prod){
	      // Chargement du produit
	      $produit = new CBcbProduit();
	      $produit->load($prod->CodeCIP, $full_mode); 
	      $produits[$prod->CodeCIP] = $produit; 
	    }
    }
    return $produits;
  }
  
  
  function searchProduitAutocomplete($text, $nb_max, $livretTherapeutique = 0){   
    global $g;
    
  	$this->distObj->Specialite = 1;
    $this->distObj->Supprime = 1;
    if($livretTherapeutique){
      $this->distObj->LivretTherapeutique = $g;  
    }
    $this->distObj->Search($text, 0, $nb_max, 0);
    
    return $this->distObj->TabProduit;
  }
  
  
  // Chargement de toutes les posologies d'un produit
  function loadRefPosologies(){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip' ORDER BY `NO_POSO` ASC;";
    $posologies = $ds->loadList($query);
    
    // Chargement de chaque posologie
    $this->_ref_posologies = array();
    $view_poso = array();
    foreach($posologies as $key => $posologie){
      $mbposologie = new CBcbPosologie();
      $mbposologie->load($posologie["CODE_CIP"], $posologie["NO_POSO"]);
      if(!in_array($mbposologie->_view, $view_poso)){
        $this->_ref_posologies[] = $mbposologie;
      }
      $view_poso[] = $mbposologie->_view;
    }
    return $this->_ref_posologies;
  }
  
  // Chargement de la monographie d'un produit
  function loadRefMonographie(){
    $this->_ref_monographie = new CBcbMonographie();
    $this->_ref_monographie->load($this->code_cip);
  }
  
  
  // Chargement de la composition
  function loadRefComposition(){    
    $this->_ref_composition = new CBcbComposition();
    $this->_ref_composition->load($this->code_cip); 
  }
  
  
  // Chargement des donnees technico-reglementaires
  function loadRefEconomique(){
    $this->_ref_economique = new CBcbEconomique();
    $this->_ref_economique->load($this->code_cip);
  }
  
  // Recherche des classes ATC d'un produit
  function loadClasseATC(){
    $classeATC = new CBcbClasseATC();
    $this->_ref_classes_ATC   = $classeATC->searchATCProduit($this->code_cip);
    if(isset($this->_ref_classes_ATC[0]->classes[3])){
      $this->_ref_ATC_2_code    = $this->_ref_classes_ATC[0]->classes[3]["code"];
      $this->_ref_ATC_2_libelle = strtolower($this->_ref_classes_ATC[0]->classes[3]["libelle"]);
    }
  }
  
  // Chargement des fiches ATC
  function loadRefsFichesATC(){
    $fiche_ATC = new CFicheATC();
    $fiche_ATC->code_ATC = $this->_ref_ATC_2_code;
    $this->_ref_fiches_ATC = $fiche_ATC->loadMatchingList();
  }
  
  // Recherche des classes Therapeutique d'un produit
  function loadClasseTherapeutique(){
    $classeThera = new CBcbClasseTherapeutique();
    $this->_ref_classes_thera = $classeThera->searchTheraProduit($this->code_cip); 
  }
  
  static function getFavoris($praticien_id) {
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT prescription_line_medicament.code_cip, COUNT(*) AS total
            FROM prescription_line_medicament, prescription
            WHERE prescription_line_medicament.prescription_id = prescription.prescription_id
            AND prescription.praticien_id = $praticien_id
            AND prescription.object_id IS NOT NULL
            GROUP BY prescription_line_medicament.code_cip
            ORDER BY total DESC
            LIMIT 0, 20";
    return $ds->loadlist($sql);
  }
  
  static function getFavorisInjectable($praticien_id) {
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT perfusion_line.code_cip, COUNT(*) AS total
            FROM perfusion, perfusion_line, prescription
            WHERE perfusion_line.perfusion_id = perfusion.perfusion_id
            AND perfusion.prescription_id = prescription.prescription_id
            AND perfusion.praticien_id = $praticien_id
            AND prescription.object_id IS NOT NULL
            GROUP BY perfusion_line.code_cip
            ORDER BY total DESC
            LIMIT 0, 20";
    return $ds->loadlist($sql);
  }
}

?>