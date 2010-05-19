<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineMixVariation extends CMbObject {
  // DB Table key
  var $prescription_line_mix_variation_id = null;
  
  // DB Fields
  var $prescription_line_mix_id  = null; // Perfusion
  var $debit         = null; // Debit
	var $dateTime      = null; // DateTime de la variation 

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_mix_variation';
    $spec->key   = 'prescription_line_mix_variation_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["prescription_line_mix_id"] = "ref notNull class|CPrescriptionLineMix";
		$specs["debit"]        = "num notNull";
		$specs["dateTime"]     = "dateTime notNull";
    return $specs;
  }
	
	function loadRefPerfusion(){
		$this->_ref_prescription_line_mix = new CPrescriptionLineMix();
		$this->_ref_prescription_line_mix = $this->_ref_prescription_line_mix->getCached($this->prescription_line_mix_id);
	}
	
	function check(){
		$this->completeField("prescription_line_mix_id");
		$this->loadRefPerfusion();
		$this->_ref_prescription_line_mix->loadRefsVariations();
		
		$last_variation = $this->_ref_prescription_line_mix->_last_variation;
		if(!$this->_id && $last_variation->_id && $last_variation->debit == $this->debit){
			return "Le débit de la perfusion n'a pas été modifié";
		}
		
		if($this->dateTime < $this->_ref_prescription_line_mix->_debut || $this->dateTime > $this->_ref_prescription_line_mix->_fin){
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
	  
		$this->completeField("prescription_line_mix_id");
    // recalcul des planifs prevues en fonction de la modification du debit
    $this->loadRefPerfusion();
    $this->_ref_prescription_line_mix->loadRefPrescription();
    if($this->_ref_prescription_line_mix->_ref_prescription->type == "sejour"){
      $this->_ref_prescription_line_mix->removePlanifSysteme();
      if($this->_ref_prescription_line_mix->substitution_active && (!$this->_ref_prescription_line_mix->conditionnel || ($this->_ref_prescription_line_mix->conditionnel && $this->_ref_prescription_line_mix->condition_active))){
        $this->_ref_prescription_line_mix->calculPlanifsPerf();
      }
    }
	}
	
	function delete(){
		$this->completeField("prescription_line_mix_id");
    $this->loadRefPerfusion();
		$this->_ref_prescription_line_mix->loadRefPrescription();
		$this->_ref_prescription_line_mix->removePlanifSysteme();
		
		if($msg = parent::delete()){
			return $msg;
		}
		
		if($this->_ref_prescription_line_mix->_ref_prescription->type == "sejour"){
      $this->_ref_prescription_line_mix->removePlanifSysteme();
      if($this->_ref_prescription_line_mix->substitution_active && (!$this->_ref_prescription_line_mix->conditionnel || ($this->_ref_prescription_line_mix->conditionnel && $this->_ref_prescription_line_mix->condition_active))){
        $this->_ref_prescription_line_mix->calculPlanifsPerf();
			}
    }
	}
}
  
?>