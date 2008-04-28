<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescriptionLineMedicament extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_medicament_id = null;
  
  // DB Fields
  var $code_cip         = null;
  var $no_poso          = null;
  var $commentaire      = null;
 
  /*
  var $debut            = null;
  var $duree            = null;
  var $unite_duree      = null;
  */
/*
  var $date_arret       = null;
*/

  var $valide_pharma    = null; 
  var $accord_praticien = null;
   
  // Form Field
  //var $_fin            = null;
  var $_unite_prise    = null;
  var $_specif_prise   = null;
  var $_traitement     = null;
  
  // Object References
  var $_ref_prescription = null;
  var $_ref_produit      = null;
  var $_ref_posologie    = null;
  var $_ref_prescription_traitement = null;
    
  // Alertes
  var $_ref_alertes      = null;
  var $_ref_alertes_text = null;
  var $_nb_alertes       = null;

  // Behaviour field
  var $_delete_prises = null;
  
  // Logs
  var $_ref_log_validation_pharma = null;
  
  
  function CPrescriptionLineMedicament() {
    $this->CMbObject("prescription_line_medicament", "prescription_line_medicament_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "code_cip"        => "notNull numchar|7",
      "no_poso"         => "num max|128",
      "commentaire"     => "str",
      "valide_pharma"   => "bool",
      "accord_praticien"=> "bool",
      "_unite_prise"    => "str",
      "_traitement"     => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  

  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_nb_alertes = 0;
    $this->_view = $this->_ref_produit->libelle;
    $this->_duree_prise = "";
    if($this->debut){
      $this->_duree_prise .= "à partir du ".mbTranformTime(null, $this->debut, "%d/%m/%Y");
    }
    if($this->duree && $this->unite_duree){
    	$this->_duree_prise .= " pendant ".$this->duree." ".$this->unite_duree;
    }
    if($this->_ref_prescription->type == "traitement"){
    	$this->_traitement = "1";
    }
  }
  
  // Store-like function
  function deletePrises(){
  	$this->_delete_prises = 0;
  	// Chargement des prises 
    $this->loadRefsPrises();
    // Parcours des suppression des prises
    foreach($this->_ref_prises as &$_prise){
      if($msg = $_prise->delete()){
      	return $msg;
      }
    }
  }
  
  
  function check(){
  	parent::check();
  	
  	// TODO: Verifier que le praticien_id passé est un praticien ou un admin
  
  }
  
  function store(){
  	if($msg = parent::store()){
  		return $msg;
  	}
  	
  	if($this->_delete_prises){
  		if($msg = $this->deletePrises()){
  			return $msg;
  		}
  	}
  }
  
  
  function loadRefsFwd() {
  	$this->_ref_prescription = new CPrescription();
    $this->_ref_prescription->load($this->prescription_id);
    $this->_ref_produit = new CBcbProduit();
    $this->_ref_produit->load($this->code_cip);
    $this->loadPosologie();
    // Si aucune posologie, on chargement l'unite de prise de la premiere trouvée
    if(!$this->_unite_prise){
    	$this->_ref_produit->loadRefPosologies();
    	$posologie_temp = reset($this->_ref_produit->_ref_posologies);
    	$this->_unite_prise = $posologie_temp->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    }
  }
  
  
  
  function loadPosologie() {
    $posologie = new CBcbPosologie();
    if($this->_ref_produit->code_cip && $this->no_poso) {
      $posologie->load($this->_ref_produit->code_cip, $this->no_poso);
    }
    $this->_unite_prise = $posologie->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    $this->_specif_prise = $posologie->_code_prise1;
    $this->_ref_posologie = $posologie;
  }
  
  function checkAllergies($listAllergies) {
    $this->_ref_alertes["allergie"] = array();
    foreach($listAllergies as $key => $all) {
      if($all->CIP == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["allergie"][$key]      = $all;
        $this->_ref_alertes_text["allergie"][$key] = $all->LibelleAllergie;
      }
    }
  }
  
  function checkInteractions($listInteractions) {
    $this->_ref_alertes["interaction"] = array();
    foreach($listInteractions as $key => $int) {
      if($int->CIP1 == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["interaction"][$key]      = $int;
        $this->_ref_alertes_text["interaction"][$key] = $int->Type;
      }
    }
  }
  
  function checkIPC($listIPC) {
    $this->_ref_alertes["IPC"]      = array();
    $this->_ref_alertes_text["IPC"] = array();
  }
  
  function checkProfil($listProfil) {
    $this->_ref_alertes["profil"] = array();
    foreach($listProfil as $key => $pro) {
      if($pro->CIP == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["profil"][$key]      = $pro;
        $this->_ref_alertes_text["profil"][$key] = $pro->LibelleMot;
      }
    }
  }
  
  
  function loadRefLogValidationPharma(){
    $this->_ref_log_validation_pharma = $this->loadLastLogForField("valide_pharma");
  }
  
  
}

?>