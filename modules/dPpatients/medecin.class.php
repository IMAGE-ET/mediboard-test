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
	var $portable        = null;
	var $email           = null;
  var $disciplines     = null;
  var $orientations    = null;
  var $complementaires = null;
  var $type            = null;
  var $adeli           = null;

  // Object References
  var $_ref_patients = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'medecin';
    $spec->key   = 'medecin_id';
    return $spec;
  }
	
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["patients_traites"] = "CPatient medecin_traitant";
    $backProps["patients_correspondants"] = "CCorrespondant medecin_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["nom"]             = "str notNull confidential seekable|begin";
    $specs["prenom"]          = "str notNull confidential seekable|begin";
    $specs["jeunefille"]      = "str confidential";
    $specs["adresse"]         = "text confidential";
    $specs["ville"]           = "str confidential seekable";
    $specs["cp"]              = "numchar maxLength|5 confidential";
    $specs["tel"]             = "numchar length|10 confidential mask|99S99S99S99S99";
    $specs["fax"]             = "numchar length|10 confidential mask|99S99S99S99S99";
    $specs["portable"]        = "numchar length|10 confidential mask|99S99S99S99S99";
    $specs["email"]           = "str confidential";
    $specs["disciplines"]     = "text confidential seekable";
    $specs["orientations"]    = "text confidential";
    $specs["complementaires"] = "text confidential";
    $specs["type"]            = "enum list|medecin|kine|sagefemme|infirmier default|medecin";
    $specs["adeli"]           = "numchar length|9 confidential mask|99S9S99999S9";
    return $specs;
  }
  
  function countPatients() {
    $this->_count_patients_traites        = $this->countBackRefs("patients_traites");
    $this->_count_patients_correspondants = $this->countBackRefs("patients_correspondants");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    if ($this->type == 'medecin') {
    	$this->_view = "Dr $this->nom $this->prenom";
    }
    else {
    	$this->_view = "$this->nom $this->prenom ({$this->_specs['type']->_locales[$this->type]})";
    }
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