<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CEvenementSSR extends CMbObject {
  // DB Table key
	var $evenement_ssr_id        = null;
	
	// DB Fields
	var $element_prescription_id = null;
	var $code                    = null; // Code Cdarr
	var $sejour_id               = null;
	var $debut                   = null; // DateTime
	var $duree                   = null; // Durée en minutes
	var $therapeute_id           = null;
	var $equipement_id           = null;
  var $realise                 = null;
	
	var $_heure                  = null;
	var $_ref_element_prescription = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table       = 'evenement_ssr';
    $spec->key         = 'evenement_ssr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["element_prescription_id"] = "ref notNull class|CElementPrescription";
    $props["code"]          = "str notNull length|4";
    $props["sejour_id"]     = "ref notNull class|CSejour";
    $props["debut"]         = "dateTime notNull";
    $props["duree"]         = "num notNull min|0";
		$props["therapeute_id"] = "ref notNull class|CMediusers";
		$props["equipement_id"] = "ref class|CEquipement";
		$props["realise"]       = "bool default|0";
		$props["_heure"]        = "time";
    return $props;
  }
	
  function loadRefElementPrescription() {
    $this->_ref_element_prescription = new CElementPrescription();
    $this->_ref_element_prescription = $this->_ref_element_prescription->getCached($this->element_prescription_id); 
  }
	
	function loadRefSejour(){
		$this->_ref_sejour = new CSejour();
		$this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
	}
}

?>