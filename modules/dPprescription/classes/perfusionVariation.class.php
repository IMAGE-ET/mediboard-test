<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPerfusionVariation extends CMbObject {
  // DB Table key
  var $perfusion_variation_id = null;
  
  // DB Fields
  var $perfusion_id  = null; // Perfusion
  var $debit         = null; // Debit
	var $dateTime      = null; // DateTime de la variation 

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'perfusion_variation';
    $spec->key   = 'perfusion_variation_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["perfusion_id"] = "ref notNull class|CPerfusion";
		$specs["debit"]        = "num notNull";
		$specs["dateTime"]     = "dateTime notNull";
    return $specs;
  }
	
	function loadRefPerfusion(){
		$this->_ref_perfusion = new CPerfusion();
		$this->_ref_perfusion = $this->_ref_perfusion->getCached($this->perfusion_id);
	}
	
	function check(){
		$this->completeField("perfusion_id");
		$this->loadRefPerfusion();
		$this->_ref_perfusion->loadRefsVariations();
		
		$last_variation = $this->_ref_perfusion->_last_variation;
		if(!$this->_id && $last_variation->_id && $last_variation->debit == $this->debit){
			return "Le débit de la perfusion n'a pas été modifié";
		}
		
		if($this->dateTime < $this->_ref_perfusion->_debut || $this->dateTime > $this->_ref_perfusion->_fin){
			return "Impossible de faire varier le debit en dehors des heures de prescription de la perfusion";
		}
		return parent::check();
	}
	
	function updateDBFields(){
		parent::updateDBFields();
		if($this->dateTime == "current"){
			$this->dateTime = mbDateTime();
		}
	}
	
	function store(){
		if($msg = parent::store()){
			return $msg;
		}
	  
		$this->completeField("perfusion_id");
    // recalcul des planifs prevues en fonction de la modification du debit
    $this->loadRefPerfusion();
    $this->_ref_perfusion->loadRefPrescription();
    if($this->_ref_perfusion->_ref_prescription->type == "sejour"){
      $this->_ref_perfusion->removePlanifSysteme();
      if($this->_ref_perfusion->substitution_active && (!$this->_ref_perfusion->conditionnel || ($this->_ref_perfusion->conditionnel && $this->_ref_perfusion->condition_active))){
        $this->_ref_perfusion->calculPlanifsPerf();
      }
    }
	}
	
	function delete(){
		$this->completeField("perfusion_id");
    $this->loadRefPerfusion();
		$this->_ref_perfusion->loadRefPrescription();
		$this->_ref_perfusion->removePlanifSysteme();
		
		if($msg = parent::delete()){
			return $msg;
		}
		
		if($this->_ref_perfusion->_ref_prescription->type == "sejour"){
      $this->_ref_perfusion->removePlanifSysteme();
      if($this->_ref_perfusion->substitution_active && (!$this->_ref_perfusion->conditionnel || ($this->_ref_perfusion->conditionnel && $this->_ref_perfusion->condition_active))){
        $this->_ref_perfusion->calculPlanifsPerf();
			}
    }
	}
}
  
?>