<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CMedecin Class
 */
class CMedecin extends CMbObject {
  // DB Table key
	var $medecin_id = null;

  // DB Fields
	var $nom             = null;
  var $prenom          = null;
  var $jeunefille      = null;
	var $adresse         = null;
	var $ville           = null;
	var $cp              = null;
	var $tel             = null;
	var $fax             = null;
	var $email           = null;
  var $disciplines     = null;
  var $orientations    = null;
  var $complementaires = null;

  // Object References
  var $_ref_patients = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'medecin';
    $spec->key   = 'medecin_id';
    return $spec;
  }
	
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["patients_traites"] = "CPatient medecin_traitant";
    $backRefs["patients1"] = "CPatient medecin1";
    $backRefs["patients2"] = "CPatient medecin2";
    $backRefs["patients3"] = "CPatient medecin3";
    return $backRefs;
  }
    
  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      "nom"             => "notNull str confidential",
      "prenom"          => "notNull str confidential",
      "jeunefille"      => "str confidential",
      "adresse"         => "text confidential",
      "ville"           => "str confidential",
      "cp"              => "numchar maxLength|5 confidential",
      "tel"             => "numchar length|10 confidential mask|99S99S99S99S99",
      "fax"             => "numchar length|10 confidential mask|99S99S99S99S99",
      "email"           => "str confidential",
      "disciplines"     => "text confidential",
      "orientations"    => "text confidential",
      "complementaires" => "text confidential"
    ));
  }
  
  function getSeeks() {
    return array (
      "nom"         => "likeBegin",
      "prenom"      => "likeBegin",
      "ville"       => "like",
      "disciplines" => "like"
    );
  }
  
  function countPatients() {
    $this->_count_patients_traites = $this->countBackRefs("patients_traites");
    $this->_count_patients1 = $this->countBackRefs("patients1");
    $this->_count_patients2 = $this->countBackRefs("patients2");
    $this->_count_patients3 = $this->countBackRefs("patients3");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->nom $this->prenom";
  }
	 
  function loadRefs() {
    // Backward references
    $obj = new CPatient();
    $this->_ref_patients = $obj->loadList("medecin_traitant = '$this->medecin_id'");
  }
  
  function loadExactSiblings() {
    $medecin = new CMedecin();
    $medecin->nom    = $this->nom;
    $medecin->prenom = $this->prenom;
    $medecin->cp     = $this->cp;
    
    $medecin->escapeDBFields();
    $siblings = $medecin->loadMatchingList();
    unset($siblings[$this->_id]);
    return $siblings;
  }
}
?>