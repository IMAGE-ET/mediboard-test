<?php

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision$
* @author Romain Ollivier
*/

class CCatalogueLabo extends CMbObject {
  // DB Table key
  var $catalogue_labo_id = null;
  
  // DB References
  var $pere_id = null;
  
  // DB fields
  var $identifiant = null;
  var $libelle     = null;
  var $function_id = null;
  var $obsolete    = null;
  
  // Fwd References
  var $_ref_pere     = null;
  var $_ref_function = null;
  
  // Back references
  var $_ref_examens_labo = null;
  var $_ref_catalogues_labo = null;
  
  // Distant references
  var $_ref_prescription_items = null;
  var $_count_examens_labo = null;
  var $_total_examens_labo = null;
  
  // Form fields
  var $_level = null;
  
  function CCatalogueLabo() {
    parent::__construct();
    $this->_locked =& $this->_external;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'catalogue_labo';
    $spec->key   = 'catalogue_labo_id';
    return $spec;
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if ($this->hasAncestor($this)) {
      return "Cyclic catalog creation";
    }
    
    // Checks whether there is a sibling catalogue in the same hierarchy
    $root = $this->getRootCatalogue();
    foreach ($this->getSiblings() as $_sibling) {
      $_root = $_sibling->getRootCatalogue();
      if ($root->_id == $_root->_id) {
        return "CCatalogue-sibling-conflict ($this->identifiant)";
      }
    }
  }
  
  /**
   * Recursive root catalogue accessor
   */
  function getRootCatalogue() {
    if (!$this->pere_id) {
      return $this;
    }

    $this->loadParent();
    return $this->_ref_pere->getRootCatalogue();
  }
  
  /**
   * load catalogues with same identifier
   */
  function getSiblings() {
    $catalogue = new CCatalogueLabo;
    $where = array();
    $where["identifiant"] = "= '$this->identifiant'";
    $where["catalogue_labo_id"] = "!= '$this->catalogue_labo_id'";
    return $catalogue->loadList($where);
  }
  
  /**
   * Checks whether given catalogue is an ancestor
   */
  function hasAncestor($catalogue) {
    if (!$this->_id) {
      return false;
    }

    if ($catalogue->_id == $this->pere_id) {
      return true;
    }
    
    $this->loadParent();
    return $this->_ref_pere->hasAncestor($catalogue);
  }
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "pere_id"     => "ref class|CCatalogueLabo",
      "function_id" => "ref class|CFunctions",
      "identifiant" => "str maxLength|10 notNull",
      "libelle"     => "str notNull",
      "obsolete"    => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["catalogues_labo"] = "CCatalogueLabo pere_id";
    $backProps["examens_labo"   ] = "CExamenLabo catalogue_labo_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->identifiant;
    $this->_view = $this->libelle;
  }
  
  function computeLevel() {
    if (!$this->pere_id) {
      return $this->_level = 0;
    }
    
    $this->loadParent();
    return $this->_level = $this->_ref_pere->computeLevel() + 1;
  }
  
  function loadParent() {
    if (!$this->_ref_pere) {
      $this->_ref_pere = new CCatalogueLabo;
      $this->_ref_pere->load($this->pere_id);
    }
  }
  
  function loadRefFunction() {
    if(!$this->_ref_function) {
      $this->_ref_function = new CFunctions();
      $this->_ref_function->load($this->function_id);
    }
  }

  function loadRefsFwd() {
    $this->loadParent();
    $this->loadRefFunction();
  }

  function loadSections() {
    $this->_ref_catalogues_labo = $this->loadBackRefs("catalogues_labo", "libelle");
  }
  
  function loadExamens($withObsolete = false) {
    $this->_ref_examens_labo = $this->loadBackRefs("examens_labo");	
  }
  
  function loadSectionsWithoutObsolete(){
    $catalogueLabo = new CCatalogueLabo();
    $catalogueLabo->pere_id = $this->_id;
    $catalogueLabo->obsolete = 0;
    $this->_ref_catalogues_labo = $catalogueLabo->loadMatchingList();
  }
  
  
  function loadExamensWithoutObsolete(){
  	$examenLabo = new CExamenLabo();
  	$examenLabo->catalogue_labo_id = $this->_id;
  	$examenLabo->obsolete = 0;
  	$this->_ref_examens_labo = $examenLabo->loadMatchingList();
  }
  
  function loadRefsBack() {
    parent::loadRefsBack();
    
    $this->loadSectionsWithoutObsolete();
    $this->loadExamensWithoutObsolete();
  }
  
  function loadRefsDeep($n = 0) {
    $this->_level = $n;
    $this->loadParent();
    $this->loadExternal();
    $this->_count_examens_labo = $this->countBackRefs("examens_labo");
    $this->_total_examens_labo = $this->_count_examens_labo;
    
    $this->loadSectionsWithoutObsolete();
    
    foreach ($this->_ref_catalogues_labo as &$_catalogue) {
      $_catalogue->_ref_pere =& $this;
      $_catalogue->loadRefsDeep($this->_level + 1);
      $this->_total_examens_labo += $_catalogue->_total_examens_labo;
    }
  }
  
  function getPerm($perm_type) {
    if($this->function_id && !$this->pere_id) {
      $this->loadRefFunction();
      return $this->_ref_function->getPerm($perm_type);
    } elseif($this->pere_id) {
      $this->loadParent();
      return $this->_ref_pere->getPerm($perm_type);
    } else {
      return true;
    }
  }
}

?>