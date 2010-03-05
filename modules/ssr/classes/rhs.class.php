<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Résumé Hébdomadaire Simplifié
 * Correspond à une cotation d'actes de réadaptation pour une semaine
 */
class CRHS extends CMbObject {
  // DB Table key
  var $rhs_id = null;
 
  // DB Fields
  var $sejour_id  = null;
  var $date_monday = null;
  
	// Form Field
	var $_semaine = null;
	
  // Object References
  var $_ref_sejour    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'rhs';
    $spec->key   = 'rhs_id';
    $spec->uniques["rhs"] = array("sejour_id", "date_monday");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]    = "ref notNull class|CSejour";
    $specs["date_monday"]   = "date notNull";

    return $specs;
  }
  
	function check() {
		return parent::check();
		
		if ($this->date_monday && strftime($this->date_monday != "1")) {
			return CAppUI::tr("CRHS-check-monday", $this->date_monday);
		}
	}
	
	/**
	 * Make an empty RHS for sejour 
	 * @param CSejour $sejour
	 * @return CRHS, null if not applyable, with an empty date_monday if not available
	 */
	function getNextAvailableRHS(CSejour $sejour) {
		if (!$sejour->_id || $sejour->type != "ssr") {
			return;
		}
				
		$rhss = $sejour->loadBackRefs("rhss");
		$max_monday = max(CMbArray::pluck($rhss, "date_monday"));
		
		$rhs = new CRHS;
		$rhs->sejour_id = $sejour->_id;
		$rhs->date_monday = mbDate("last monday", $sejour->_entree);
		while ($rhs->countMultipleList()) {
			if ($rhs->date_monday > $sejour->_sortie) {
				$rhs->date_monday = "";
				break;
			}
			$rhs->date_monday = mbdate("+1 week", $rhs->date_monday);
		}
		
		return $rhs;
	}
}

?>