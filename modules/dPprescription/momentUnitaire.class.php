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
class CMomentUnitaire extends CMbObject {
  // DB Table key
  var $moment_unitaire_id = null;
  var $libelle  = null;
  var $heure = null;
  var $type_moment = null;
  var $principal = null;
  
  // Forms fields
  var $_heure = null;
  var $_min   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'moment_unitaire';
    $spec->key   = 'moment_unitaire_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["libelle"]     = "str notNull";
    $specs["heure"]       = "time";
    $specs["type_moment"] = "enum list|matin|midi|soir|apres_midi|horaire|autre";
    $specs["principal"]   = "bool";
    $specs["_heure"]      = "num";
    $specs["_min"]        = "num";
    return $specs;
  }

	function getBackRefs() {
	  $backRefs = parent::getBackRefs();
	  $backRefs["associations"] = "CAssociationMoment moment_unitaire_id";
	  $backRefs["prises"]       = "CPrisePosologie moment_unitaire_id";
	  return $backRefs;
	}
	
  // Chargement des moments
  static function loadAllMoments(){
    $moment = new CMomentUnitaire();
    $tabMoment = $moment->loadList();
    foreach($tabMoment as &$moment){
     	$moments[$moment->type_moment][] = $moment;
    }
    return $moments;
  }
  
  // Chargement des moments
  static function loadAllMomentsWithPrincipal(){
    // Chargement de tous les moments
  	$moment = new CMomentUnitaire();
    $tabMoment = $moment->loadList();
    
    $moment_complexe = new CMomentComplexe();
    $moment_complexe->visible = 1;
    $moments_complexe = $moment_complexe->loadMatchingList();
    
    foreach($tabMoment as &$moment){
      if($moment->principal){
      	$moments["Principaux"][] = $moment;
      } else {
        if (!CAppUI::conf("dPprescription CMomentUnitaire principaux")){
          $moments[$moment->type_moment][] = $moment;
        }
      }
    }
    foreach($moments_complexe as &$_moment_complexe){
    	$moments["Complexes"][] = $_moment_complexe;
    }
    return $moments;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    if($this->heure) {
      $this->_heure = intval(substr($this->heure, 0, 2));
    }
  }
  
	function updateDBFields(){
	 	parent::updateDBFields();	 	
	  if($this->_heure !== null) {
	    $this->heure = ($this->_heure ? $this->_heure.":00:00" : '');
	  }
	}
}
  
?>