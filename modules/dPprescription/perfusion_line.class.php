<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPerfusionLine extends CMbObject {
	// DB Table key
  var $perfusion_line_id = null;
  
  // DB Fields
  var $perfusion_id = null;
  var $code_cip     = null; 
  var $code_ucd     = null;
  var $code_cis     = null;
  var $quantite     = null; // Quantite de produit
  var $unite        = null;
  
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
  var $_forme_galenique = null;
  var $_posologie = null;
  var $_unite_administration = null;
  
  var $_administrations = null;
	var $_ref_produit_prescription = null;
  
  // Can fields
  var $_can_vw_livret_therapeutique = null;
  var $_can_vw_generique = null;
  
  var $_ref_administrations = null;
  var $_view_unite_prise = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perfusion_line';
    $spec->key   = 'perfusion_line_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["perfusion_id"] = "ref notNull class|CPerfusion cascade";
    $specs["code_cip"]     = "numchar notNull length|7";
    $specs["code_ucd"]     = "numchar length|7";
    $specs["code_cis"]     = "numchar length|8";
    $specs["quantite"]     = "num";
    $specs["unite"]        = "str";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administrations"]  = "CAdministration object_id";
    return $backProps;
  }
  

  function updateFormFields(){
    parent::updateFormFields();
    
    $this->loadRefPerfusion();
    $this->_debut = $this->_ref_perfusion->_debut;
    $this->_fin = $this->_ref_perfusion->_fin;
    
		// Suppression de l'equivalence entre unite de prise à l'affichage
    if(!$this->_view_unite_prise){
      if(preg_match("/\(([0-9.,]+).*\)/i", $this->unite, $matches)){
        $_quant = end($matches);
        $nb = $this->quantite * $_quant;
        $this->_view_unite_prise = str_replace($_quant, $nb, $this->unite);
      }
    }
		
    $this->loadRefProduit();
    $this->_forme_galenique = $this->_ref_produit->forme;
    $this->_ucd_view = "{$this->_ref_produit->libelle_abrege} {$this->_ref_produit->dosage}";
    $this->_view = "$this->_ucd_view $this->_forme_galenique";
    if($this->quantite){
    	if($this->_view_unite_prise){
    	  $this->_posologie =  "$this->quantite $this->_view_unite_prise";
      } else {
        $this->_posologie =  "$this->quantite $this->unite";
			}
      $this->_view .= " ($this->_posologie)";
    }

    // Affichage de l'icone Livret Therapeutique
    if(!$this->_ref_produit->inLivret){
      $this->_can_vw_livret_therapeutique = 1;
    }
    // Affichage de l'icone generique
    if($this->_ref_produit->_generique){
      $this->_can_vw_generique = 1;
    }
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
    
    $this->_unites_prise = array();
    $produits = $this->_ref_produit->loadRapportUnitePriseByCIS();
    
    $this->_ref_produit->loadLibellePresentation();
    $this->_ref_produit->loadUnitePresentation();
      
    $libelle_unite_presentation = $this->_ref_produit->libelle_unite_presentation;
    $libelle_unite_presentation_pluriel = $this->_ref_produit->libelle_unite_presentation_pluriel;
   
    if(!$this->_ref_produit->_ref_posologies){
    	$this->_ref_produit->loadRefPosologies();
    } 
		
    foreach($this->_ref_produit->_ref_posologies as $_poso){
      $unite = $_poso->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
      
      if($unite){
	      $coef_adm = $this->_ref_produit->rapport_unite_prise[$unite][$libelle_unite_presentation];
        if($_poso->p_kg) {
	        // On ajoute la poso avec les /kg
	          $_presentation = "";
		        if (!preg_match("/$unite/i", $libelle_unite_presentation_pluriel)){
		          $_presentation = " ($coef_adm $libelle_unite_presentation/kg)";
		        }
		        $this->_unites_prise[] = "$unite/kg".$_presentation;
	      }
        $_presentation = "";
        if (!preg_match("/$unite/i", $libelle_unite_presentation_pluriel)){
          $_presentation = " ($coef_adm $libelle_unite_presentation)";
        }
        $this->_unites_prise[] = $unite.$_presentation;
      }
    }
    
    // Ajout de la presentation comme unite de prise
	  foreach($produits as $_produit){
	    if ($_produit->libelle_presentation){
	      $libelle_unite_presentation = $_produit->libelle_unite_presentation;
		    $coef_adm = $_produit->rapport_unite_prise[$_produit->libelle_presentation][$libelle_unite_presentation];
	      $this->_unites_prise[] = "{$_produit->libelle_presentation} ($coef_adm $libelle_unite_presentation)";
	    }
    }
    
    if(is_array($this->_unites_prise)){
      $this->_unites_prise = array_unique($this->_unites_prise);
    }
  }
  
  function loadRefsAdministrations(){
    $this->_ref_administrations = $this->loadBackRefs("administrations");
  }
	
  function loadRefProduitPrescription(){
    $this->_ref_produit_prescription = new CProduitPrescription();
    if($this->code_cis){
      $this->_ref_produit_prescription->code_cis = $this->code_cis;
      $this->_ref_produit_prescription->loadMatchingObject();
    }
    
    if(!$this->_ref_produit_prescription->_id && $this->code_ucd){
      $this->_ref_produit_prescription->code_cis = null;
      $this->_ref_produit_prescription->code_ucd = $this->code_ucd;
      $this->_ref_produit_prescription->loadMatchingObject();
    }
    
    if(!$this->_ref_produit_prescription->_id && $this->code_cip){
      $this->_ref_produit_prescription->code_cis = null;
      $this->_ref_produit_prescription->code_ucd = null;
      $this->_ref_produit_prescription->code_cip = $this->code_cip;
      $this->_ref_produit_prescription->loadMatchingObject();
    }
    
    if($this->_ref_produit_prescription->unite_prise){
      $this->_unite_administration = $this->_ref_produit_prescription->unite_prise;
    }
  }
}
  
?>