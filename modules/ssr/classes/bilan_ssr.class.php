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
  var $sejour_id     = null;
  var $technicien_id = null;
	var $entree        = null;
  var $sortie        = null;
  var $planification = null;
  var $brancardage   = null;
	
  // References
  var $_ref_technicien = null;

  // Distant Fields
  var $_kine_referent_id = null;
  var $_kine_journee_id  = null;

  // Distant references
  var $_ref_kine_referent = null;
  var $_ref_kine_journee  = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "bilan_ssr";
    $spec->key   = "bilan_id";
    $spec->uniques["sejour_id"] = array("sejour_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"    ] = "ref notNull class|CSejour show|0";
		$specs["technicien_id"] = "ref class|CTechnicien";
    $specs["entree"       ] = "text helped";
    $specs["sortie"       ] = "text helped";
    $specs["planification"] = "bool default|1";
    $specs["brancardage"  ] = "bool default|0";

    $specs["_kine_referent_id"]   = "ref class|CMediusers";
    $specs["_kine_journee_id" ]   = "ref class|CMediusers";
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
	
	function loadRefTechnicien() {
		$this->_ref_technicien = $this->loadFwdRef("technicien_id");
	}
	
	function loadRefKineReferent() {
    $this->loadRefTechnicien();
    $technicien =& $this->_ref_technicien;
    $technicien->loadRefKine();
    $this->_ref_kine_referent = $technicien->_ref_kine;
		$this->_kine_referent_id = $this->_ref_kine_referent->_id;
	}
	
	/**
	 * Kine référent et kiné journée pour une date donné
	 * @param date $date Date courante if null;
	 * @return void
	 */
	function loadRefKineJournee($date = null) {
    $this->loadRefKineReferent();
    $this->_ref_kine_journee = $this->_ref_kine_referent;

    // Recherche d'un remplacement
		$sejour = $this->loadFwdRef("sejour_id", true);
		$sejour->loadRefReplacement();
    $replacement =& $sejour->_ref_replacement;
		if ($replacement->_id) {
			$replacement->loadRefConge();
			$conge = $replacement->_ref_conge;
			if (in_range(CValue::first($date, mbDate()), $conge->date_debut, $conge->date_fin)) {
        $replacement->loadRefReplacer();
				$replacer =& $replacement->_ref_replacer;
				$replacer->loadRefFunction();
				$this->_ref_kine_journee = $replacement->_ref_replacer;
			}
	  }
		
		$this->_kine_journee_id = $this->_ref_kine_journee->_id;
	}
}

?>