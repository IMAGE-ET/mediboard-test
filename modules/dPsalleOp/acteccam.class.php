<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Thomas Despoix
 */

/**
 * Classe servant à gérer les enregistrements des actes CCAM pendant les
 * interventions
 */
class CActeCCAM extends CMbObject {
  // DB Table key
	var $acte_id = null;

  // DB References
  var $operation_id = null;
  var $executant_id = null;

  // DB Fields
  var $code_acte           = null;
  var $code_activite       = null;
  var $code_phase          = null;
  var $execution           = null;
  var $modificateurs       = null;
  var $montant_depassement = null;
  var $commentaire         = null;  

  // Form fields
  var $_modificateurs = array();
  
  // Object references
  var $_ref_operation = null;
  var $_ref_executant = null;
  var $_ref_code_ccam = null;

	function CActeCCAM() {
		$this->CMbObject( "acte_ccam", "acte_id" );
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}
  
  function getSpecs() {
    return array (
      "code_acte"           => "notNull|code|ccam",
      "code_activite"       => "notNull|numchar|maxLength|2",
      "code_phase"          => "notNull|numchar|maxLength|2",
      "execution"           => "notNull|dateTime",
      "modificateurs"       => "str|maxLength|4",
      "montant_depassement" => "currency|min|0",
      "commentaire"         => "text",
      "operation_id"        => "notNull|ref",
      "executant_id"        => "notNull|ref"
    );
  }
  
  function getSeeks() {
    return array (
      "code_acte" => "equal"
    );
  }
  
  function check() {
    return parent::check(); 

    // datetime_execution: attention à rester dans la plage de l'opération
  }
   
  function updateFormFields() {
    parent::updateFormFields();
    for ($index = 0; $index < strlen($this->modificateurs); $index++) {
    	$this->_modificateurs[] = $this->modificateurs[$index];
    }
    $this->_view = "$this->code_acte-$this->code_activite-$this->code_phase-$this->modificateurs"; 
  }
  
  function loadRefOperation() {
    $this->_ref_operation = new COperation;
    $this->_ref_operation->load($this->operation_id);
  }
  
  function loadRefExecutant() {
    $this->_ref_executant = new CMediusers;
    $this->_ref_executant->load($this->executant_id);
  }
  
  function loadRefCodeCCAM() {
    $this->_ref_code_ccam = new CCodeCCAM($this->code_acte);
    $this->_ref_code_ccam->load();
  }
   
  function loadRefsFwd() {
    $this->loadRefOperation();
    $this->loadRefExecutant();
    $this->loadRefCodeCCAM();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_operation) {
      $this->loadRefOperation();
    }
    return $this->_ref_operation->getPerm($permType);
  }
}

?>