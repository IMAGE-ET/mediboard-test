<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CElementPrescriptionToCdarr extends CMbObject {
  // DB Table key
  var $element_prescription_to_cdarr_id = null;
	
  // DB Fields
	var $element_prescription_id = null;
	var $code = null;
	var $commentaire = null;
	
	var $_ref_element_prescription = null;
	var $_ref_activite_cdarr = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'element_prescription_to_cdarr';
    $spec->key   = 'element_prescription_to_cdarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["element_prescription_id"] = "ref notNull class|CElementPrescription";
    $props["code"]                    = "str notNull length|4";
    $props["commentaire"]             = "str";
    return $props;
  }
	
	function loadRefElementPrescription(){
	  $element = new CElementPrescription();
    $this->_ref_element_prescription = $element->getCached($this->element_prescription_id);
  }
	
	function check(){
		// Verification du code Cdarr saisi
		$code_cdarr = CActiviteCdARR::get($this->code);
		if(!$code_cdarr->code){
			return "Ce code n'est pas un code Cdarr valide";
		}
		return parent::check();
	}
	
	function updateFormFields(){
		parent::updateFormFields();
		$this->_view = "Code CdARR ".$this->code;
	}
	
	function loadRefActiviteCdarr(){
    $this->_ref_activite_cdarr = CActiviteCdARR::get($this->code);
    $this->_ref_activite_cdarr->loadRefTypeActivite();
	}
	
	function loadView(){
		parent::loadView();
    $this->loadRefActiviteCdarr();
	}
}

?>