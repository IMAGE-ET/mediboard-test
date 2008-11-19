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
class CCategoryPrescription extends CMbObject {
  // DB Table key
  var $category_prescription_id = null;
  
  // DB Fields
  var $chapitre    = null;
  var $nom         = null;
  var $description = null;
  var $header      = null;
  
  // BackRefs
  var $_ref_elements_prescription = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'category_prescription';
    $spec->key   = 'category_prescription_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["chapitre"]    = "notNull enum list|dmi|anapath|biologie|imagerie|consult|kine|soin|dm";
    $specs["nom"]         = "notNull str";
    $specs["description"] = "text";
    $specs["header"]      = "text";
    return $specs;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["elements_prescription"]   = "CElementPrescription category_prescription_id";
    $backRefs["executants_prescription"] = "CExecutantPrescriptionLine category_prescription_id";
    $backRefs["functions_category"]      = "CFunctionCategoryPrescription category_prescription_id";
    $backRefs["comments_prescription"]   = "CPrescriptionLineComment category_prescription_id";
    return $backRefs;
  }     
  
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->_view = $this->nom;
  }
  
  function loadElementsPrescription() {
    $this->_ref_elements_prescription = $this->loadBackRefs("elements_prescription","libelle");
  }
  
  /**
   * Charge toutes les categories triées par chapitre
   * @param $chapitre string Permet de restreindre à un seul chapitre
   * @return array[CCategoryPrescription] Les catégories
   */
  static function loadCategoriesByChap($chapitre = null) {
		$categorie = new CCategoryPrescription;
		$categorie->chapitre = $chapitre;
		
		// Initialisation des chapitres
		$chapitres = explode("|", $categorie->_specs["chapitre"]->list);
		$categories_par_chapitre = array();
		foreach ($chapitres as $chapitre) {
		  $categories_par_chapitre[$chapitre] = array();
		}
		
		// Chargement et classement par chapitre
    $order = "nom";
    $categories = $categorie->loadMatchingList($order);
    foreach ($categories as &$categorie) {
		  $categories_par_chapitre[$categorie->chapitre]["$categorie->_id"] =& $categorie;
		} 

  	ksort($categories_par_chapitre);
  	return $categories_par_chapitre;
  }
}

?>