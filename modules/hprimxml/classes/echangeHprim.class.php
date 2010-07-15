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
  var $message_content_id      = null;
  var $acquittement_content_id = null;
  var $statut_acquittement  = null;
  var $initiateur_id        = null;
  var $message_valide       = null;
  var $acquittement_valide  = null;
  var $id_permanent				  = null;
    
  var $_ref_notifications   = null;
  
  // Form fields
  var $_self_emetteur     = null;
  var $_self_destinataire = null;
  var $_observations      = null;
  var $_message           = null;
  var $_acquittement      = null;
  
  // Filter fields
  var $_date_min          = null;
  var $_date_max          = null;
  
  // Forward references
  var $_ref_group         = null;  
  
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
    $specs["message_content_id"]      = "ref class|CContentXML show|0";
    $specs["acquittement_content_id"] = "ref class|CContentXML show|0";
    $specs["initiateur_id"]         = "ref class|CEchangeHprim";
    $specs["statut_acquittement"]   = "str show|0";
    $specs["message_valide"]        = "bool show|0";
    $specs["acquittement_valide"]   = "bool show|0";
    $specs["id_permanent"]          = "str";
    $specs["object_id"]             = "ref class|CMbObject meta|object_class unlink";
    $specs["object_class"]          = "enum list|CPatient|CSejour|COperation show|0";
    
    $specs["_self_emetteur"]        = "bool";
    $specs["_self_destinataire"]    = "bool notNull";
    $specs["_observations"]         = "str";
    
    $specs["_date_min"]             = "dateTime";
    $specs["_date_max"]             = "dateTime";
    
    $specs["_message"]              = "xml";
    $specs["_acquittement"]         = "xml";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CEchangeHprim initiateur_id";
    
    return $backProps;
  }
  
  function loadContent() {
    $content = new CContentXML();
    $content->load($this->message_content_id);
    $this->_message = $content->content;
    
    $content = new CContentXML();
    $content->load($this->acquittement_content_id);
    $this->_acquittement = $content->content;
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
  	
  	// Chargement des contents 
  	$this->loadContent();
  	 
  	$this->_self_emetteur = $this->emetteur == CAppUI::conf('mb_id');
    $this->_self_destinataire = $this->destinataire == CAppUI::conf('mb_id');
  }
  
  function updateDBFields() {
    if ($this->_message !== null) {
      $content = new CContentXML();
      $content->load($this->message_content_id);
      $content->content = $this->_message;
      if ($msg = $content->store()) {
        return $msg;
      }
      if (!$this->message_content_id) {
        $this->message_content_id = $content->_id;
      }
    }
    
    if ($this->_acquittement !== null) {
      $content = new CContentXML();
      $content->load($this->acquittement_content_id);
      $content->content = $this->_acquittement;
      if ($msg = $content->store()) {
        return $msg;
      }
      if (!$this->acquittement_content_id) {
        $this->acquittement_content_id = $content->_id;
      }
    }
  }
  
  function getObservations() {
    if ($this->_acquittement) {
      $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
      $domGetAcquittement->loadXML(utf8_decode($this->_acquittement));
      $doc_valid = $domGetAcquittement->schemaValidate();
      if ($doc_valid) {    
        return $this->_observations = $domGetAcquittement->getAcquittementObservationPatients();
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
	
	function setAckError($doc_valid, $messageAcquittement, $statut_acquittement) {
		$this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->_acquittement = $messageAcquittement;
    $this->statut_acquittement = $statut_acquittement;
    $this->date_echange = mbDateTime();
    $this->store();
	}
}
?>