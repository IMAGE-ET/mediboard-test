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
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "prescription_line_id" => "ref class|CPrescriptionLineMedicament notNull cascade",
      "moment_unitaire_id"   => "ref class|CMomentUnitaire",
      "quantite"             => "float",
      "nb_fois"              => "num",
      "unite_fois"           => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "nb_tous_les"          => "num",
      "unite_tous_les"       => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
  }
}
  
?>