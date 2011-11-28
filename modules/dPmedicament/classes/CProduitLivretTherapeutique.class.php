<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/*
 * Classe permettant de definir ou de redefinir les elements indispensables a la prescripion pour un produit
 */
class CProduitLivretTherapeutique extends CMbObject {
  // DB Table key
  var $produit_livret_id = null;
  
	var $owner_crc         = null;
	
  var $code_cip          = null;
	var $code_ucd          = null;
  var $code_cis          = null;
  
  var $prix_hopital      = null;
  var $prix_ville        = null;
  var $date_prix_hopital = null;
  var $date_prix_ville   = null;
  var $code_interne      = null;
  var $commentaire       = null;
  var $unite_prise       = null;
	
	var $_ref_produit      = null;
	var $_function_guid    = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'produit_livret_therapeutique';
    $spec->key   = 'produit_livret_id';
    $spec->uniques["code_cip"] = array("code_cip", "owner_crc");
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["owner_crc"] = "num";
    $specs["code_cip"] = "numchar notNull length|7";
		$specs["code_ucd"] = "numchar length|7";
    $specs["code_cis"] = "numchar length|8";
    $specs["prix_hopital"] = "float";
    $specs["prix_ville"] = "float";
    $specs["date_prix_hopital"] = "date";
    $specs["date_prix_ville"] = "date";
    $specs["code_interne"] = "str";
    $specs["commentaire"] = "str";
    $specs["unite_prise"] = "str";
    return $specs;
	}
	
  function loadRefProduit(){
    $this->_ref_produit = CBcbProduit::get($this->code_cip);
  }
	
	static function getProduit($owner_crc, $code_cip){
		$produit_livret = new CProduitLivretTherapeutique();
    $produit_livret->owner_crc = $owner_crc;
		$produit_livret->code_cip = $code_cip;
		$produit_livret->loadMatchingObject();
		return $produit_livret;
	}
	
	static function countProduits($owner_crc = ''){
		$produit = new CProduitLivretTherapeutique();
		if($owner_crc){
		  $produit->owner_crc = CBcbProduit::getHash($owner_crc);
    }
		return $produit->countMatchingList();
	}
	
	static function purgeProduits($owner_crc = ''){
		if ($owner_crc){
			$owner_crc = CBcbProduit::getHash($owner_crc);
		}
		
		// Suppression des produits dans Mb
		$ds = CSQLDataSource::get("std");
		$query = "DELETE FROM `produit_livret_therapeutique`";
		if($owner_crc){
			$query .= " WHERE owner_crc = '$owner_crc'";
		}
		$ds->exec($query);
    
		CProduitLivretTherapeutique::synchronize();
  }
	
	static function synchronize (){
		// Suppression des produits presents dans la BCB
		CBcbProduitLivretTherapeutique::purgeProduits();
		
		// Chargement des produits du livret
		$produit = new CProduitLivretTherapeutique();
		$produits = $produit->loadList();
		foreach($produits as $_produit){
			CBcbProduitLivretTherapeutique::insert($_produit->owner_crc, $_produit->code_cip);
		}
	}
		
	function addToStocks($category = null, $group = null, &$messages = array()) {
    if (!isset(CModule::$active["dPstock"])) {
      return false;
    }
    if (!$category) {
      $category = new CProductCategory;
      if (!$category->load(CAppUI::conf("bcb CBcbProduitLivretTherapeutique product_category_id"))) {
        return false;
      }
    }
    if (!$group) {
      $group = CGroups::loadCurrent();
    }
    
    $this->loadRefProduit();
    $this->_ref_produit->loadConditionnement();
    $this->_ref_produit->loadLibellePresentation();
    
    // Recherche du produit dans la table de produits hors AMM
    $produit_prescription = new CProduitPrescription();
    $produit_prescription->code_cip = $this->code_cip;
    $produit_prescription->loadMatchingObject();
    
    if($produit_prescription->_id){
      $libelle = $produit_prescription->libelle;
      $quantite = $produit_prescription->nb_presentation;
      
      $libelle_presentation = $produit_prescription->unite_dispensation;
      $nb_unite_presentation = $produit_prescription->quantite; 
      $libelle_unite_presentation = $produit_prescription->unite_prise;
      $packaging = "";
    } else {    
      $_produit =& $this->_ref_produit; 
      $libelle = $_produit->libelle;
      $packaging = $_produit->libelle_conditionnement;  
      
      if($_produit->libelle_presentation){
        $quantite = $_produit->nb_presentation;
        $libelle_presentation = $_produit->libelle_presentation;
        $nb_unite_presentation = $_produit->nb_unite_presentation ? $_produit->nb_unite_presentation : 1;
        $libelle_unite_presentation = $_produit->libelle_unite_presentation;
      } else {
        $quantite = $_produit->nb_unite_presentation;
        $libelle_presentation = $_produit->libelle_unite_presentation;
        $nb_unite_presentation = "";
        $libelle_unite_presentation = "";
      }
    }
    
    $product = new CProduct();
    $product->code          = $this->code_cip;
    
    if (!$product->loadMatchingObject()) {
      $product->category_id   = $category->_id;
      $product->name          = $libelle;
    
      $product->description   = $this->commentaire;
      $product->packaging     = $packaging;
      $product->quantity      = $quantite;
      $product->item_title    = $libelle_presentation;
      $product->unit_quantity = $nb_unite_presentation;
      $product->unit_title    = $libelle_unite_presentation;
      
      if($product->item_title == $product->unit_title){
        $product->item_title = "";
      }
    
      // On vérifie si le fabriquant du produit est déjà dans la base de données
      if ($this->_ref_produit->nom_laboratoire) {
        $societe = new CSociete();
        $societe->name = $this->_ref_produit->nom_laboratoire;
        if (!$societe->loadMatchingObject()) {
          $societe->store();
          $msg = 'Société ajoutée';
          if (!isset($messages[$msg])) $messages[$msg] = 0;
          $messages[$msg]++;
        }
        $product->societe_id = $societe->_id;
      }
    }
  
    $msg = $product->store();
  
    // Sauvegarde du nouveau produit correspondant au médicament
    if (!$msg) {
      $product->updateFormFields();
      $product->loadRefStock();
      $stock = $product->_ref_stock_group;
      
      if (!$stock->_id) {
        $location = CProductStockLocation::getDefaultLocation($group, $product);
        
        $stock->quantity = $product->_unit_quantity;
        $stock->order_threshold_min = $stock->quantity;
        $stock->location_id = $location->_id;
        
        //$stock->order_threshold_max = $stock->quantity * 2;
        if ($msg = $stock->store()) {
          if (!isset($messages[$msg])) $messages[$msg] = 0;
          $messages[$msg]++;
        } else {
          $msg = 'Stock produit ajouté';
          if (!isset($messages[$msg])) $messages[$msg] = 0;
          $messages[$msg]++;
        }
      }
    } else {
      $msg .= " ($product->code: $product->name)";
      if (!isset($messages[$msg])) $messages[$msg] = 0;
      $messages[$msg]++;
    }
    
    return true;
  }
	
	function store(){
		if(!$this->_id && !$this->owner_crc){
	    $this->owner_crc = $this->_function_guid ? $this->_function_guid : CGroups::loadCurrent()->_guid;
      $this->owner_crc = CBcbProduit::getHash($this->owner_crc);
		}
		
		$creation = false;
		
		// Ajout du produit dans la banque de médicament
		if(!$this->_id && $this->code_cip){
			$creation = true;
			 
			$produit_livret = new CProduitLivretTherapeutique();
			$produit_livret->code_cip = $this->code_cip;
			$produit_livret->owner_crc = $this->owner_crc;
      $produit_livret->loadMatchingObject();
		
		  if($produit_livret->_id){
		  	return "Produit deja présent dans le livret Thérapeutique";
		  }
			CBcbProduitLivretTherapeutique::insert($this->owner_crc, $this->code_cip);
			
			$this->loadRefProduit();
			$this->code_ucd = $this->_ref_produit->code_ucd;
			$this->code_cis = $this->_ref_produit->code_cis;
		}
		
		if($msg = parent::store()){
			return $msg;
		}
		
	  if($creation){
	  	$this->addToStocks();
	  }
	}
	
	function delete(){
		$this->completeField("owner_crc");
		$this->completeField("code_cip");
		CBcbProduitLivretTherapeutique::delete($this->owner_crc, $this->code_cip);
		return parent::delete();
	}
}
  
?>