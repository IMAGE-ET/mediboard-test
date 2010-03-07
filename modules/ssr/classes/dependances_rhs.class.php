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
class CDependancesRHS extends CMbObject {  
  // DB Table key
  var $dependances_id = null;
  
  // DB Fields
  var $rhs_id = null;
	
	var $habillage    = null;
	var $deplacement  = null;
	var $alimentation = null;
	var $continence   = null;
	var $comportement = null;
	var $relation     = null;

  // Form fields
	
	// Distant fields

	// References
	var $_ref_rhs = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dependances_rhs';
    $spec->key   = 'dependances_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["rhs_id"] = "ref notNull class|CRHS";

		$degre = "enum list|1|2|3|4";
    $props["habillage"]    = $degre;
    $props["deplacement"]  = $degre;
    $props["alimentation"] = $degre;
    $props["continence"]   = $degre;
    $props["comportement"] = $degre;
    $props["relation"]     = $degre;

    return $props;
  }
  	
	function loadRefRHS() {
		$this->_ref_rhs = $this->loadFwdRef("rhs_id");
	}
}

?>