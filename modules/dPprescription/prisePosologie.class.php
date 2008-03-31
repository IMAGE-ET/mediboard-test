<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CMomentUnitaire class
 */
class CPrisePosologie extends CMbObject {
  
	// DB Table key
  var $prise_posologie_id = null;
  
  // DB Fields
  var $prescription_line_id  = null;
  var $moment_unitaire_id    = null;
  var $quantite              = null;
  
  var $nb_fois               = null;
  var $unite_fois            = null;
  var $nb_tous_les           = null;
  var $unite_tous_les        = null;
  
  
  function CPrisePosologie() {
    $this->CMbObject("prise_posologie", "prise_posologie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  
  function loadRefPrescriptionLine(){
    $this->_ref_prescription_line = new CPrescriptionLineMedicament();
    $this->_ref_prescription_line->load($this->prescription_line_id);
  }
  
  function loadRefMoment(){
    $this->_ref_moment = new CMomentUnitaire();
    $this->_ref_moment->load($this->moment_unitaire_id);	
  }
  
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_line_id" => "ref class|CPrescriptionLineMedicament notNull cascade",
      "moment_unitaire_id"   => "ref class|CMomentUnitaire",
      "quantite"             => "float",
      "nb_fois"              => "float",
      "unite_fois"           => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "nb_tous_les"          => "float",
      "unite_tous_les"       => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->loadRefPrescriptionLine();
    $this->loadRefMoment();
    
    $this->_view = $this->quantite;
    $this->_view .= " ".$this->_ref_prescription_line->_unite_prise;
    if($this->moment_unitaire_id){
    	$this->_view .= " ".$this->_ref_moment->_view;
    }
    if($this->nb_fois){
    	$this->_view .= " ".$this->nb_fois." fois";
    }
    if($this->unite_fois && !$this->nb_tous_les){
    	$this->_view .= " par ".$this->unite_fois;
    }
    if($this->nb_tous_les && $this->unite_tous_les){
    	$this->_view .= " tous les ".$this->nb_tous_les." ".$this->unite_tous_les;
    }   
  }
}
  
?>