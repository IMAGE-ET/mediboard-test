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
  var $group_id = null;
  var $nom      = null;
  
  // Fwd refs
  var $_ref_group = null;

  function CThemeDoc() {
    $this->CMbObject("doc_themes", "doc_theme_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["documents_ged"] = "CDocGed doc_theme_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "group_id" => "ref class|CGroups",
      "nom"      => "notNull str maxLength|50"
    );
    return array_merge($specsParent, $specs);
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefGroup() {
    if (!$this->_ref_group) {
      $this->_ref_group = new CGroups();
      $this->_ref_group->load($this->group_id);
    }
  }
  
  function loadRefsFwd() {
    $this->loadRefGroup();
  }
    
}
?>