<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

class CChapitreDoc extends CMbObject {
  // DB Table key
  var $doc_chapitre_id = null;
    
  // DB Fields
  var $pere_id  = null;
  var $group_id = null;
  var $nom      = null;
  var $code     = null;
  
  // Fwd refs
  var $_ref_pere  = null;
  var $_ref_group = null;
  
  // Back Refs
  var $_ref_chapitres_doc = null;
  
  // Other fields
  var $_level              = null;
  var $_path               = null;
  var $_chaps_and_subchaps = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'doc_chapitres';
    $spec->key   = 'doc_chapitre_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["chapitres_doc"] = "CChapitreDoc pere_id";
    $backRefs["chapitres_ged"] = "CDocGed doc_chapitre_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["pere_id"]  = "ref class|CChapitreDoc";
    $specs["group_id"] = "ref class|CGroups";
    $specs["nom"]      = "str notNull maxLength|50";
    $specs["code"]     = "str notNull maxLength|10";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "[$this->code] $this->nom";
    $this->_shortview = $this->code; 
  }
  
  function loadParent() {
    if (!$this->_ref_pere) {
      $this->_ref_pere = new CChapitreDoc;
      $this->_ref_pere->load($this->pere_id);
    }
  }
  
  function loadRefGroup() {
    if (!$this->_ref_group) {
      $this->_ref_group = new CGroups();
      $this->_ref_group->load($this->group_id);
    }
  }
  
  function loadRefsFwd() {
    $this->loadParent();
    $this->loadRefGroup();
  }
  
  function computeLevel() {
    if (!$this->pere_id) {
      return $this->_level = 0;
    }
    
    $this->loadParent();
    return $this->_level = $this->_ref_pere->computeLevel() + 1;
  }
  
  function computePath() {
    if (!$this->pere_id) {
      return $this->_path = "$this->code-";
    }
    
    $this->loadParent();
    return $this->_path = $this->_ref_pere->computePath().$this->code."-";
  }

  function loadSections() {
    $this->_ref_chapitres_doc = $this->loadBackRefs("chapitres_doc", "code");
  }
  
  function loadChapsDeep($n = 0) {
    $this->_chaps_and_subchaps = array($this->_id);
    $this->_level = $n;
    if(CAppUI::conf("dPqualite CChapitreDoc profondeur") > ($this->_level + 1)) {
      $this->loadSections();
      foreach ($this->_ref_chapitres_doc as &$_chapitre) {
        $_chapitre->_ref_pere =& $this;
        $this->_chaps_and_subchaps = array_merge($this->_chaps_and_subchaps, $_chapitre->loadChapsDeep($this->_level + 1));
      }
    }
    return $this->_chaps_and_subchaps;
  }
}
?>