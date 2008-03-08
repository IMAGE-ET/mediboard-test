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
  	$specsParent = parent::getSpecs();
    $specs = array (
      "nom"            => "notNull str confidential",
      "raison_sociale" => "str maxLength|50",
      "adresse"        => "text confidential",
      "cp"             => "numchar length|5",
      "ville"          => "str maxLength|50 confidential",
      "tel"            => "numchar length|10",
      "fax"            => "numchar length|10",
      "finess"         => "numchar length|9",
      "siret"          => "str length|14",
      "ape"            => "str maxLength|6 confidential",
      
      "_tel1" => "num length|2",
      "_tel2" => "num length|2",
      "_tel3" => "num length|2",
      "_tel4" => "num length|2",
      "_tel5" => "num length|2",
      
      "_fax1" => "num length|2",
      "_fax2" => "num length|2",
      "_fax3" => "num length|2",
      "_fax4" => "num length|2",
      "_fax5" => "num length|2",
    );
    return array_merge($specsParent, $specs);
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
 
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->nom;
    
    $this->updateFormTel("tel", "_tel");
    $this->updateFormTel("fax", "_fax");   
  }
  
  function updateDBFields() {
    $this->updateDBTel("tel", "_tel");
    $this->updateDBTel("fax", "_fax");
  }  
}
?>