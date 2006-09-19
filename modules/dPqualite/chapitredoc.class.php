<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CChapitreDoc class
 */
class CChapitreDoc extends CMbObject {
  // DB Table key
  var $doc_chapitre_id = null;
    
  // DB Fields
  var $nom  = null;
  var $code = null;

  function CChapitreDoc() {
    $this->CMbObject("doc_chapitres", "doc_chapitre_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_props["nom"]             = "str|maxLength|50|notNull";
    $this->_props["code"]            = "str|maxLength|10|notNull";
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code ." &mdash; " . $this->nom;
    $this->_shortview = $this->code; 
  }
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "procédure(s)", 
      "name"      => "doc_ged", 
      "idfield"   => "doc_ged_id", 
      "joinfield" => "doc_chapitre_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>