<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::loadClass('CEchangeXML');

class CEchangeHprim extends CEchangeXML {
	static $messages = array(
	   "pmsi"     => "CHPrimXMLEvenementsServeurActivitePmsi", 
		 "patients" => "CHPrimXMLEvenementsPatients"  
	);
	
  // DB Table key
  var $echange_hprim_id     = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_hprim';
    $spec->key   = 'echange_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["emetteur_id"]     = "ref class|CDestinataireHprim";
    $props["destinataire_id"] = "ref class|CDestinataireHprim";
    $props["initiateur_id"]   = "ref class|CEchangeHprim";
    $props["object_class"]    = "enum list|CPatient|CSejour|COperation|CAffectation show|0";
    
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CEchangeHprim initiateur_id";
    
    return $backProps;
  }
  
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }
  
  function getObservations($display_errors = false) {
    if ($this->_acquittement) {
      $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
      $domGetAcquittement->loadXML($this->_acquittement);
      $doc_valid = $domGetAcquittement->schemaValidate(null, false, false);
      if ($doc_valid) {    
        return $this->_observations = $domGetAcquittement->getAcquittementObservationPatients();
      }
    }
  }
  
  function loadView() {
    parent::loadView();
    
    $this->getObservations();
  }
  
  function setObjectClassIdPermanent($mbObject) {
    if ($mbObject instanceof CPatient) {
      $this->object_class = "CPatient";
      if ($mbObject->_IPP) {
        $this->id_permanent = $mbObject->_IPP;
      }
    }
    if ($mbObject instanceof CSejour) {
      $this->object_class = "CSejour";
      if ($mbObject->_num_dossier) {
        $this->id_permanent = $mbObject->_num_dossier;
      }
    }
    if ($mbObject instanceof COperation) {
      $this->object_class = "COperation";
    }
    if ($mbObject instanceof CAffectation) {
      $this->object_class = "CAffectation";
    }
  }
}
?>