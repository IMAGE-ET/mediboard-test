<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * 
 * Classe CCategorieCibleTransmission. 
 * @abstract Classification des cibles des transmissions médicales
 */

class CCategorieCibleTransmission extends CMbObject {

  // DB Table key
  var $categorie_cible_transmission_id = null;	
  
  // DB Fields
  var $libelle     = null;
  var $description = null;
  
  // References
  var $_ref_cibles_transmission = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'categorie_cible_transmission';
    $spec->key   = 'categorie_cible_transmission_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["libelle"]     = "str notNull";
    $specs["description"] = "text";
    return $specs;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["cibles"]  = "CCibleTransmission categorie_cible_transmission_id";
    return $backRefs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
}

?>