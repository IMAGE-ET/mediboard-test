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
  var $heure_min = null;
  var $heure_max = null;
  var $type_moment = null;
  var $principal = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'moment_unitaire';
    $spec->key   = 'moment_unitaire_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "libelle"     => "str notNull",
      "heure_min"   => "time",
      "heure_max"   => "time",
      "type_moment" => "enum list|matin|midi|soir|apres_midi|horaire|autre",
      "principal"   => "bool"
    );
    return array_merge($specsParent, $specs);
  }

  function getBackRefs() {
    $back = parent::getBackRefs();
    $back["associations"] = "CAssociationMoment moment_unitaire_id";
    return $back;
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
    
    foreach($tabMoment as &$moment){
      if($moment->principal){
      	$moments["Principales"][] = $moment;
      } else {
    	  $moments[$moment->type_moment][] = $moment;
      }
    }
    return $moments;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    if($this->heure_min && $this->heure_max){
    	$this->_view .= "(de $this->heure_min à $this->max)";
    }
  }
}
  
?>