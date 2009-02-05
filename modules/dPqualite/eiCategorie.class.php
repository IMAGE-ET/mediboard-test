<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

class CEiCategorie extends CMbObject {
  // DB Table key
  var $ei_categorie_id  = null;
    
  // DB Fields
  var $nom              = null;

  // Object References
  var $_ref_items       = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ei_categories';
    $spec->key   = 'ei_categorie_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["items"] = "CEiItem ei_categorie_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["nom"] = "str notNull maxLength|50";
    return $specs;
  }
  
  function loadRefsBack() {
    $this->_ref_items = $this->loadBackRefs("items", "nom");
  }
}
?>