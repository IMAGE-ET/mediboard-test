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
    
    $this->_props["ei_categorie_id"] = "ref|notNull";
    $this->_props["nom"]             = "str|maxLength|50|notNull";
  }
  
  function loadRefsFwd() {
    $this->_ref_categorie = new CEiCategorie;
    $this->_ref_categorie->load($this->ei_categorie_id);
  }
}
?>