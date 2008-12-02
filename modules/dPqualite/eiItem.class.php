<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

class CEiItem extends CMbObject {
  // DB Table key
  var $ei_item_id = null;
    
  // DB Fields
  var $ei_categorie_id  = null;
  var $nom              = null;

  // Object References
  var $_ref_categorie   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ei_item';
    $spec->key   = 'ei_item_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["ei_categorie_id"] = "notNull ref class|CEiCategorie";
    $specs["nom"]             = "notNull str maxLength|50";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefsFwd() {
    $this->_ref_categorie = new CEiCategorie;
    $this->_ref_categorie->load($this->ei_categorie_id);
  }
}
?>