<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

class CThemeDoc extends CMbObject {
  // DB Table key
  var $doc_theme_id = null;
    
  // DB Fields
  var $group_id = null;
  var $nom      = null;
  
  // Fwd refs
  var $_ref_group = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'doc_themes';
    $spec->key   = 'doc_theme_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["documents_ged"] = "CDocGed doc_theme_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["group_id"] = "ref class|CGroups";
    $specs["nom"]      = "notNull str maxLength|50";
    return $specs;
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