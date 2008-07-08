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

  var $valide_pharma    = null; 
  var $accord_praticien = null;
  var $substitution_line_id = null;
  
  // Form Field
  var $_unites_prise    = null;
  var $_specif_prise    = null;
  var $_traitement      = null;

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
  
  // Can fields
 	var $_can_select_equivalent              = null;
  var $_can_view_historique                = null;
 	var $_can_view_form_ald                  = null;
 	var $_can_vw_form_traitement             = null;
 	var $_can_view_signature_praticien       = null;
 	var $_can_view_form_signature_praticien  = null;
 	var $_can_view_form_signature_infirmiere = null;
  var $_can_vw_livret_therapeutique        = null;
  var $_can_vw_hospi                       = null;
  var $_can_vw_generique                   = null;
 	var $_can_modify_poso                    = null;
  var $_can_delete_line                    = null;
	var $_can_vw_form_add_line_contigue      = null;
  var $_can_modify_dates                   = null;
  var $_can_modify_comment                 = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_medicament';
    $spec->key   = 'prescription_line_medicament_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "code_cip"             => "notNull numchar|7",
      "no_poso"              => "num max|128",
      "commentaire"          => "str",
      "valide_pharma"        => "bool",
      "accord_praticien"     => "bool",
      "substitution_line_id" => "ref class|CPrescriptionLineMedicament",
      "_unite_prise"         => "str",
      "_traitement"          => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  
  function loadView() {
    $this->loadRefsPrises();
  }
  
  /*
   * Déclaration des backRefs
   */
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prev_hist_line"]  = "CPrescriptionLineMedicament substitution_line_id";
    return $backRefs;
  }
  
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_nb_alertes = 0;
    $this->_view = $this->_ref_produit->libelle;
    $this->_duree_prise = "";
    
    if ($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    }
    else {
	    if ($this->debut){
	      $this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    }
	    if ($this->duree && $this->unite_duree){
	    	$this->_duree_prise .= " pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree);
	    }
    }
    if($this->_ref_prescription->type == "traitement"){
    	$this->_traitement = "1";
    }
    
    $time = ($this->time_arret) ? $this->time_arret : "23:59:00";
    if(!$this->_traitement){
      $this->_date_arret_fin = $this->_fin ? "$this->_fin $time" : "";    	
    }
    
    // Calcul de la date de fin de la ligne
    if($this->date_arret){
    	$this->_date_arret_fin = $this->date_arret;
      $this->_date_arret_fin .= $this->time_arret ? " $this->time_arret" : " $time";
    }    
  }
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $prescription_type = "", $mode_protocole = 0, $mode_pharma = 0) {
  	
  	/*
  	 * Une infirmiere peut remplir entierement une ligne si elle l'a créée.
  	 * Une fois que la ligne est validé par la praticien ou par le pharmacien, l'infirmiere ne peut plus y toucher
  	 */
  	
		global $AppUI;
		
		$perm_infirmiere = $this->creator_id == $AppUI->user_id && 
                       !$this->signee && 
                       !$this->valide_infirmiere && 
                       !$this->valide_pharma;
    
		$perm_edit = (!$this->signee || $mode_pharma) && 
                 !$this->valide_pharma && 
                 ($this->praticien_id == $AppUI->user_id  || $perm_infirmiere || $is_praticien || $mode_pharma);
 
    // Modification des dates et des commentaires
    if($perm_edit){
    	$this->_can_modify_dates = 1;
    	$this->_can_modify_comment = 1;
    }
    // Select equivalent
    if($perm_edit){
    	$this->_can_select_equivalent = 1;
    }
		// Affichage de l'icone d'historique
		if (!$this->_protocole && $this->_count_parent_line){
			$this->_can_view_historique = 1;
		}
    // View ALD
    if($perm_edit && !$this->_protocole){
    	$this->_can_view_form_ald = 1;
    }
    // View formulaire traitement
    if($perm_edit && !$mode_pharma && !$this->_protocole){
    	$this->_can_vw_form_traitement = 1;
    }
    // View signature praticien
    if(($AppUI->user_id != $this->praticien_id) && !$this->_protocole){
    	$this->_can_view_signature_praticien = 1;
    }
    // Affichage du formulaire de signature praticien
    if(!$this->_protocole && $is_praticien && ($this->praticien_id == $AppUI->user_id)){
    	$this->_can_view_form_signature_praticien = 1;
    }
    // Affichage du formulaire de signature infirmiere
    if(!$this->_protocole && !$is_praticien && !$this->signee && $this->creator_id == $AppUI->user_id && !$this->valide_pharma){
    	$this->_can_view_form_signature_infirmiere = 1;
    }
    // Affichage de l'icone Livret Therapeutique
    if(!$this->_ref_produit->inLivret && ($prescription_type == "sejour" || $this->_protocole)){
      $this->_can_vw_livret_therapeutique = 1;
    }
    // Affichage de l'icone Produit Hospitalier
    if(!$this->_ref_produit->hospitalier && ($prescription_type == "sortie" || $this->_protocole)){
      $this->_can_vw_hospi = 1;
    }
    // Affichage de l'icone generique
    if($this->_ref_produit->_generique){
      $this->_can_vw_generique = 1;
    }
    // Modification de la posologie
    if($perm_edit){
    	$this->_can_modify_poso = 1;
    	// On ne peut modifier une ligne de traitement personnel seulement en pre_admission
      if($this->_traitement && ($prescription_type != "pre_admission")){
      	$this->_can_modify_poso = 0;
      }
    }
    // Suppression de la ligne
    if(($perm_edit && $is_praticien) || $this->_protocole){
      $this->_can_delete_line = 1;
  	}
  	// Affichage du bouton "Modifier une ligne"
  	if(!$this->_protocole && $this->_ref_prescription->type != "externe"){
  		$this->_can_vw_form_add_line_contigue = 1;
  	}
	}
  
  /*
   * Store-like function, suppression des prises de la ligne
   */
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
  	parent::loadRefsFwd();
    $this->loadRefProduit();
    $this->loadPosologie();
  
    $this->_ref_produit->loadRefPosologies();
    foreach($this->_ref_produit->_ref_posologies as $_poso){
      $unite = $_poso->_code_unite_prise["LIBELLE_UNITE_DE_PRISE_PLURIEL"];
      if($_poso->p_kg) {
        $unite .= "/kg";
      }
    	$this->_unites_prise[] = $unite;
    }
    $this->_unites_prise = array_unique($this->_unites_prise);
  }
  
  /*
   * Chargement du produit
   */
  function loadRefProduit(){
  	$this->_ref_produit = new CBcbProduit();
  	$this->_ref_produit->load($this->code_cip);
  }
  
  /*
   * Chargement de la posologie
   */
  function loadPosologie() {
    $posologie = new CBcbPosologie();
    if($this->_ref_produit->code_cip && $this->no_poso) {
      $posologie->load($this->_ref_produit->code_cip, $this->no_poso);
    }
    $this->_unite_prise = $posologie->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    $this->_specif_prise = $posologie->_code_prise1;
    $this->_ref_posologie = $posologie;
  }
  
  
  /*
   * Chargement de la ligne suivante (dans le cas d'une subsitution)
   */
  function loadRefNextHistLine(){
    $this->_ref_next_hist_line = new $this->_class_name;
    if($this->subsitution_line_id){
      $this->_ref_next_hist_line->_id = $this->subsitution_line_id;
      $this->_ref_next_hist_line->loadMatchingObject();
    }  
  }
  
  
  /*
   * Calcul permettant de savoir si la ligne possède un historique (substitution)
   */
  function countPrevHistLine(){
    $line = new $this->_class_name;
    $line->subsitution_line_id = $this->_id;
    $this->_count_prev_hist_line = $line->countMatchingList(); 
  }
  
  /*
   * Chargement de la ligne precedent la ligne courante
   */
  function loadRefPrevHistLine(){
  	$this->_ref_prev_hist_line = $this->loadUniqueBackRef("prev_hist_line");
  }

  /*
   * Chargement récursif des parents d'une ligne (substitution) permet d'afficher l'historique d'une ligne
   */
  function loadRefsPrevLines($lines = array()) {
    if(!array_key_exists($this->_id, $lines)){
      $lines[$this->_id] = $this;
    }
    // Chargement de la parent_line
    $this->loadRefPrevHistLine();
    if($this->_ref_prev_hist_line->_id){
      $lines[$this->_ref_prev_hist_line->_id] = $this->_ref_prev_hist_line;
      return $this->_ref_prev_hist_line->loadRefsPrevLines($lines);
    } else {
      return $lines;
    }
  }
  
  
  function delete(){
    // Chargement de la substitution_line de l'objet à supprimer
    $line = new $this->_class_name;
    $line->substitution_line_id = $this->_id;
    $line->loadMatchingObject();
    if($line->_id){
      if($msg = $line->delete()){
        return $msg;
      }
    }
    
    // Suppression de la ligne
    if($msg = parent::delete()){
      return $msg;
    }
  }
  
  
  
  /*
   * Controle des allergies
   */
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
  
  /*
   * Controle des interactions
   */
  function checkInteractions($listInteractions) {
    $this->_ref_alertes["interaction"] = array();
    foreach($listInteractions as $key => $int) {
      if($int->CIP1 == $this->code_cip || $int->CIP2 == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["interaction"][$key]      = $int;
        $this->_ref_alertes_text["interaction"][$key] = $int->Type;
      }
    }
  }
  
  /*
   * Controle des IPC
   */
  function checkIPC($listIPC) {
    $this->_ref_alertes["IPC"]      = array();
    $this->_ref_alertes_text["IPC"] = array();
  }
  
  /*
   * Controle du profil du patient
   */
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
  
  /*
   * Chargement du log de validation par le pharmacien
   */
  function loadRefLogValidationPharma(){
    $this->_ref_log_validation_pharma = $this->loadLastLogForField("valide_pharma");
  }
}

?>