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
  }

  function getSpecs() {
    return array (
      "nom"  => "str|maxLength|50|notNull",
      "code" => "str|maxLength|1|notNull"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code ." - " . $this->nom;
    $this->_shortview = $this->code; 
  }  

  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "msg-CDocGed-canDelete",
      "name"      => "doc_ged", 
      "idfield"   => "doc_ged_id", 
      "joinfield" => "doc_categorie_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>