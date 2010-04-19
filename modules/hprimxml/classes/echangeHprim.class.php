<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireSystemClass('mbMetaObject');

class CEchangeHprim extends CMbMetaObject {
	static $messages = array(
	   "pmsi"     => "CHPrimXMLEvenementsServeurActivitePmsi", 
		 "patients" => "CHPrimXMLEvenementsPatients"  
	);
	
  // DB Table key
  var $echange_hprim_id     = null;
  
  // DB Fields
  var $group_id             = null;
  var $date_production      = null;
  var $emetteur             = null;
  var $identifiant_emetteur = null;
  var $destinataire         = null;
  var $type                 = null;
  var $sous_type            = null;
  var $date_echange         = null;
  var $message              = null;
  var $acquittement         = null;
  var $statut_acquittement  = null;
  var $initiateur_id        = null;
  var $message_valide       = null;
  var $acquittement_valide  = null;
  var $id_permanent				  = null;
  var $compressed           = null;
    
  var $_ref_notifications   = null;
  
  // Form fields
  var $_self_emetteur       = null;
  var $_self_destinataire   = null;
  var $_observations        = null;
  
  // Filter fields
  var $_date_min            = null;
  var $_date_max            = null;
  
  // Forward references
  var $_ref_group = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_hprim';
    $spec->key   = 'echange_hprim_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["date_production"]       = "dateTime notNull";
    $specs["group_id"]              = "ref notNull class|CGroups";
    $specs["emetteur"]              = "str";
    $specs["identifiant_emetteur"]  = "str";
    $specs["destinataire"]          = "str notNull";
    $specs["type"]                  = "str";
    $specs["sous_type"]             = "str";
    $specs["date_echange"]          = "dateTime";
    $specs["message"]               = "xml notNull show|0";
    $specs["acquittement"]          = "xml show|0";
    $specs["initiateur_id"]         = "ref class|CEchangeHprim";
    $specs["statut_acquittement"]   = "str show|0";
    $specs["message_valide"]        = "bool show|0";
    $specs["acquittement_valide"]   = "bool show|0";
    $specs["id_permanent"]          = "str";
    $specs["compressed"]            = "bool show|0";
    $specs["object_id"]             = "ref class|CMbObject meta|object_class unlink";
    $specs["object_class"]          = "enum list|CPatient|CSejour|COperation show|0";
    
    $specs["_self_emetteur"]        = "bool";
    $specs["_self_destinataire"]    = "bool notNull";
    $specs["_observations"]         = "str";
    
    $specs["_date_min"]             = "dateTime";
    $specs["_date_max"]             = "dateTime";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CEchangeHprim initiateur_id";
    
    return $backProps;
  }
  
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }
  
  function loadRefsFwd() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function updateFormFields() {
  	parent::updateFormFields();
  	
  	$this->_self_emetteur = $this->emetteur == CAppUI::conf('mb_id');
    $this->_self_destinataire = $this->destinataire == CAppUI::conf('mb_id');
  }
  
  function getObservations() {
    if ($this->acquittement) {
      $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
      $domGetAcquittement->loadXML(utf8_decode($this->acquittement));
      $doc_valid = $domGetAcquittement->schemaValidate();
      if ($doc_valid) {    
        return $this->_observations = $domGetAcquittement->getAcquittementObservation();
      }
    }
  }
  
  function loadView() {
    parent::loadView();
    
    $this->getObservations();
  }
  
  function setObjectIdClass($object_class, $object_id) {
    $this->object_id = $object_id;
    $this->object_class = $object_class;
  }
  
  function getObjectIdClass() {
    switch($this->sous_type) {
      case "enregistrementPatient" :
        $this->object_class = "CPatient";
        $this->loadObjectId("hprim:enregistrementPatient", "hprim:patient");
        break;
      case "fusionPatient" :
        $this->object_class = "CPatient";
        $this->loadObjectId("hprim:fusionPatient", "hprim:patient");
        break;
      case "venuePatient" :
        $this->object_class = "CSejour"; 
        $this->loadObjectId("hprim:venuePatient", "hprim:venue");
        break;
      case "mouvementPatient" :
        $this->object_class = "CSejour";
        $this->loadObjectId("hprim:mouvementPatient", "hprim:venue");
        break;
      case "fusionVenue" :
        $this->object_class = "CSejour";
        $this->loadObjectId("hprim:fusionVenue", "hprim:venue");
        break;
      case "debiteursVenue" :
        $this->object_class = "CSejour";
        $this->loadObjectId("hprim:debiteursVenue", "hprim:venue");
        break;
      default :       
        $this->object_class = null;
        $this->object_id = null;
    }
  }
    
  function loadObjectId($evtNode = null , $objectNode = null) {
    if ($this->_self_emetteur) {
      $domGetIdSourceObject = new CHPrimXMLEvenementsPatients();
      @$domGetIdSourceObject->loadXML(utf8_decode($this->message));
      $id_source = null;
      try {
        $id_source = $domGetIdSourceObject->getIdSourceObject($evtNode, $objectNode);
      } catch (Exception $e) {}
      $this->object_id = $id_source;
      return;
    }
    
    $dest_hprim = new CDestinataireHprim();
    $dest_hprim->nom = $this->emetteur;
    $dest_hprim->loadMatchingObject();

    // Recuperation de la valeur de l'id400
    $id400 = new CIdSante400();
    if ($this->object_class == "CPatient") {
      $id400->tag = $dest_hprim->_tag_patient;
    }
    if ($this->object_class == "CSejour") {
      $id400->tag = $dest_hprim->_tag_sejour;
    }
    $id400->object_class = $this->object_class;
    if ($this->id_permanent) {
      $id400->id400 = $this->id_permanent;
      $id400->loadMatchingObject();
    }
    
    // Si pas d'id400
    if(!$id400->_id){
      $this->object_id = null;
    }
    
    $this->object_id = $id400->object_id;   
    
  }
}
?>