<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPhospi
 *	@version $Revision$
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
    $specs["degre"]        = "enum notNull list|low|high|info default|low";
    $specs["date"]         = "dateTime notNull";
    $specs["text"]         = "text helped|degre";
    return $specs;
  }

  function canEdit(){
    $nb_hours = CAppUI::conf("dPprescription CPrescription max_time_modif_suivi_soins");
    $datetime_max = mbDateTime("+ $nb_hours HOURS", $this->date);
    return $this->_canEdit = (mbDateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->user_id);  
  }
	
  function loadRefSejour(){
    $this->_ref_sejour = new CSejour;
    $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
  }
  
  function loadRefUser(){
    $this->_ref_user = new CMediusers;
    $this->_ref_user = $this->_ref_user->getCached($this->user_id);
		$this->_ref_user->loadRefFunction();
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
  	$this->_view = "Observation du Dr ".$this->_ref_user->_view;
  }
  
  function check(){
    if (!$this->_id && $this->degre == "info" && $this->text == "Visite effectuée"){
      if($this->countNotifSiblings()) {
        return "Notification deja effectuée";
      }
    }
    return parent::check();
  }
  
  function countNotifSiblings() {
    $date = mbDate($this->date);
    $observation = new CObservationMedicale();
    $where = array();
    $where["sejour_id"]  = " = '$this->sejour_id'";
    $where["user_id"]  = " = '$this->user_id'";
    $where["degre"]  = " = 'info'";
    $where["date"]  = " LIKE '$date%'";
    $where["text"] = " = 'Visite effectuée'";
    return $observation->countList($where);
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
  
}

?>