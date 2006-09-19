<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CThemeDoc class
 */
class CThemeDoc extends CMbObject {
  // DB Table key
  var $doc_theme_id = null;
    
  // DB Fields
  var $nom = null;

  function CThemeDoc() {
    $this->CMbObject("doc_themes", "doc_theme_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_props["nom"]          = "str|maxLength|50|notNull";
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
    
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "procédure(s)", 
      "name"      => "doc_ged", 
      "idfield"   => "doc_ged_id", 
      "joinfield" => "doc_theme_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>