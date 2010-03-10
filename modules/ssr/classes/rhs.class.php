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
  var $sejour_id   = null;
  var $date_monday = null;
  
	// Form Field
	var $_date_tuesday   = null;
	var $_date_wednesday = null;
	var $_date_thursday  = null;
	var $_date_friday    = null;
	var $_date_saturday  = null;
	var $_date_sunday    = null;
	var $_week_number    = null;
	
	// Distant fields
  var $_in_bounds     = null;
  var $_in_bounds_mon = null;
  var $_in_bounds_tue = null;
  var $_in_bounds_wed = null;
  var $_in_bounds_thu = null;
  var $_in_bounds_fri = null;
  var $_in_bounds_sat = null;
  var $_in_bounds_sun = null;
	
  // Object References
  var $_ref_sejour    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "rhs";
    $spec->key   = "rhs_id";
    $spec->uniques["rhs"] = array("sejour_id", "date_monday");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
		
    // DB Fields
    $props["sejour_id"]    = "ref notNull class|CSejour";
    $props["date_monday"]   = "date notNull";

    // Form Field
    $props["_date_tuesday"]   = "date";
    $props["_date_wednesday"] = "date";
    $props["_date_thursday"]  = "date";
    $props["_date_friday"]    = "date";
    $props["_date_saturday"]  = "date";
    $props["_date_sunday"]    = "date";
    $props["_week_number"]    = "num min|0 max|52";

    // Remote fields
    $props["_in_bounds"]     = "bool";
    $props["_in_bounds_mon"] = "bool";
    $props["_in_bounds_tue"] = "bool";
    $props["_in_bounds_wed"] = "bool";
    $props["_in_bounds_thu"] = "bool";
    $props["_in_bounds_fri"] = "bool";
    $props["_in_bounds_sat"] = "bool";
    $props["_in_bounds_sun"] = "bool";
		
    return $props;
  }
 
	function getBackProps() {
	  $backProps = parent::getBackProps();
	  $backProps["lines"] = "CLigneActivitesRHS rhs_id";
	  return $backProps;
	}
  
	function check() {
		return parent::check();
		
		if ($this->date_monday && strftime($this->date_monday != "1")) {
			return CAppUI::tr("CRHS-failed-monday", $this->date_monday);
		}
	}
	
	function updateFormFields() {
		parent::updateFormFields();
		$this->_week_number = mbTransformTime(null, $this->date_monday, "%U");
      
    $this->_date_tuesday   = mbDate("+1 DAY", $this->date_monday);
    $this->_date_wednesday = mbDate("+2 DAY", $this->date_monday);
    $this->_date_thursday  = mbDate("+3 DAY", $this->date_monday);
    $this->_date_friday    = mbDate("+4 DAY", $this->date_monday);
    $this->_date_saturday  = mbDate("+5 DAY", $this->date_monday);
    $this->_date_sunday    = mbDate("+6 DAY", $this->date_monday);
    
		$this->_view = CAppUI::tr("Week") . " $this->_week_number";
	}

  function loadRefSejour() {
  	$this->_ref_sejour = $sejour = $this->loadFwdRef("sejour_id", true);
    
  	$this->_in_bounds = 
		  $this->date_monday <= mbDate(null, $sejour->_sortie) && 
      $this->_date_sunday >= mbDate(null, $sejour->_entree);
      
    $this->_in_bounds_mon = 
		  $this->date_monday <= mbDate($sejour->_sortie) && 
      $this->date_monday >= mbDate($sejour->_entree);
    
    $this->_in_bounds_tue = 
		  $this->_date_tuesday <= mbDate($sejour->_sortie) && 
      $this->_date_tuesday >= mbDate($sejour->_entree);
    
    $this->_in_bounds_wed = 
		  $this->_date_wednesday <= mbDate($sejour->_sortie) && 
      $this->_date_wednesday >= mbDate($sejour->_entree);
    
    $this->_in_bounds_thu = 
		  $this->_date_thursday <= mbDate($sejour->_sortie) && 
      $this->_date_thursday >= mbDate($sejour->_entree);
    
    $this->_in_bounds_fri = 
		  $this->_date_friday <= mbDate($sejour->_sortie) && 
      $this->_date_friday >= mbDate($sejour->_entree);
    
    $this->_in_bounds_sat = 
		  $this->_date_saturday <= mbDate($sejour->_sortie) && 
      $this->_date_saturday >= mbDate($sejour->_entree);
    
    $this->_in_bounds_sun = 
		  $this->_date_sunday <= mbDate($sejour->_sortie) && 
      $this->_date_sunday >= mbDate($sejour->_entree);
  }
	
	/**
	 * Get all possible and existing RHS for given sejour, by date as keys
	 * @param CSejour $sejour
	 * @return array[CRHS], null if not applyable
	 */
	static function getAllRHSsFor(CSejour $sejour) {
		if (!$sejour->_id || $sejour->type != "ssr") {
			return;
		}
			
	  $rhss = array();
    foreach ($sejour->loadBackRefs("rhss") as $_rhs) {
    	$rhss[$_rhs->date_monday] = $_rhs;
    }
    
		for (
		  $date_monday = mbDate("last sunday + 1 day", $sejour->_entree);
			$date_monday <= $sejour->_sortie;
			$date_monday = mbDate("+1 week", $date_monday)
		) {
			if (!isset($rhss[$date_monday])) {
				$rhs = new CRHS;
				$rhs->sejour_id = $sejour->_id;
				$rhs->date_monday = $date_monday;
				$rhs->updateFormFields();
				$rhss[$date_monday] = $rhs;
			}
		}
		
		ksort($rhss);
		
		return $rhss;
	}
}

?>