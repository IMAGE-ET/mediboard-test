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
  
  function CFournisseur() {
    $this->CMbObject("fournisseur", "fournisseur_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["0"] = "CRefMateriel fournisseur_id";
     return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "societe"    => "notNull str maxLength|50",
      "adresse"    => "str",
      "codepostal" => "num length|5",
      "ville"      => "str",
      "telephone"  => "num",
      "mail"       => "email",
      "nom"        => "str maxLength|50",
      "prenom"     => "str maxLength|50"
    );
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
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "référence(s)", 
      "name"      => "ref_materiel", 
      "idfield"   => "reference_id", 
      "joinfield" => "fournisseur_id"
    );
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>