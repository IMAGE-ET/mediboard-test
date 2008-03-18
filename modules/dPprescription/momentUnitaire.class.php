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
  
  function CMomentUnitaire() {
    $this->CMbObject("moment_unitaire", "moment_unitaire_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "libelle"     => "str notNull",
      "heure_min"   => "time",
      "heure_max"   => "time",
      "type_moment" => "enum list|matin|midi|soir|apres_midi|horaire|autre"
    );
    return array_merge($specsParent, $specs);
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
  
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
    if($this->heure_min && $this->heure_max){
    	$this->_view .= "(de $this->heure_min à $this->max)";
    }
  }
}
  
?>