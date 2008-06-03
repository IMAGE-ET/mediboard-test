<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescription class
 */
class CElementPrescription extends CMbObject {
  // DB Table key
  var $element_prescription_id = null;
  
  // DB Fields
  var $category_prescription_id = null;
  var $libelle                  = null;
  var $description              = null;
  
  // FwdRefs
  var $_ref_category_prescription = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'element_prescription';
    $spec->key   = 'element_prescription_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "category_prescription_id" => "notNull ref class|CCategoryPrescription",
      "libelle"      => "notNull str",
      "description"  => "text"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_view = $this->libelle;
  }
    
  function loadRefCategory(){
  	$this->_ref_category_prescription = new CCategoryPrescription();
  	$this->_ref_category_prescription->load($this->category_prescription_id);	
  }
  
  static function getFavoris($praticien_id, $category) {
    $ds = CSQLDataSource::get("std");
    $sql = "SELECT prescription_line_element.element_prescription_id, COUNT(*) AS total
            FROM prescription_line_element, element_prescription, category_prescription, prescription
            WHERE prescription_line_element.prescription_id = prescription.prescription_id
            AND prescription_line_element.element_prescription_id = element_prescription.element_prescription_id
            AND element_prescription.category_prescription_id = category_prescription.category_prescription_id
            AND category_prescription.chapitre = '$category'
            AND prescription.praticien_id = $praticien_id
            AND prescription.object_id IS NOT NULL
            GROUP BY prescription_line_element.element_prescription_id
            ORDER BY total DESC
            LIMIT 0, 20";
    return $ds->loadlist($sql);
  }
  
  
  function loadRefsFwd(){
  	parent::loadRefsFwd();
  	$this->loadRefCategory();
  }
}

?>