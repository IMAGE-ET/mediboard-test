<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * 
 * Classe CTransmissionMedicale. 
 * @abstract Permet d'ajouter des transmissions médicales à un séjour
 */

class CTransmissionMedicale extends CMbObject {

  // DB Table key
  var $transmission_medicale_id = null;	
  
  // DB Fields
  var $sejour_id             = null;
  var $user_id               = null;
  var $cible_transmission_id = null;
  
  var $degre        = null;
  var $date         = null;
  var $text         = null;
  
  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["sejour_id"]             = "notNull ref class|CSejour";
    $specs["user_id"]               = "notNull ref class|CMediusers";
    $specs["cible_transmission_id"] = "ref class|CCibleTransmission";
    $specs["degre"]                 = "notNull enum list|low|high default|low";
    $specs["date"]                  = "notNull dateTime";
    $specs["text"]                  = "text";
    return $specs;
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour->load($this->sejour_id);
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_view = "Transmission faite par ".$this->_ref_user->_view;
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
  
}

?>