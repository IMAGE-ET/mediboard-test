<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */


/**
 * The CCategorieDoc class
 */
class CCategorieDoc extends CMbObject {
  // DB Table key
  var $doc_categorie_id = null;
    
  // DB Fields
  var $nom  = null;
  var $code = null;

  function CCategorieDoc() {
    $this->CMbObject("doc_categories", "doc_categorie_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_props["nom"]              = "str|maxLength|50|notNull";
    $this->_props["code"]             = "str|maxLength|1|notNull";
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
      "joinfield" => "doc_categorie_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>