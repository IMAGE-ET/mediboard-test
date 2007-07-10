<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Alexis / Yohann	
 */

/**
 * The CFacture class
 */
class CFacture extends CMbObject {
  // DB Table key
  var $facture_id = null;
  
  // DB Fields
  var $date        = null;
  var $sejour_id = null;
  var $prix = null;
  
  // Distan fields
  var $_total = null;
  
  // Object References
  var $_ref_sejour = null;
  var $_ref_items  = null;
  
  function CFacture() {
    $this->CMbObject("facture", "facture_id"); 
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["items"] = "CFactureItem facture_id";
     return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "date"         => "notNull date",
      "prix"		 => "currency",
      "sejour_id"    => "notNull ref class|CSejour",
      "_total"		 => "currency",
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Facture du ".$this->date;
  }
  
  function loadRefsBack(){
	$item =  new CFactureItem;
	$item->facture_id = $this->_id;
	$this->_ref_items = $item->loadMatchingList();
	$this->_total = 0;
	foreach($this->_ref_items as $_item) {
		$this->_total += $_item->_ttc;
	}
  } 
  
  function loadRefsFwd(){ 
	$this->_ref_sejour = new CSejour;
	$this->_ref_sejour->load($this->sejour_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_sejour) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_sejour->getPerm($permType));
  }
  
}
?>