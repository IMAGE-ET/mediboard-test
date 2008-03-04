<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescription extends CMbObject {
  // DB Table key
  var $prescription_id = null;
  
  // DB Fields
  var $praticien_id = null;
  var $object_class = null;
  var $object_id    = null;
  var $libelle      = null;
  
  // Object References
  var $_ref_object  = null;
  var $_ref_patient = null;
  
  // BackRefs
  var $_ref_prescription_lines = null;
  var $_ref_prescription_lines_element_by_cat = null;
  
  function CPrescription() {
    $this->CMbObject("prescription", "prescription_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_line"] = "CPrescriptionLine prescription_id";
    return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "praticien_id"  => "notNull ref class|CMediusers",
      "object_id"     => "ref class|CCodable meta|object_class",
      "object_class"  => "notNull enum list|CSejour|CConsultation",
      "libelle"       => "str"
    );
  }
  
  function getSeeks() {
    return array (
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    //$this->_view = "Prescription: ".$this->libelle;
    $this->_view = "Prescription du Dr. ".$this->_ref_praticien->_view." : ".$this->_ref_object->_view;
    if($this->libelle){
    	$this->_view .= "($this->libelle)";
    }
  
    if(!$this->object_id){
    	$this->_view = "Protocole: ".$this->libelle;
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);
    $this->_ref_object = new $this->object_class();
    $this->_ref_object->load($this->object_id);
    $this->_ref_patient = new CPatient();
    $this->_ref_patient->load($this->_ref_object->patient_id);
  }
  
  function loadRefsLines() {
    $line = new CPrescriptionLine();
    $where = array("prescription_id" => "= $this->_id");
    $order = "prescription_line_id";
    $this->_ref_prescription_lines = $line->loadList($where, $order);
  }
  
  function loadRefsLinesElement(){
  	$line = new CPrescriptionLineElement();
    $where = array("prescription_id" => "= $this->_id");
    $order = "prescription_line_element_id";
    $this->_ref_prescription_lines_element = $line->loadList($where, $order);
    foreach($this->_ref_prescription_lines_element as &$line_element){
    	$line_element->loadRefElement();
    }
  }
  
  function loadRefsLinesElementByCat(){
  	$this->loadRefsLinesElement();
  	$this->_ref_prescription_lines_element_by_cat = array();
  	
  	foreach($this->_ref_prescription_lines_element as $line){
  		$category = new CCategoryPrescription();
  		$category->load($line->_ref_element_prescription->category_prescription_id);
  	  $this->_ref_prescription_lines_element_by_cat[$category->chapitre][] = $line;	
  	}
  	//if($this->_ref_prescription_lines_elemnent_by_cat){
  	  ksort($this->_ref_prescription_lines_element_by_cat);
  	//}
  }
  
  static function getFavorisPraticien($praticien_id){
  	$favoris = array();
  	$listFavoris = array();
  	$listFavoris["medicament"] = array();
  	$listFavoris["dmi"] = array();
  	$listFavoris["imagerie"] = array();
  	$listFavoris["consult"] = array();
  	$listFavoris["kine"] = array();
  	$listFavoris["soin"] = array();
  	$listFavoris["labo"] = array();
  	
  	$favoris["medicament"] = CBcbProduit::getFavoris($praticien_id);
	  $favoris["dmi"] = CElementPrescription::getFavoris($praticien_id, "dmi");
	  $favoris["labo"] = CElementPrescription::getFavoris($praticien_id, "labo");
	  $favoris["imagerie"] = CElementPrescription::getFavoris($praticien_id, "imagerie");
	  $favoris["consult"] = CElementPrescription::getFavoris($praticien_id, "consult");
	  $favoris["kine"] = CElementPrescription::getFavoris($praticien_id, "kine");
	  $favoris["soin"] = CElementPrescription::getFavoris($praticien_id, "soin");
	  
	  foreach($favoris as $key => $typeFavoris) {
	  	foreach($typeFavoris as $curr_fav){
	  		if($key == "medicament"){
	  		  $produit = new CBcbProduit();
	        $produit->load($curr_fav["code_cip"]);
	        $listFavoris["medicament"][] = $produit;
	  		} else {
	  			$element = new CElementPrescription();
	  			$element->load($curr_fav["element_prescription_id"]);
	  			$listFavoris[$key][] = $element;
	  		}
	  	}
	  }
	  return $listFavoris;  	
  }
  
}

?>