<?php /* $Id:  $ */

/**
 *	@package Mediboard
 *	@subpackage dPetablissement
 *	@version $Revision: 2663 $
 *  @author Thomas Despoix
*/

/**
 * The CEtabExterne class
 */
class CEtabExterne extends CMbObject {
  // DB Table key
	var $etab_id       = null;	

  // DB Fields
	var $nom            = null;
  var $raison_sociale = null;
  var $adresse        = null;
  var $cp             = null;
  var $ville          = null;
  var $tel            = null;
  var $fax            = null;
  var $finess         = null;
  var $siret          = null;
  var $ape            = null;
  
  // Form fields
  var $_tel1        = null;
  var $_tel2        = null;
  var $_tel3        = null;
  var $_tel4        = null;
  var $_tel5        = null;
    
  var $_fax1        = null;
  var $_fax2        = null;
  var $_fax3        = null;
  var $_fax4        = null;
  var $_fax5        = null;
  
  function CEtabExterne() {
    $this->CMbObject("etab_externe", "etab_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["transferts"] = "CSejour etablissement_transfert_id";
    return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "nom"            => "notNull str confidential",
      "raison_sociale" => "str maxLength|50",
      "adresse"        => "text confidential",
      "cp"             => "numchar length|5",
      "ville"          => "str maxLength|50 confidential",
      "tel"            => "numchar length|10",
      "fax"            => "numchar length|10",
      "finess"         => "numchar length|9",
      "siret"          => "str length|14",
      "ape"            => "str length|4"
    );
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
 
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
    
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
    if ($this->_tel1 !== null 
     && $this->_tel2 !== null 
     && $this->_tel3 !== null 
     && $this->_tel4 !== null
     && $this->_tel5 !== null) {
      $this->tel = 
        $this->_tel1 .
        $this->_tel2 .
        $this->_tel3 .
        $this->_tel4 .
        $this->_tel5;
    }

    if ($this->_fax1 !== null 
     && $this->_fax2 !== null 
     && $this->_fax3 !== null 
     && $this->_fax4 !== null
     && $this->_fax5 !== null) {
      $this->fax = 
        $this->_fax1 .
        $this->_fax2 .
        $this->_fax3 .
        $this->_fax4 .
        $this->_fax5;
    }
  }  
}
?>