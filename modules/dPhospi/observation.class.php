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

  function getProps() {
  	$specs = parent::getProps();
    $specs["sejour_id"]    = "ref notNull class|CSejour";
    $specs["user_id"]      = "ref notNull class|CMediusers";
    $specs["degre"]        = "enum notNull list|low|high default|low";
    $specs["date"]         = "dateTime notNull";
    $specs["text"]         = "text helped";
    return $specs;
  }
    
  function loadRefSejour(){
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
  }
  
  function loadRefUser(){
    $this->_ref_user = new CMediusers;
    $this->_ref_user = $this->_ref_user->getCached($this->user_id);
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
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