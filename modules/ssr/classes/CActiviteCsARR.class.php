<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Activite CsARR
 */
class CActiviteCsARR extends CCsARRObject {
  var $code          = null;
  var $hierarchie    = null;
  var $libelle       = null;
  var $libelle_court = null;
  var $ordre         = null;
  
  var $_ref_hierachie = null;
    
  static $cached = array();
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'activite';
    $spec->key   = 'code';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code"]          = "str notNull length|7 seekable show|0";
    $props["hierarchie"]    = "str notNull maxLength|12 seekable show|0";
    $props["libelle"]       = "str notNull seekable";
    $props["libelle_court"] = "str notNull seekable show|0";
    $props["ordre"]         = "num max|100";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->code;
    $this->_shortview = $this->code;
  }
  
  function loadRefHierarchie() {
    return $this->_ref_type_activite = CHierarchieCdARR::get($this->hierarchie);
  }
	
	function loadView(){
    parent::loadView();
    $this->loadRefHierarchie();
  }

	function loadRefsElements() {
		$element = new CElementPrescriptionToCsarr();
		$element->code = $this->code;
		return $this->_ref_elements = $element->loadMatchingList();
	}
	
	function loadRefsElementsByCat() {
		foreach ($this->loadRefsElements() as $_element){
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