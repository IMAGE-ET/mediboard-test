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
  var $adresse_suite = null;
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

    static $props = array (
      "societe"    => "str|maxLength|50|notNull",
      "adresse"    => "str",
      "codepostal" => "num|length|5",
      "ville"      => "str",
      "telephone"  => "num",
      "mail"       => "email",
      "nom"        => "str|maxLength|50",
      "prenom"     => "str|maxLength|50"
    );
    $this->_props =& $props;

    static $seek = array (
      "societe" => "like",
      "ville"   => "like",
      "nom"     => "like",
      "prenom"  => "like"
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }

  function getSpecs() {
    return array (
      "societe"    => "str|maxLength|50|notNull",
      "adresse"    => "str",
      "codepostal" => "num|length|5",
      "ville"      => "str",
      "telephone"  => "num",
      "mail"       => "email",
      "nom"        => "str|maxLength|50",
      "prenom"     => "str|maxLength|50"
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