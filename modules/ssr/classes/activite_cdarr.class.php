<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("ssr", "cdarrObject");

/**
 * Activité CdARR
 */
class CActiviteCdARR extends CCdARRObject {
  var $code    = null;
  var $type    = null;
	var $libelle = null;
	var $note    = null;
	var $inclu   = null;
	var $exclu   = null;
	
	var $_ref_type_activite = null;
	var $_ref_elements = null;
	var $_ref_elements_by_cat = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'activite';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|4 seekable";
    $props["type"]    = "str notNull length|2 seekable";
    $props["libelle"] = "str notNull maxLength|250 seekable";
    $props["note"]    = "text";
    $props["inclu"]   = "text";
    $props["exclu"]   = "text";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code . " : " . $this->libelle;
    $this->_shortview = $this->code;
  }
  
  function loadRefTypeActivite() {
    $this->_ref_type_activite = new CTypeActiviteCdARR();
    $this->_ref_type_activite->load($this->type);
  }
	
	function loadRefsElements(){
		$element_to_cdarr = new CElementPrescriptionToCdarr();
		$element_to_cdarr->code = $this->code;
		$this->_ref_elements = $element_to_cdarr->loadMatchingList();
	}
	
	function loadRefsElementsByCat(){
		$this->loadRefsElements();
		foreach($this->_ref_elements as $_element){
      $_element->loadRefElementPrescription();
      $this->_ref_elements_by_cat[$_element->_ref_element_prescription->category_prescription_id][] = $_element;
    }
	}
	
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR
	 **/
	static function get($code) {
		$found = new CActiviteCdARR();
    $found->load($code);
		return $found;
	}
}

?>