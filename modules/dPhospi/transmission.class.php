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

class CTransmissionMedicale extends CMbMetaObject {

  // DB Table key
  var $transmission_medicale_id = null;	
  
  // DB Fields
  var $sejour_id = null;
  var $user_id   = null;
  
  var $degre = null;
  var $date  = null;
  var $text  = null;
  
  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
  var $_ref_cible  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["object_id"]    = "ref class|CMbObject meta|object_class cascade";
  	$specs["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineComment|CCategoryPrescription|CAdministration|CPerfusion";
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
  	$this->_view = "Transmission de ".$this->_ref_user->_view;
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
  
}

?>