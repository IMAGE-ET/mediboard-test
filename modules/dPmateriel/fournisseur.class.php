<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author Sébastien Fillonneau
 */

require_once($AppUI->getSystemClass("mbobject"));
require_once($AppUI->getModuleClass("dPmateriel", "refmateriel"));

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
    
    $this->_props["societe"]    = "str|maxLength|50|notNull";
    $this->_props["adresse"]    = "str";
    $this->_props["codepostal"] = "num|length|5";
    $this->_props["ville"]      = "str";
    $this->_props["telephone"]  = "num";
    $this->_props["mail"]       = "email";
    $this->_props["nom"]        = "str|maxLength|50";
    $this->_props["prenom"]     = "str|maxLength|50";
    
    $this->_seek["societe"] = "like";
    $this->_seek["ville"]   = "like";
    $this->_seek["nom"]     = "like";
    $this->_seek["prenom"]  = "like";
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
    return CDpObject::canDelete( $msg, $oid, $tables );
  }
}
?>