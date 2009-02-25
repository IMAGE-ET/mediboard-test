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
    $specs["sejour_id"]    = "ref notNull class|CSejour";
    $specs["user_id"]      = "ref notNull class|CMediusers";
    $specs["degre"]        = "enum notNull list|low|high default|low";
    $specs["date"]         = "dateTime notNull";
    $specs["text"]         = "text helped";
    return $specs;
  }
  
  function getHelpedFields(){
    return array(
      "text" => null
    );
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