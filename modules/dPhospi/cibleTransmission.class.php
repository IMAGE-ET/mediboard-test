<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
 *  @author Romain Ollivier
*/

/**
 * 
 * Classe CCibleTransmission. 
 * @abstract Liste des cibles des transmissions médicales
 */

class CCibleTransmission extends CMbObject {

  // DB Table key
  var $cible_transmission_id = null;

  // DB Foreign keys
  var $categorie_cible_transmission_id = null;
  
  // DB Fields
  var $libelle     = null;
  var $description = null;
  
  // References
  var $_ref_categorie_cible_transmission = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cible_transmission';
    $spec->key   = 'cible_transmission_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
  	$specs["categorie_cible_transmission_id"] = "ref notNull class|CCategorieCibleTransmission";
    $specs["libelle"]     = "str notNull";
    $specs["description"] = "text";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->_ref_categorie_cible_transmission = new CCategorieCibleTransmission();
    $this->_ref_categorie_cible_transmission->load($this->categorie_cible_transmission_id);
  }
  
}

?>