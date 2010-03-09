<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("bcbObject.class.php");

class CBcbProduitLivretTherapeutique extends CBcbObject {

  var $group_id = null;
  var $code_cip = null;
  var $prix_hopital = null;
  var $prix_ville = null;
  var $date_prix_ville = null;
  var $date_prix_hopital = null;
  var $code_interne = null;
  var $commentaire = null;
  
  var $_ref_produit = null;
  
  
  // Constructeur
  function CBcbProduitLivretTherapeutique(){
    $this->distClass = "BCBLivretTherapeutique";
    parent::__construct();
    
    // Connexion a la base de Gestion
    $result = $this->distObj->InitConnexionGestion(CBcbObject::$objDatabaseGestion->LinkDB, CBcbObject::$TypeDatabaseGestion);
  }
  
  /**
   * Compte le nombre de produit du livret courant dans la base médicament
   * @return int Le nombre de produit
   */
  static function countProduitsMed() {
    global $g;
    $query = "SELECT COUNT(*) FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    $ds = CBcbObject::getDataSource();
    return $ds->loadResult($query);
  }
  
  /**
   * Compte le nombre de produit du livret courant dans la base de gestion
   * @return int Le nombre de produit
   */
  static function countProduitsGes(){
    global $g;
    $query = "SELECT COUNT(*) FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    $ds = CSQLDataSource::get("bcbges");
    return $ds->loadResult($query);
  }
  
  static function purgeProduits() {
    global $g;
    $query = "DELETE FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    $ds = CSQLDataSource::get("bcbges");
    $ds->exec($query);
    $ds = CBcbObject::getDataSource();
    $ds->exec($query);
    return $ds->affectedRows();
  }
  
  static function getProduits($order = 'CODECIP', $limit = null, $full_mode = true) {
  	global $g;
    $ds = CBcbObject::getDataSource();
    $query = "SELECT `CODECIP` FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g'";
    if ($order) $query .= " ORDER BY $order";
    if ($limit) $query .= " LIMIT $limit";
    $results = $ds->loadList($query);
    $list = array();
    foreach ($results as $plt) {
    	$produitLivretTherapeutique = new CBcbProduitLivretTherapeutique();
      $produitLivretTherapeutique->load($plt['CODECIP']);
      $produitLivretTherapeutique->loadRefProduit($full_mode);
      $list[] = $produitLivretTherapeutique;
    }
    return $list;
  }
  
  function load($code_cip) {
    global $g;
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `LIVRETTHERAPEUTIQUE` WHERE `CODEETABLISSEMENT` = '$g' AND `CODECIP` = '$code_cip';";
    if($result = $ds->loadHash($query)){
      $this->group_id          = $result["CODEETABLISSEMENT"];
      $this->code_cip          = $result["CODECIP"];
      $this->prix_hopital      = $result["PRIXHOPITAL"];
      $this->prix_ville        = $result["PRIXVILLE"];
      $this->date_prix_hopital = $result["DATEPRIXHOPITAL"];
      $this->date_prix_ville   = $result["DATEPRIXVILLE"];
      $this->code_interne      = $result["CODEINTERNE"];
      $this->commentaire       = $result["COMMENTAIRE"];
      // return true si le produit existe => chargement
      return true;
    }
    // return false si le produit n'existe pas
    return false;
  }
  
  function updateFormFields(){
    $this->date_prix_hopital = mbDateFromLocale($this->date_prix_hopital);
    $this->date_prix_ville = mbDateFromLocale($this->date_prix_ville);
  }
  
  function updateDBFields(){
    $this->distObj->DatePrixHopital = mbDateToLocale($this->distObj->DatePrixHopital);
	  $this->distObj->DatePrixVille = mbDateToLocale($this->distObj->DatePrixVille);
  }
    
  function loadRefProduit($full_mode = true){
    $this->_ref_produit = new CBcbProduit();
    if($this->code_cip){
      $this->_ref_produit->load($this->code_cip, $full_mode);
    }
  }
  
  function synchronize(){
    $this->distObj->synchronize();
  }
  
  function addToStocks($category = null, $group = null, &$messages = array()) {
    if (!isset(CModule::$active["dPstock"])) {
      return false;
    }
    if (!$category) {
      $category = new CProductCategory;
      if (!$category->load(CAppUI::conf("dPmedicament CBcbProduitLivretTherapeutique product_category_id"))) {
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
      $product->category_id = $category->_id;
      $product->name        = $libelle;
    }
    
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
  
    $msg = $product->store();
  
    // Sauvegarde du nouveau produit correspondant au médicament
    if (!$msg) {
      $product->updateFormFields();
      
      $stock = new CProductStockGroup();
      $stock->product_id = $product->_id;
      $stock->group_id = $group->_id;
      if (!$stock->loadMatchingObject()) {
        $stock->quantity = $product->_unit_quantity;
        $stock->order_threshold_min = $stock->quantity;
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
} 

?>