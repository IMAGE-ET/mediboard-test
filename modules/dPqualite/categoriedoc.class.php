<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["documents_ged"] = "CDocGed doc_categorie_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["nom"]  = "str notNull maxLength|50";
    $specs["code"] = "str notNull maxLength|1";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code - $this->nom";
    $this->_shortview = $this->code; 
  }
}
?>