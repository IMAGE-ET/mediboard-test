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
  
   // Form fields
   var $_total = null;
   
  // Object References
  var $_ref_sejour = null;
  var $_ref_items  = null;
  
  function CFacture() {
    $this->CMbObject("facture", "facture_id"); 
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "date"         => "notNull date",
      "sejour_id"    => "notNull ref class|CSejour"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Facture du ".$this->date;
  }
  
  function loadRefsBack(){
    $this->_ref_items = new CFactureItem;
    $where = array();
    $where["facture_id"] = "= '$this->facture_id'";
    $this->_ref_items = $this->_ref_items->loadList($where);
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
  
  function canDelete(&$msg, $oid = null) {
    $tables[] = array (
      "label"     => "élément(s) de facture", 
      "name"      => "factureitem", 
      "idfield"   => "facture_item_id", 
      "joinfield" => "facture_id"
    );
    
    return CMbObject::canDelete( $msg, $oid, $tables );
  }
}
?>