<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("mbobject"));

require_once($AppUI->getModuleClass("dPpatients", "patients"));

/**
 * The CMedecin Class
 */
class CMedecin extends CMbObject {
  // DB Table key
	var $medecin_id = null;

  // DB Fields
	var $nom             = null;
  var $prenom          = null;
  var $nom_jeunefille  = null;
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
    
    $this->_props["nom"]             = "str|notNull|confidential";
    $this->_props["prenom"]          = "str|confidential";
    $this->_props["nom_jeunefille"]  = "str|confidential";
    $this->_props["adresse"]         = "str|confidential";
    $this->_props["ville"]           = "str|confidential";
    $this->_props["cp"]              = "num|length|5|confidential";
    $this->_props["tel"]             = "num|length|10|confidential";
    $this->_props["fax"]             = "num|length|10|confidential";
    $this->_props["email"]           = "str|confidential";
    $this->_props["disciplines"]     = "str|confidential";
    $this->_props["orientations"]    = "str|confidential";
    $this->_props["complementaires"] = "str|confidential";
    
    $this->_seek["nom"]         = "likeBegin";
    $this->_seek["prenom"]      = "likeBegin";
    $this->_seek["ville"]       = "like";
    $this->_seek["disciplines"] = "like";
	}
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->nom $this->prenom";
    
    $this->_tel1 = substr($this->tel, 0, 2);
    $this->_tel2 = substr($this->tel, 2, 2);
    $this->_tel3 = substr($this->tel, 4, 2);
    $this->_tel4 = substr($this->tel, 6, 2);
    $this->_tel5 = substr($this->tel, 8, 2);

    $this->_fax1 = substr($this->fax, 0, 2);
    $this->_fax2 = substr($this->fax, 2, 2);
    $this->_fax3 = substr($this->fax, 4, 2);
    $this->_fax4 = substr($this->fax, 6, 2);
    $this->_fax5 = substr($this->fax, 8, 2);
  }
  
  function updateDBFields() {
    if(($this->_tel1 !== null) && ($this->_tel2 !== null) && ($this->_tel3 !== null) && ($this->_tel4 !== null) && ($this->_tel5 !== null)) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }
    if(($this->_fax1 !== null) && ($this->_fax2 !== null) && ($this->_fax3 !== null) && ($this->_fax4 !== null) && ($this->_fax5 !== null)) {
      $this->fax = 
        $this->_fax1 .
        $this->_fax2 .
        $this->_fax3 .
        $this->_fax4 .
        $this->_fax5;
    }
  }

	function check() {
    // Data checking
    $msg = null;

    if (!strlen($this->nom)) {
      $msg .= "Nom invalide: '$this->nom'<br />";
    }

    if (!strlen($this->prenom)) {
      $msg .= "Nom invalide: '$this->prenom'<br />";
    }
        
    return $msg . parent::check();
	}
	
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "patient(s)", 
      "name"      => "patients", 
      "idfield"   => "patient_id", 
      "joinfield" => "medecin_traitant"
    );
    
    return parent::canDelete( $msg, $oid, $tables );
  }
  
  function loadRefs() {
    // Backward references
    $obj = new CPatient();
    $this->_ref_patients = $obj->loadList("medecin_traitant = '$this->medecin_id'");
  }
  
  function getExactSiblings() {
  	$where = array();
  	$where["medecin_id"] = "!= '".addslashes($this->medecin_id)."'";
  	$where["nom"] = "= '".addslashes($this->nom)."'";
  	$where["prenom"] = "= '".addslashes($this->prenom)."'";
  	if($this->cp == null)
      $where["cp"] = "IS NULL";
  	else
  	  $where["cp"] = "= '".addslashes($this->cp)."'";
      
  	$siblings = new CMedecin;
  	$siblings = $siblings->loadList($where);
  	return $siblings;
  }

  function getSiblings() {
    $sql = "SELECT medecin_id, nom, prenom, adresse, ville, CP " .
      		"FROM medecin WHERE " .
      		"medecin_id != '$this->medecin_id' " .
      		"AND nom = '$this->nom' AND prenom = '$this->prenom'";
    $siblings = db_loadlist($sql);
    return $siblings;
  }
}
?>