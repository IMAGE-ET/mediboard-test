<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CFournisseur class
 */
class CFournisseur extends CMbObject {
  // DB Table key
  var $fournisseur_id = null;
  
  // DB Fields
  var $societe       = null;
  var $adresse       = null;
  var $codepostal    = null;
  var $ville         = null;
  var $telephone     = null;
  var $mail          = null;
  var $prenom        = null;
  var $nom           = null;
  
  // Object References
  var $_ref_references = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fournisseur';
    $spec->key   = 'fournisseur_id';
    return $spec;
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["materiels"] = "CRefMateriel fournisseur_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "societe"    => "str notNull maxLength|50",
      "adresse"    => "str",
      "codepostal" => "num length|5",
      "ville"      => "str",
      "telephone"  => "num",
      "mail"       => "email",
      "nom"        => "str maxLength|50",
      "prenom"     => "str maxLength|50"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getSeeks() {
    return array (
      "societe" => "like",
      "ville"   => "like",
      "nom"     => "like",
      "prenom"  => "like"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->societe;
  }
  
  function loadRefsBack(){
    $this->_ref_references = new CRefMateriel;
    $where = array();
    $where["fournisseur_id"] = "= '$this->fournisseur_id'";
    $this->_ref_references = $this->_ref_references->loadList($where);
  } 	
  
}
?>