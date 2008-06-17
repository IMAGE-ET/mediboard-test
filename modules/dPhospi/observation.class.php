<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * 
 * Classe CObservationMedicale. 
 * @abstract Permet d'ajouter des observations médicales à un séjour
 */

class CObservationMedicale extends CMbObject {

  // DB Table key
  var $observation_medicale_id = null;	
  
  // DB Fields
  var $sejour_id    = null;
  var $user_id      = null;
  
  var $degre        = null;
  var $date         = null;
  var $text         = null;
  
  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'observation_medicale';
    $spec->key   = 'observation_medicale_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["sejour_id"]    = "notNull ref class|CSejour";
    $specs["user_id"]      = "notNull ref class|CMediusers";
    $specs["degre"]        = "notNull enum list|low|high default|low";
    $specs["date"]         = "notNull dateTime";
    $specs["text"]         = "text";
    return $specs;
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_view = "Observation du Dr ".$this->_ref_user->_view;
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
  
}

?>