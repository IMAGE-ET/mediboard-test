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

  // Form fields
	var $_tel1 = null;
	var $_tel2 = null;
	var $_tel3 = null;
	var $_tel4 = null;
	var $_tel5 = null;
	var $_fax1 = null;
	var $_fax2 = null;
	var $_fax3 = null;
	var $_fax4 = null;
	var $_fax5 = null;

  // Object References
  var $_ref_patients = null;

	function CMedecin() {
		$this->CMbObject("medecin", "medecin_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
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
      "tel"             => "numchar length|10 confidential",
      "fax"             => "numchar length|10 confidential",
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
    
    $this->updateFormTel("tel","_tel");
    $this->updateFormTel("fax","_fax");
  }
  
  function updateDBFields() {
    $this->updateDBTel("tel", "_tel");
    $this->updateDBTel("fax", "_fax");
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
    $siblings = $medecin->loadMatchingList();
    unset($siblings[$this->_id]);
    return $siblings;
  }
}
?>