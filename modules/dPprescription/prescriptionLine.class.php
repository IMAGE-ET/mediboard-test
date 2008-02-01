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
class CPrescriptionLine extends CMbObject {
  // DB Table key
  var $prescription_line_id = null;
  
  // DB Fields
  var $prescription_id = null;
  var $code_cip        = null;
  var $no_poso         = null;
  
  // Object References
  var $_ref_prescription = null;
  var $_ref_produit      = null;
  
  // Alertes
var $_ref_alertes = null;
var $_ref_alertes_text = null;
  
  function CPrescriptionLine() {
    $this->CMbObject("prescription_line", "prescription_line_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "prescription_id" => "notNull ref class|CPrescription",
      "code_cip"        => "notNull numchar|7",
      "no_poso"         => "num max|128",
    );
  }
  
  function getSeeks() {
    return array (
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_produit->libelle;
  }
  
  function loadRefsFwd() {
    $this->_ref_prescription = new CPrescription();
    $this->_ref_prescription->load($this->prescription_id);
    $this->_ref_produit = new CBcbProduit();
    $this->_ref_produit->load($this->code_cip);
  }
  
  function checkAllergies($listAllergies) {
    $this->_ref_alertes["allergie"] = array();
    foreach($listAllergies as $key => $all) {
      if($all->CIP == $this->code_cip) {
        $this->_ref_alertes["allergie"][$key]      = $all;
        $this->_ref_alertes_text["allergie"][$key] = $all->LibelleAllergie;
      }
    }
  }
  
  function checkInteractions($listInteractions) {
    $this->_ref_alertes["interaction"] = array();
    foreach($listInteractions as $key => $int) {
      if($int->CIP1 == $this->code_cip) {
        $this->_ref_alertes["interaction"][$key]      = $int;
        $this->_ref_alertes_text["interaction"][$key] = $int->Type;
      }
    }
  }
  
  function checkIPC($listIPC) {
    $this->_ref_alertes["IPC"]      = array();
    $this->_ref_alertes_text["IPC"] = array();
  }
  
  function checkProfil($listProfil) {
    $this->_ref_alertes["profil"] = array();
    foreach($listProfil as $key => $pro) {
      if($pro->CIP == $this->code_cip) {
        $this->_ref_alertes["profil"][$key]      = $pro;
        $this->_ref_alertes_text["profil"][$key] = $pro->LibelleMot;
      }
    }
  }
  
}

?>