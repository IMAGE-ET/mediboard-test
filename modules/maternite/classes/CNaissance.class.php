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
  var $operation_id     = null;
  var $grossesse_id     = null;
  var $sejour_enfant_id = null;
  
  // DB Fields
  var $hors_etab        = null;
  var $heure            = null;
  var $rang             = null;
  
  // DB References
  var $_ref_operation   = null;
  var $_ref_grossesse   = null;
  var $_ref_sejour_enfant = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'naissance';
    $spec->key   = 'naissance_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["operation_id"]     = "ref class|COperation";
    $specs["grossesse_id"]     = "ref class|CGrossesse";
    $specs["sejour_enfant_id"] = "ref notNull class|CSejour";
    $specs["hors_etab"]        = "bool default|0";
    $specs["heure"]            = "time notNull";
    $specs["rang"]             = "num pos notNull";
    return $specs;
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
}
?>