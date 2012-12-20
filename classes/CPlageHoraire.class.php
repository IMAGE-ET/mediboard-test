<?php /* $Id: CPlageconsult.class.php 17537 2012-12-16 21:17:11Z mytto $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 17537 $
* @author Romain Ollivier
*/

class CPlageHoraire extends CMbObject {
  // DB fields
  var $date    = null;
  var $debut   = null;
  var $fin     = null;
  
  // Object references
  var $_colliding_plages = null;
  
  function getProps() {
    $props = parent::getProps();
    $props["date"]  = "date notNull";
    $props["debut"] = "time notNull";
    $props["fin"]   = "time notNull moreThan|debut";
    return $props;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
		
		// Collision keys mandatory definition to determine which references identify collisions
		// Has to be an array, be it empty
    $spec->collision_keys = null;

    return $spec;
  }
	
  /**
   * Check collision with another plage regarding defined in class spec
   *
   * @return string Collision message
   */
  function hasCollisions() {
  	// Check whether mandatory collision keys are defined
		$keys = $this->_spec->collision_keys;
  	if (!is_array($keys)) {
      trigger_error(CAppUI::tr("CPlageHoraire-collision_keys", $this->_class), E_USER_ERROR);
			return;
  	}
		
		$keys = $this->_spec->collision_keys;
    $this->completeField("date", "debut", "fin");
    $this->completeField($keys);		
        
    // Get all other plages the same day
    $where[$this->_spec->key] = "!= '$this->_id'";
    $where["date"]            = "= '$this->date'";
    $where["debut"]           = "< '$this->fin'";
    $where["fin"]             = "> '$this->debut'";
		// Append collision keys clauses
		foreach ($keys as $_key) {
			$where[$_key] = "= '{$this->$_key}'";
		}

		// Load collision
    $plages = new $this->_class;
		CSQLDataSource::$trace = true;
    $this->_colliding_plages = $plages->loadList($where);
    
		// Build collision message
    $msgs = array();
    foreach ($this->_colliding_plages as $_plage) {
      $msgs[] = "Collision avec la plage de '$_plage->debut' � '$_plage->fin'";
    }
    
    return count($msgs) ? implode(", ", $msgs) : null;
  }

  function store() {
  	if ($msg = $this->hasCollisions()) {
  		return $msg;
  	}
    
		return parent::store();
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf(
      "Plage du %s de %s � %s",
      mbTransformTime($this->date , null, CAppUI::conf("date")),
      mbTransformTime($this->debut, null, CAppUI::conf("time")),
      mbTransformTime($this->fin  , null, CAppUI::conf("time"))
    );
  }
  
  function updatePlainFields() {
    // Usefull for automatic plages coming from instant consult in emergency
    if ($this->fin && $this->fin == "00:00:00") {
      $this->fin = "23:59:59";
    }
  }
}
?>