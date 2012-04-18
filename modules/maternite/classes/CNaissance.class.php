<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CNaissance extends CMbObject {
  // DB Table key
  var $naissance_id     = null;

  // DB References
  var $sejour_maman_id  = null;
  var $sejour_enfant_id = null;
  var $operation_id     = null;
  var $grossesse_id     = null;
  
  // DB Fields
  var $hors_etab        = null;
  var $heure            = null;
  var $rang             = null;
  
  // DB References
  var $_ref_operation   = null;
  var $_ref_grossesse   = null;
  var $_ref_sejour_enfant = null;
  var $_ref_sejour_maman  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'naissance';
    $spec->key   = 'naissance_id';
    return $spec;
  }
  
  function getProps() {
  	$props = parent::getProps();
    $props["operation_id"]     = "ref class|COperation";
    $props["grossesse_id"]     = "ref class|CGrossesse";
    $props["sejour_maman_id" ] = "ref notNull class|CSejour";
    $props["sejour_enfant_id"] = "ref notNull class|CSejour";
    $props["hors_etab"]        = "bool default|0";
    $props["heure"]            = "time";
    $props["rang"]             = "num pos";
    return $props;
  }
  
  function check() {
  	if ($msg = parent::check()) {
      return $msg;
  	}
  	
  	$this->completeField("operation_id", "sejour_maman_id", "grossesse_id");
  	
  	// Operation has to be part of sejour
  	if ($this->operation_id) {
      $operation = $this->loadRefOperation();
      if ($operation->sejour_id != $this->sejour_maman_id) {
      	return "failed-operation-notin-sejour";
      }
  	}

    // Sejour has to be part of grossesse
    $sejour = $this->loadRefSejourMaman();
    if ($sejour->grossesse_id != $this->grossesse_id) {
      return "failed-sejour-maman-notin-grossesse";
    }
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->heure) {
      $this->_view = $this->getFormattedValue("heure");
    }
    else {
      $this->_view = "Dossier provisoire";
    }
    if ($this->rang) {
      $this->_view .= ", rang " . $this->rang;
    }
  }
  
  function loadRefsFwd() {
    $this->loadRefOperation();
    $this->loadRefGrossesse();
  }
  
  function loadRefOperation() {
    return $this->_ref_operation = $this->loadFwdRef("operation_id", true);
  }
  
  function loadRefGrossesse() {
    return $this->_ref_grossesse = $this->loadFwdRef("grossesse_id", true);
  }
  
  function loadRefSejourEnfant() {
    return $this->_ref_sejour_enfant = $this->loadFwdRef("sejour_enfant_id", true);
  }

  function loadRefSejourMaman() {
    return $this->_ref_sejour_maman = $this->loadFwdRef("sejour_maman_id", true);
  }
}
?>