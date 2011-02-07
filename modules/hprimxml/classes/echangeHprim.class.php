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
		 "patients" => "CHPrimXMLEvenementsPatients",
	   "pmsi"     => "CHPrimXMLEvenementsServeurActivitePmsi" 
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
  
  function loadRefsBack() {
    parent::loadRefsBack();
    
    $this->loadRefNotifications();
  }
  
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }
  
  function getErrors() {
    if ($this->_message !== null) {
      $domGetEvenement = null;
      $this->type == "patients" ?
        $domGetEvenement = new CHPrimXMLEvenementsPatients() : null;
      $this->type == "pmsi" ?
        $domGetEvenement = new CHPrimXMLEvenementsServeurActivitePmsi::$evenements[$this->sous_type] : null;
        
      $domGetEvenement->loadXML($this->_message);
      $domGetEvenement->formatOutput = true;

      $errors = explode("\n", utf8_decode($domGetEvenement->schemaValidate(null, true, false)));
      $this->_doc_errors_msg = array_filter($errors);
      
      $this->_message = utf8_encode($domGetEvenement->saveXML());
    } 
    
    if ($this->_acquittement !== null) {
      $this->type == "patients" ?
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients() : null;
      $this->type == "pmsi" ?
        $domGetAcquittement = new CHPrimXMLAcquittementsServeurActivitePmsi::$evenements[$this->sous_type] : null;
      $domGetAcquittement->loadXML($this->_acquittement);
      $domGetAcquittement->formatOutput = true; 
      $errors = explode("\n", utf8_decode($domGetAcquittement->schemaValidate(null, true, false)));
      $this->_doc_errors_ack = array_filter($errors);
          
      $this->_acquittement = utf8_encode($domGetAcquittement->saveXML());
    }
  }
  
  function getObservations($display_errors = false) {
    if ($this->_acquittement) {
      if ($this->type == "patients") {
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
        $domGetAcquittement->loadXML($this->_acquittement);
        $doc_valid = $domGetAcquittement->schemaValidate(null, false, false);
        if ($doc_valid) {    
          return $this->_observations = $domGetAcquittement->getAcquittementObservationPatients();
        }
      }
      /* @todo a remplir ... */
      if ($this->type == "pmsi") {
        return $this->_observations = array();
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