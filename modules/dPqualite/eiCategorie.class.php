<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision$
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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items"] = "CEiItem ei_categorie_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"] = "str notNull maxLength|50";
    return $specs;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefsBack() {
    $this->_ref_items = $this->loadBackRefs("items", "nom");
  }
}
?>