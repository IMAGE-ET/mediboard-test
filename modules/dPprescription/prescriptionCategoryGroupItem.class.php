<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionCategoryGroupItem extends CMbObject {
  // DB Table key
  var $prescription_category_group_item_id = null;
  
  // DB Fields
  var $prescription_category_group_id = null;
	
  // L'item de categorie cible une categorie ou un type de medicament (med, inj ou perf)
  var $category_prescription_id = null;
  var $type_produit = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_category_group_item';
    $spec->key   = 'prescription_category_group_item_id';
    $spec->xor[] = array("category_prescription_id", "type_produit");
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["prescription_category_group_id"] = "ref class|CPrescriptionCategoryGroup notNull cascade";
    $specs["category_prescription_id"]       = "ref class|CCategoryPrescription";
    $specs["type_produit"]                   = "enum list|med|perf|inj";
    return $specs;
  }
	
	/*
	 * Chargement de la catégorie de prescription
	 */
	function loadRefCategoryPrescription(){
		$categoryPrescription = new CCategoryPrescription();
    $this->_ref_category_prescription = $categoryPrescription->getCached($this->category_prescription_id);
	}
	
	/*
	 * Chargement du groupe de categorie de prescription
	 */
	function loadRefPrescriptionCategoryGroup(){
		$prescriptionCategoryGroup = new CPrescriptionCategoryGroup();
		$this->_ref_prescription_category_group = $prescriptionCategoryGroup->getCached($this->prescription_category_group_id);
	}
	
	/*
	 * LoadRefsFwd
	 */
	function loadRefsFwd(){
		$this->loadRefCategoryPrescription();
		$this->loadRefPrescriptionCategoryGroup();
	}
}

?>