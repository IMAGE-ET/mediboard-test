<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

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
    
    $props["receiver_id"]   = "ref class|CDestinataireHprim"; 
    $props["initiateur_id"] = "ref class|CEchangeHprim";
    $props["object_class"]  = "enum list|CPatient|CSejour|COperation|CAffectation|CConsultation show|0";
    
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
      
      $validate = $domGetEvenement->schemaValidate(null, true, false);
      if (!is_bool($validate)) {
        $errors = explode("\n", utf8_decode($validate));
        $this->_doc_errors_msg = array_filter($errors);
      }
      
      $this->_message = utf8_encode($domGetEvenement->saveXML());
    } 
    
    if ($this->_acquittement !== null) {
      $this->type == "patients" ?
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients() : null;
      $this->type == "pmsi" ?
        $domGetAcquittement = new CHPrimXMLAcquittementsServeurActivitePmsi::$evenements[$this->sous_type] : null;
      
      $domGetAcquittement->loadXML($this->_acquittement);
      $domGetAcquittement->formatOutput = true; 
      
      $validate = $domGetAcquittement->schemaValidate(null, true, false);
      if (!is_bool($validate)) {
        $errors = explode("\n", utf8_decode($validate));
        $this->_doc_errors_ack = array_filter($errors);
      }
      
          
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
  
  function setObjectClassIdPermanent(CMbObject $mbObject) {
    $this->object_class = $mbObject->_class;
    
    if ($mbObject instanceof CPatient && $mbObject->_IPP) {
      $this->id_permanent = $mbObject->_IPP;
    }
    if ($mbObject instanceof CSejour && $mbObject->_NDA) {
      $this->id_permanent = $mbObject->_NDA;
    }
  }
  
  function handle() {
    return COperatorHprimXML::event($this);
  }

  function getFamily() {
    return self::$messages;
  }
  
  function populateEchange(CExchangeDataFormat $data_format, CHPrimXMLEvenements $dom_evt) {
    $this->date_production = mbDateTime();
    $this->group_id        = $data_format->group_id;
    $this->sender_id       = $data_format->sender_id;
    $this->sender_class    = $data_format->sender_class;
    $this->type            = $dom_evt->type;
    $this->sous_type       = $dom_evt->sous_type ? $dom_evt->sous_type : "inconnu";
    $this->_message        = $data_format->_message;;
  }
  
  function populateErrorEchange($msgAcq, $doc_valid, $type_error) {
    $this->_acquittement       = $msgAcq;
    $this->statut_acquittement = $type_error;
    $this->message_valide      = 0;
    $this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->date_echange        = mbDateTime();
    $this->store();
  }
  
  function setAck(CHPrimXMLAcquittements $dom_acq, $codes, $avertissement = null, $commentaires = null, CMbObject $mbObject = null) {
    $commentaire = $avertissement ? $avertissement : $commentaires;                    
    $statut      = $avertissement ? $dom_acq->_codes_erreurs["avt"] : $dom_acq->_codes_erreurs["ok"];

    $msgAcq = $dom_acq->generateAcquittements($statut, $codes, $commentaire, $mbObject);
    $doc_valid = $dom_acq->schemaValidate();
    
    $this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->statut_acquittement = $statut;
        
    if ($mbObject) {
      $this->setObjectIdClass($mbObject);
    }
    $this->_acquittement = $msgAcq;
    $this->date_echange = mbDateTime();
    $this->store();
    
    return $msgAcq;
  }
  
  function setAckError(CHPrimXMLAcquittements $dom_acq, $code_erreur, $commentaires = null, CMbObject $mbObject = null) {
    $statut = $dom_acq->_codes_erreurs["err"];
    
    $msgAcq    = $dom_acq->generateAcquittements($dom_acq->_codes_erreurs["err"], $code_erreur, $commentaires, $mbObject);
    $doc_valid = $dom_acq->schemaValidate();
    
    $this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->statut_acquittement = $statut;
    
    if ($mbObject) {
      $this->setObjectIdClass($mbObject);
    }
    $this->_acquittement = $msgAcq;
    $this->date_echange = mbDateTime();
    $this->store();
    
    return $msgAcq;
  }         
  
  function getConfigs($actor_guid) {
    list($sender_class, $sender_id) = explode("-", $actor_guid);
    
    $hprimxml_config = new CHprimXMLConfig();
    $hprimxml_config->sender_class = $sender_class;
    $hprimxml_config->sender_id    = $sender_id;
    $hprimxml_config->loadMatchingObject();
    
    return $this->_configs_format = $hprimxml_config;
  }
}
?>