<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
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
  
  function getBackProps() {
      $backProps = parent::getBackProps();
      $backProps["materiels"] = "CRefMateriel fournisseur_id";
     return $backProps;
  }
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "societe"    => "str notNull maxLength|50 seekable",
      "adresse"    => "str",
      "codepostal" => "num length|5",
      "ville"      => "str seekable",
      "telephone"  => "num",
      "mail"       => "email",
      "nom"        => "str maxLength|50 seekable",
      "prenom"     => "str maxLength|50 seekable"
    );
    return array_merge($specsParent, $specs);
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