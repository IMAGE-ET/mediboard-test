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
  
  // Constructeur
  function CBcbProduit(){
    $this->distClass = "BCBProduit";
    parent::__construct();
  }

  // Chargement d'un produit
  function load($code_cip){
    $this->distObj->SearchInfo($code_cip);
    
    $infoProduit = $this->distObj->DataInfo;  

    if($infoProduit->Charge == 1){
      $this->code_cip        = $infoProduit->Code_CIP;
      $this->code_ucd        = $infoProduit->Code_Ucd;
      $this->libelle         = $infoProduit->Libelle;
      $this->nom_commercial  = $infoProduit->NomCommercial;
      $this->forme           = $infoProduit->Forme;
      $this->formes          = $infoProduit->Formes;
      $this->nb_ucd          = $infoProduit->Nb_UCD;
      $this->hospitalier     = $infoProduit->Hospitalier;
      $this->nom_laboratoire = $infoProduit->Laboratoire;
    }
    
    // Chargement de la monographie (permet d'obtenir la date de suppression) 
    $this->loadRefMonographie();

    // Chargement du statut du produit
    $this->getStatut();  
    
    // Chargement de l'agrement
    $this->getAgrement();
    
    // Produit générique ?
    $this->getGenerique();
    
    // Produit référent ?
    $this->getReferent();

    // Produit supprime ?
    $this->getSuppression();

    // Chargement de la composition du produit
    $this->loadRefComposition();
    
    // Permet de savoir si le produit appartient au livret therapeutique
    $this->isInLivret();

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
  
  // $livretTherapeutique = 1 pour une recherche dans le livret Therapeutique
  function searchProduit($text, $supprime = 1, $type_recherche = "debut", $specialite = 1, $max = 50, $livretTherapeutique = 0){   
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
    $this->distObj->Specialite = $specialite;
    $this->distObj->Supprime = $supprime;  
    $this->distObj->Search($text, 0, $max, $type_recherche);
    
    $produits = array();
    // Parcours des produits
    foreach($this->distObj->TabProduit as $key => $prod){
      // Chargement du produit
      $produit = new CBcbProduit();
      $produit->load($prod->CodeCIP);
      
      $produits[$prod->CodeCIP] = $produit; 
    }  
    return $produits;
  }
  
  
  // Chargement de toutes les posologies d'un produit
  function loadRefPosologies(){
    $ds = CSQLDataSource::get("bcb");
    $query = "SELECT * FROM `POSO_PRODUITS` WHERE `CODE_CIP` = '$this->code_cip' ORDER BY `NO_POSO` ASC;";
    $posologies = $ds->loadList($query);
    
    // Chargement de chaque posologie
    $this->_ref_posologies = array();
    foreach($posologies as $key => $posologie){
      $mbposologie = new CBcbPosologie();
      $mbposologie->load($posologie["CODE_CIP"], $posologie["NO_POSO"]);
      $this->_ref_posologies[] = $mbposologie;
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
    $this->_ref_classes_ATC = $classeATC->searchATCProduit($this->code_cip);  
  }
  
  // Recherche des classes Therapeutique d'un produit
  function loadClasseTherapeutique(){
    $classeThera = new CBcbClasseTherapeutique();
    $this->_ref_classes_thera = $classeThera->searchTheraProduit($this->code_cip); 
  }
  
  static function getFavoris($praticien_id) {
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT prescription_line.code_cip, COUNT(*) AS total
            FROM prescription_line, prescription
            WHERE prescription_line.prescription_id = prescription.prescription_id
            AND prescription.praticien_id = $praticien_id
            AND prescription.object_id IS NOT NULL
            GROUP BY prescription_line.code_cip
            ORDER BY total DESC
            LIMIT 0, 20";
    return $ds->loadlist($sql);
  }
  
}

?>