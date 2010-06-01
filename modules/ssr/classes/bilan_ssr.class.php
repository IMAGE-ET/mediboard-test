<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Bilan d'entrée SSR
 */
class CBilanSSR extends CMbObject {
  // DB Table key
  var $bilan_id = null;
  
  // DB Fields
  var $sejour_id = null;
  var $technicien_id = null;
	var $entree = null;
  var $sortie = null;
	var $brancardage = null;
	
  var $_activites = array();
	
  // References
  var $_ref_technicien = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "bilan_ssr";
    $spec->key   = "bilan_id";
    $spec->uniques["sejour_id"] = array("sejour_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"] = "ref notNull class|CSejour show|0";
		$specs["technicien_id"]   = "ref class|CTechnicien";
    $specs["entree"] = "text helped";
    $specs["sortie"] = "text helped";
		$specs["brancardage"] = "bool default|0";
    return $specs;
  }
	
	/**
	 * Load Sejour for technicien at a date
	 **/ 
	static function loadSejoursSSRfor($technicien_id, $date) {
		$group = CGroups::loadCurrent();
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
		$where["bilan_ssr.technicien_id"] = $technicien_id ? "= '$technicien_id'" : "IS NULL";
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
		return CSejour::loadListForDate($date, $where, "entree_reelle", null, null, $leftjoin);
	}
	
	function loadRefTechnicien(){
		$this->_ref_technicien = $this->loadFwdRef("technicien_id");
	}
}

?>