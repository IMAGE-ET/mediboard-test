<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescription class
 */
class CPrescriptionLineElement extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_element_id = null;
  
  // DB Fields
  var $element_prescription_id        = null;
  var $executant_prescription_line_id = null; 
  var $user_executant_id              = null;
  
  // Object references
  var $_ref_element_prescription      = null;
  var $_ref_executant                 = null;
  var $_executant                     = null;
  
  // Can fields
  var $_can_select_executant               = null;
  var $_can_delete_line                    = null;
  var $_can_view_signature_praticien       = null;
  var $_can_view_form_signature_praticien  = null;
  var $_can_view_form_signature_infirmiere = null;
  var $_can_view_form_ald                  = null;
  var $_can_view_form_conditionnel         = null;
  var $_can_modify_poso                    = null;
  var $_can_modify_comment                 = null;
  var $_can_modify_dates                   = null; 
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_element';
    $spec->key   = 'prescription_line_element_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["element_prescription_id"]        = "ref notNull class|CElementPrescription cascade";
    $specs["executant_prescription_line_id"] = "ref class|CExecutantPrescriptionLine";
    $specs["user_executant_id"]              = "ref class|CMediusers";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["parent_line"]     = "CPrescriptionLineElement child_id";  
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefElement();
    $this->_ref_element_prescription->loadRefCategory();
    $this->_view = $this->_ref_element_prescription->_view;
    
    $chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;
    
    // Un element ne peut jamais être un traitement
    $this->_traitement = 0;	
    $this->_unite_prise = CAppUI::conf("dPprescription CCategoryPrescription $chapitre unite_prise");
    $this->_duree_prise = "";
    
    if($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    } else {
	    if($this->debut && !$this->_fin){
	      $this->_duree_prise .= "le ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    }
	    if($this->duree && $this->_fin){
	    	$this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y")." pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree).".";
	    }
    }
    
    $time_fin = ($this->time_fin) ? $this->time_fin : "23:59:00";
    // Calcul de la date de fin de la ligne
    $this->_fin_reelle = $this->_fin ? "$this->_fin $time_fin" : "$this->debut 23:59:00";    
    if($this->date_arret){
    	$this->_fin_reelle = $this->date_arret;
      $this->_fin_reelle .= $this->time_arret ? " $this->time_arret" : " 23:59:00";
    }
    
    $chapitre = $this->_ref_element_prescription->_ref_category_prescription->chapitre;
    if($chapitre == "imagerie" || $chapitre == "consult"){
      $this->_debut_reel = "$this->debut 00:00:00";
      $this->_fin_reelle = "$this->debut 23:59:59";
    }
  }
  
  function updateDBFields(){
    parent::updateDBFields();
    if($this->_executant !== null){
      if($this->_executant == ""){
        $this->executant_prescription_line_id = "";
        $this->user_executant_id = "";
      } else {
	      $explode_executant = explode("-", $this->_executant);
	      if($explode_executant[0] === "CExecutantPrescriptionLine"){
	        $this->executant_prescription_line_id = $explode_executant[1];
	      } else {
	        $this->user_executant_id = $explode_executant[1];
	      }
      }
    }
  }
  
  function store(){
  	$get_guid = $this->_id ? false : true;
    
    if($msg = parent::store()){
  		return $msg;
  	}
    
  	// On met en session le dernier guid créé
    if($get_guid){
      $_SESSION["dPprescription"]["full_line_guid"] = $this->_guid;
    }
  }
  
  function loadView() {
    $this->loadRefsPrises();
    $this->loadRefsTransmissions();
  }
  
  /*
   * Vue modifiée en fonction de la présence de prises
   */
  function loadCompleteView(){
  	$this->loadRefsPrises();
  	
  	// Si la ligne comportent des prises ==> "à partir du"
  	if(count($this->_ref_prises)){
  		$this->_duree_prise = "";
	    if($this->fin){
	    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
	    } else {
        if($this->debut){
          $this->_duree_prise .= "à partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
        }
	    	if($this->duree){
	    		$this->_duree_prise .= " pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree).".";
	    	} 	
	    } 
  	}
  }
  
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $prescription_type = "", $mode_protocole = 0, $mode_pharma = 0) {	
		global $AppUI, $can;

	  $perm_infirmiere = $this->creator_id == $AppUI->user_id && 
                       !$this->signee && 
                       !$this->valide_infirmiere;
    
		

    // Cas d'une ligne de protocole  
    if($this->_protocole){
      $protocole =& $this->_ref_prescription;
      if($protocole->praticien_id){
        $protocole->loadRefPraticien();
        $perm_edit = $protocole->_ref_praticien->canEdit();    
      } elseif($protocole->function_id){
        $protocole->loadRefFunction();
        $perm_edit = $protocole->_ref_function->canEdit();
      } elseif($protocole->group_id){
        $protocole->loadRefGroup();
        $perm_edit = $protocole->_ref_group->canEdit();
      }
    } else {
      $perm_edit = $can->admin || ((!$this->signee) && 
                 ($this->praticien_id == $AppUI->user_id || $perm_infirmiere || $is_praticien));
    }
    
    $this->_perm_edit = $perm_edit;
    
    // Modification des dates
    if($perm_edit){
    	$this->_can_modify_dates = 1;
    }
    // View ALD
    if($perm_edit && !$this->_protocole){
    	$this->_can_view_form_ald = 1;
    }
    // View Conditionnel
    if($perm_edit){
    	$this->_can_view_form_conditionnel = 1;
    }
    // View signature praticien
    if(!$this->_protocole){
    	$this->_can_view_signature_praticien = 1;
    }
    // Affichage du formulaire de signature praticien
    if(!$this->_protocole && $is_praticien && ($this->praticien_id == $AppUI->user_id)){
    	$this->_can_view_form_signature_praticien = 1;
    }
    // Affichage du formulaire de signature infirmiere
    if(!$this->_protocole && !$is_praticien && !$this->signee && $this->creator_id == $AppUI->user_id && $this->_ref_prescription->type != "externe"){
    	$this->_can_view_form_signature_infirmiere = 1;
    }
    // Suppression de la ligne
    if ($perm_edit || $this->_protocole) {
  	  $this->_can_delete_line = 1;
  	}
  	// Modification de la posologie
  	if ($perm_edit){
    	$this->_can_modify_poso = 1;
    }
    // Modification de l'executant et du commentaire
    if ($perm_edit){
    	$this->_can_select_executant = 1;
    	$this->_can_modify_comment = 1;
    } 
	}
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefElement();
    $this->loadRefExecutant();
  }
  
  /*
   * Chargement de l'element
   */
  function loadRefElement() {
    if (!$this->_ref_element_prescription) {
	  	$element = new CElementPrescription();
	  	$this->_ref_element_prescription = $element->getCached($this->element_prescription_id);	
    }
  }
  
  /*
   * Chargement de l'executant
   */
  function loadRefExecutant(){
    if (!$this->_ref_executant) {
      if($this->executant_prescription_line_id){
		    $executant = new CExecutantPrescriptionLine();
		    $this->_ref_executant = $executant->getCached($this->executant_prescription_line_id);
      }
      if($this->user_executant_id){
        $user = new CMediusers();
        $this->_ref_executant = $user->getCached($this->user_executant_id);
      }
    }
  }
}

?>