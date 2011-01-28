<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Technicien de SSR, association entre un plateau technique et un utilisateur
 */
class CTechnicien extends CMbObject {
  // DB Table key
  var $technicien_id = null;
  
  // References fields
  var $plateau_id = null;
  var $kine_id    = null;
	
	// DB Fields
	var $actif      = null;
	
  // Derived fields
  var $_count_sejours_date = null;

  // References
  var $_ref_kine    = null;
  var $_ref_plateau = null;
	
	
  // Derived references
  var $_ref_conge_date = null;
  var $_ref_sejours_date = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'technicien';
    $spec->key   = 'technicien_id';
		return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["plateau_id"] = "ref notNull class|CPlateauTechnique";
    $props["kine_id"]    = "ref notNull class|CMediusers";
    $props["actif"]      = "bool notNull default|1";

    $props["_count_sejours_date"] = "num";
		
    return $props;
  }
	
	function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["bilan_ssr"] = "CBilanSSR technicien_id";
    return $backProps;
  }
	
  function loadRefPlateau() {
  	return $this->_ref_plateau = $this->loadFwdRef("plateau_id", true);
  }	
	
	
  function loadRefKine() {
    $this->_ref_kine = $this->loadFwdRef("kine_id", true);
		$this->_ref_kine->loadRefFunction();
    $this->_view = $this->_ref_kine->_view;
		return $this->_ref_kine;
  }
	
	function loadRefCongeDate($date) {
		$this->_ref_conge_date = new CPlageConge;
		$this->_ref_conge_date->loadFor($this->kine_id, $date);
		return $this->_ref_conge_date;
	}
	
	function countSejoursDate($date) {
		$group = CGroups::loadCurrent();
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = "= '$this->_id'";
    return $this->_count_sejours_date = CSejour::countForDate($date, $where, $leftjoin);
	}
	
	function loadRefsSejours($date) {
    $group = CGroups::loadCurrent();
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = "= '$this->_id'";
    return $this->_ref_sejours_date = CSejour::loadListForDate($date, $where, null, null, null, $leftjoin);
	}
	
}

?>