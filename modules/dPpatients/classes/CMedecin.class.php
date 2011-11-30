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
  var $rpps            = null;

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
    $backProps["patients_traites"]        = "CPatient medecin_traitant";
    $backProps["patients_correspondants"] = "CCorrespondant medecin_id";
    $backProps["sejours_adresses"]        = "CSejour adresse_par_prat_id";
    $backProps["consultations_adresses"]  = "CConsultation adresse_par_prat_id";
    $backProps["echanges_hprim21"]        = "CEchangeHprim21 object_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $phone_number_format = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
    
    $specs["nom"]             = "str notNull confidential seekable|begin";
    $specs["prenom"]          = "str seekable|begin";
    $specs["jeunefille"]      = "str confidential";
    $specs["adresse"]         = "text".(CAppUI::conf("dPpatients CMedecin medecin_strict") == 1 ? ' notNull' : '')." confidential";
    $specs["ville"]           = "str".(CAppUI::conf("dPpatients CMedecin medecin_strict") == 1 ? ' notNull' : '')." confidential seekable";
    $specs["cp"]              = "numchar".(CAppUI::conf("dPpatients CMedecin medecin_strict") == 1 ? ' notNull' : '')." maxLength|5 confidential";
    $specs["tel"]             = "numchar".(CAppUI::conf("dPpatients CMedecin medecin_strict") == 1 ? ' notNull' : '')." length|10 confidential mask|$phone_number_format";
    $specs["fax"]             = "numchar length|10 confidential mask|$phone_number_format";
    $specs["portable"]        = "numchar length|10 confidential mask|$phone_number_format";
    $specs["email"]           = "str confidential";
    $specs["disciplines"]     = "text seekable";
    $specs["orientations"]    = "text";
    $specs["complementaires"] = "text";
    $specs["type"]            = "enum list|medecin|kine|sagefemme|infirmier|dentiste|podologue|pharmacie|maison_medicale|autre default|medecin";
    $specs["adeli"]           = "numchar length|9 confidential mask|99S9S99999S9";
    $specs["rpps"]            = "numchar length|11 confidential mask|99999999999 control|luhn";
    
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
    	$this->_view = "$this->nom $this->prenom";
    	if ($this->type) {
    	  $this->_view .= " ({$this->_specs['type']->_locales[$this->type]})";
    	} 
    }
  }
	 
  function loadRefs() {
    // Backward references
    $obj = new CPatient();
    $this->_ref_patients = $obj->loadList("medecin_traitant = '$this->medecin_id'");
  }
  
  function loadExactSiblings($strict_cp = true) {
    $medecin = new CMedecin();
    $where           = array();
    $where["nom"]    = $this->_spec->ds->prepare(" = %", $this->nom);
    $where["prenom"] = $this->_spec->ds->prepare(" = %", $this->prenom);
    
    if (!$strict_cp) {
      $cp = substr($this->cp, 0, 2);
      $where["cp"] = " LIKE '{$cp}___'";
    } else {
      $where["cp"] = " = '$this->cp'";
    }
    
    $medecin->escapeValues();

    $siblings = $medecin->loadList($where);
    unset($siblings[$this->_id]);

    return $siblings;
  }
  
  function toVcard(CMbvCardExport $vcard) {
    $vcard->addName($this->prenom, $this->nom, ucfirst($this->civilite));
    $vcard->addPhoneNumber($this->tel     , 'WORK');
    $vcard->addPhoneNumber($this->portable, 'CELL');
    $vcard->addPhoneNumber($this->fax     , 'FAX');
    $vcard->addEmail($this->email);
    $vcard->addAddress($this->adresse, $this->ville, $this->cp, $this->pays, 'WORK');
  }
}
?>