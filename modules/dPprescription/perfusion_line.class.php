<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPerfusionLine class
 */
class CPerfusionLine extends CMbObject {
	// DB Table key
  var $perfusion_line_id = null;
  
  // DB Fields
  var $perfusion_id = null;
  var $code_cip     = null; 
  var $quantite     = null; // Quantite de produit
  var $unite        = null;
  var $date_debut   = null; // Date de debut (si le debut est différé)
  var $time_debut   = null; // Heure de debut (si le debut est différé)
  
  // Object references
  var $_ref_perfusion = null;

  // Form fields
  var $_debut = null;
  var $_fin   = null;
  
  var $_unite_sans_kg = null;
  var $_quantite_administration = null;
  var $_ratio_administration_dispensation = null;
  var $_quantite_dispensation = null;
  var $_ucd_view = null;
  
  // Can fields
  var $_can_vw_livret_therapeutique = null;
  var $_can_vw_generique = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perfusion_line';
    $spec->key   = 'perfusion_line_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["perfusion_id"] = "notNull ref class|CPerfusion cascade";
    $specs["code_cip"]     = "notNull numchar length|7";
    $specs["quantite"]     = "num";
    $specs["unite"]        = "str";
    $specs["date_debut"]   = "date";
    $specs["time_debut"]   = "time";
    return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();
    
    $this->loadRefPerfusion();
    $this->_debut = ($this->date_debut && $this->time_debut) ? 
                    "$this->date_debut $this->time_debut" : $this->_ref_perfusion->_debut;
    $this->_fin = $this->_ref_perfusion->_fin;
    
    $this->loadRefProduit();
    $this->_view = $this->_ref_produit->libelle;
    if($this->quantite){
      $this->_view .= "($this->quantite $this->unite)";
    }
    
    // Affichage de l'icone Livret Therapeutique
    if(!$this->_ref_produit->inLivret){
      $this->_can_vw_livret_therapeutique = 1;
    }
    // Affichage de l'icone generique
    if($this->_ref_produit->_generique){
      $this->_can_vw_generique = 1;
    }
    
    $this->_ucd_view = substr($this->_ref_produit->libelle, 0, strrpos($this->_ref_produit->libelle, ' ')+1);   
    $this->_protocole = $this->_ref_perfusion->_protocole;
  }
  
  /*
   * Chargement de la perfusion
   */
  function loadRefPerfusion(){
    $this->_ref_perfusion = new CPerfusion();
    $this->_ref_perfusion = $this->_ref_perfusion->getCached($this->perfusion_id);
  }
  
  /*
   * Chargement du produit
   */
  function loadRefProduit(){
  	$this->_ref_produit = CBcbProduit::get($this->code_cip);
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefPerfusion();

//    $this->_ref_produit->loadRefPosologies();
//    $this->_ref_produit->loadLibellePresentation();
    if($this->_ref_produit->libelle_presentation){
      $this->_unites_prise[] = $this->_ref_produit->libelle_presentation;
    }
    foreach($this->_ref_produit->_ref_posologies as $_poso){
      $unite = $_poso->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
      if($_poso->p_kg) {
        // On ajoute la poso avec les /kg
        $this->_unites_prise[] = "$unite/kg";
      }
    	$this->_unites_prise[] = $unite;
    }
    if(is_array($this->_unites_prise)){
      $this->_unites_prise = array_unique($this->_unites_prise);
    }
  }
}
  
?>