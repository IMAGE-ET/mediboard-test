<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */


/**
 * The CCategorieDoc class
 */
class CCategorieDoc extends CMbObject {
  // DB Table key
  var $doc_categorie_id = null;
    
  // DB Fields
  var $nom  = null;
  var $code = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'doc_categories';
    $spec->key   = 'doc_categorie_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["documents_ged"] = "CDocGed doc_categorie_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["nom"]  = "notNull str maxLength|50";
    $specs["code"] = "notNull str maxLength|1";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code ." - " . $this->nom;
    $this->_shortview = $this->code; 
  }  
}
?>