<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

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
	
	static $cached = array();
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'activite';
    $spec->key         = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]    = "str notNull length|4 seekable show|0";
    $props["type"]    = "str notNull length|2 seekable show|0";
    $props["libelle"] = "str notNull maxLength|250 seekable show|1";
    $props["note"]    = "text";
    $props["inclu"]   = "text";
    $props["exclu"]   = "text";
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }
  
  function loadRefTypeActivite() {
    return $this->_ref_type_activite = CTypeActiviteCdARR::get($this->type);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefTypeActivite();
  }

	function loadRefsElements(){
		$element_to_cdarr = new CElementPrescriptionToCdarr();
		$element_to_cdarr->code = $this->code;
		return $this->_ref_elements = $element_to_cdarr->loadMatchingList();
	}
	
	function loadRefsElementsByCat() {
		foreach ($this->loadRefsElements() as $_element){
      $_element->loadRefElementPrescription();
      $this->_ref_elements_by_cat[$_element->_ref_element_prescription->category_prescription_id][] = $_element;
    }
	}
	
	static function getLibelle($type) {
	  $found = new self();
	  $found->type = $type;
	  $found->loadMatchingObject();
	  
	  return $found->libelle;
	}
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR
	 **/
  static function get($code) {
  	if (!$code) {
  	  return new self(); 
  	}
  	
    if (!isset(self::$cached[$code])) {
      $activite = new self();
      $activite->load($code);
      self::$cached[$code] = $activite;
    }
    
    return self::$cached[$code];
  }
}

?>