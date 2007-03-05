<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CEiItem class
 */
class CEiItem extends CMbObject {
  // DB Table key
  var $ei_item_id = null;
    
  // DB Fields
  var $ei_categorie_id  = null;
  var $nom              = null;

  // Object References
  var $_ref_categorie   = null;

  function CEiItem() {
    $this->CMbObject("ei_item", "ei_item_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function updateFormFields() {
    $this->_view = $this->nom;
  }
  
  function getSpecs() {
    return array (
      "ei_categorie_id" => "notNull ref",
      "nom"             => "notNull str maxLength|50"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_categorie = new CEiCategorie;
    $this->_ref_categorie->load($this->ei_categorie_id);
  }
}
?>