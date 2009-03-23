<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CMomentUnitaire class
 */
class CMomentComplexe extends CMbObject {
  // DB Table key
  var $moment_complexe_id = null;
  
  var $code_moment_id     = null; // Id du code_moment BCB
  var $visible            = null; // Visibilité du moment dans le select
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'moment_complexe';
    $spec->key   = 'moment_complexe_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["code_moment_id"] = "num notNull";
    $specs["visible"]        = "bool";
    return $specs;
  }
 
  // Chargement des moments complexes visibles
  static function loadAllMomentsComplexesVisible(){
    $moment = new CMomentComplexe();
    $moment->visible = "1";
    $moments = $moment->loadMatchingList();
    return $moments;
  }
  
  function updateFormFields(){
  	parent::updateFormFields();
  	$this->loadRefMomentBcb();
  	$this->_view = $this->_ref_moment_bcb->libelle_moment;
  }
  
  function loadRefMomentBcb(){
  	$this->_ref_moment_bcb = new CBcbMoment();
  	$this->_ref_moment_bcb->load($this->code_moment_id);
  }
}
  
?>