<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 6148 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Ligne d'activités RHS
 */
class CLigneActivitesRHS extends CMbObject {  
  // DB Table key
  var $ligne_id = null;
  
  // DB Fields
  var $rhs_id = null;
  var $executant_id = null;
  var $code_activite_cdarr = null;
	var $code_intervenant_cdarr = null;
  var $qty_mon = null;
  var $qty_tue = null;
  var $qty_wed = null;
  var $qty_thu = null;
  var $qty_fri = null;
  var $qty_sat = null;
  var $qty_sun = null;

  // Form fields
	var $_qty_total = null;
	
	// Distant fields

	// References
	var $_ref_rhs = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ligne_activites_rhs';
    $spec->key   = 'ligne_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["rhs_id"] = "ref notNull class|CRHS";
    $props["executant_id"]           = "ref notNull class|CRHS";
    $props["code_activite_cdarr"]    = "str length|4";
    $props["code_intervenant_cdarr"] = "str length|2";
    $props["qty_mon"] = "num length|1";
    $props["qty_tue"] = "num length|1";
    $props["qty_wed"] = "num length|1";
    $props["qty_thu"] = "num length|1";
    $props["qty_fri"] = "num length|1";
    $props["qty_sat"] = "num length|1";
    $props["qty_sun"] = "num length|1";

    // Form fields
    $props["_qty_total"] = "num min|0 max|99";
  
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = CActiviteCdARR::get();
  }
	
	function loadRefRHS() {
		$this->_ref_rhs = $this->loadFwdRef("rhs_id");
	}
}

?>