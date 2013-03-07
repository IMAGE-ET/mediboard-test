<?php

/**
 * Exchange Data Format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeDataFormat
 * Exchange Data Format
 */

class CExchangeDataFormat extends CMbMetaObject {  
  // DB Fields
  var $group_id                  = null;
  var $date_production           = null;
  var $sender_id                 = null;
  var $sender_class              = null;
  var $receiver_id               = null;
  var $type                      = null;
  var $sous_type                 = null;
  var $date_echange              = null;
  var $message_content_id        = null;
  var $acquittement_content_id   = null;
  var $statut_acquittement       = null;
  var $message_valide            = null;
  var $acquittement_valide       = null;
  var $id_permanent              = null;
  var $object_id                 = null;
  var $object_class              = null;
  var $reprocess                 = null;
  
  // Filter fields
  var $_date_min                 = null;
  var $_date_max                 = null;
  
  // Form fields
  var $_self_sender              = null;
  var $_self_receiver            = null;
  var $_message                  = null;
  var $_acquittement             = null;
  var $_count_exchanges          = null;
  var $_count_msg_invalide       = null;
  var $_count_ack_invalide       = null;
  var $_observations             = array();
  var $_doc_errors_msg           = array();
  var $_doc_errors_ack           = array();
  var $_load_content             = true;
  var $_messages_supported_class = array();
  var $_family_message_class     = null;
  var $_family_message           = null;
  var $_configs_format           = null;
  var $_delayed                  = null;
  var $_exchange_ihe             = null;
  var $_to_treatment             = true;
  
  /**
   * @var CGroups
   */
  var $_ref_group      = null;
  
  /**
   * @var CInteropSender
   */
  var $_ref_sender     = null;
  
  /**
   * @var CInteropReceiver
   */
  var $_ref_receiver   = null;

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    
    $props["date_production"]     = "dateTime notNull";
    $props["sender_id"]           = "ref class|CInteropSender meta|sender_class";
    $props["sender_class"]        = "enum list|CSenderFTP|CSenderSOAP|CSenderFileSystem show|0";
    $props["receiver_id"]         = "ref class|CInteropActor";
    $props["group_id"]            = "ref notNull class|CGroups autocomplete|text";
    $props["type"]                = "str";
    $props["sous_type"]           = "str";
    $props["date_echange"]        = "dateTime";
    $props["statut_acquittement"] = "str show|0";
    $props["message_valide"]      = "bool show|0";
    $props["acquittement_valide"] = "bool show|0";
    $props["id_permanent"]        = "str";
    $props["object_id"]           = "ref class|CMbObject meta|object_class";
    $props["reprocess"]           = "num min|0 max|".CAppUI::conf("eai max_reprocess_retries")." default|0";
    
    $props["_self_sender"]        = "bool";
    $props["_self_receiver"]      = "bool notNull";
    $props["_date_min"]           = "dateTime";
    $props["_date_max"]           = "dateTime";
    $props["_count_exchanges"]    = "num";
    $props["_count_msg_invalide"] = "num";
    $props["_count_ack_invalide"] = "num";
    $props["_observations"]       = "str";
    $props["_doc_errors_msg"]     = "str";
    $props["_doc_errors_ack"]     = "str";
    
    return $props;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();

    // Chargement des tags
    $this->_tag_patient  = CPatient::getTagIPP($this->group_id);
    $this->_tag_sejour   = CSejour::getTagNDA($this->group_id);
    $this->_tag_mediuser = CMediusers::getTagMediusers($this->group_id);
    $this->_tag_service  = CService::getTagService($this->group_id);

    // Chargement des contents
    if ($this->_load_content) {
      $this->loadContent();
    }

    $this->_self_sender   = $this->sender_id   === null;
    $this->_self_receiver = $this->receiver_id === null;

    if ($this->date_echange > CMbDT::dateTime("+ ".CAppUI::conf("eai exchange_format_delayed")." minutes", $this->date_production)) {
      $this->_delayed = CMbDT::minutesRelative($this->date_production, $this->date_echange);
    }
  }

  /**
   * Load Groups
   *
   * @return CGroups
   */
  function loadRefGroups() {
    $this->_ref_group = $this->loadFwdRef("group_id");
  }

  /**
   * Load interop actors
   *
   * @return void
   */
  function loadRefsInteropActor() {
    $this->loadRefReceiver();
    $this->loadRefSender();
  }
  
  /**
   * Load interop sender
   *
   * @return CInteropSender
   */
  function loadRefSender(){
    return $this->_ref_sender = $this->loadFwdRef("sender_id");
  }
  
  /**
   * Load interop receiver
   *
   * @return CInteropReceiver
   */
  function loadRefReceiver(){
    return $this->_ref_receiver = $this->loadFwdRef("receiver_id");
  }

  /**
   * Load content
   *
   * @return void
   */
  function loadContent() {
  }

  /**
   * Get observations
   *
   * @return array
   */
  function getObservations() {
  }

  /**
   * Get errors
   *
   * @return array
   */
  function getErrors() {
  }

  /**
   * Get encoding
   *
   * @return string
   */
  function getEncoding() {
  }

  /**
   * Is well formed ?
   *
   * @param string $data Data
   *
   * @return bool
   */
  function isWellFormed($data) {
  }

  /**
   * Understand ?
   *
   * @param string        $data  Data
   * @param CInteropActor $actor Actor
   *
   * @return bool
   */
  function understand($data, CInteropActor $actor = null) {
  }

  /**
   * Handle exchange
   *
   * @return void
   */
  function handle() {
  }

  /**
   * Get configs
   *
   * @param string $actor_guid Actor
   *
   * @return array
   */
  function getConfigs($actor_guid) {
  }

  /**
   * Get family
   *
   * @return array
   */
  function getFamily() {
    return array();
  }
  
  /**
   * Get child exchanges
   *
   * @param string $class Classname
   * 
   * @return array CExchangeDataFormat collection 
   */
  static function getAll($class = "CExchangeDataFormat") {    
    return CApp::getChildClasses($class, array(), true);
  }

  /**
   * Count exchanges
   *
   * @return int|void
   */
  function countExchanges() {
    // Total des échanges
    $this->_count_exchanges = $this->countList();
    
    // Total des messages invalides
    $where = array();
    $where['message_valide'] = " = '0'";
    $this->_count_msg_invalide = $this->countList($where);
    
    // Total des acquittements invalides
    $where = array();
    $where['acquittement_valide'] = " = '0'";
    $this->_count_ack_invalide = $this->countList($where);
  }

  /**
   * Get messages supported
   *
   * @param string $actor_guid Actor guid
   * @param bool   $all        All messages
   * @param null   $evenement  Event name
   * @param null   $show_actif Show only active
   *
   * @return array
   */
  function getMessagesSupported($actor_guid, $all = true, $evenement = null, $show_actif = null) {
    list($object_class, $object_id) = explode("-", $actor_guid);
    $family = array();

    foreach ($this->getFamily() as $_message => $_root_class) {
      $root  = new $_root_class;
      
      foreach ($root->getEvenements() as $_evt => $_evt_class) {
        if ($evenement && ($evenement != $_evt)) {
          continue;
        }
        $message_supported = new CMessageSupported();
        $message_supported->object_class = $object_class;
        $message_supported->object_id    = $object_id;
        $message_supported->message      = $_evt_class;
        if ($show_actif) {
          $message_supported->active     = $show_actif;
        }
        $message_supported->loadMatchingObject();
        if (!$message_supported->_id && !$all) {
          continue;
        } 

        $this->_messages_supported_class[] = $message_supported->message;
        
        $family[$_root_class][] = $message_supported;
      }
    }

    return $family;
  }

  /**
   * Set object_id & object_class
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function setObjectIdClass(CMbObject $mbObject) {
    if ($mbObject) {
      $this->object_id    = $mbObject->_id;
      $this->object_class = $mbObject->_class;
    }
  }

  /**
   * Set permanent identifier
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function setIdPermanent(CMbObject $mbObject) {
    if ($mbObject instanceof CPatient) {
      if (!$mbObject->_IPP) {
        $mbObject->loadIPP($this->group_id);
      }
      $this->id_permanent = $mbObject->_IPP;
    }
    
    if ($mbObject instanceof CSejour) {
      if (!$mbObject->_NDA) {
        $mbObject->loadNDA($this->group_id);
      }
      $this->id_permanent = $mbObject->_NDA;
    }
  }

  /**
   * Reprocessing exchange
   *
   * @throws CMbException
   *
   * @return void
   */
  function reprocessing() {
    if ($this->reprocess >= CAppUI::conf("eai max_reprocess_retries")) {
      throw new CMbException("CExchangeDataFormat-too_many_retries", $this->reprocess);
    }
    
    $sender = new $this->sender_class;
    $sender->load($this->sender_id);
    
    // Suppression de l'identifiant dans le cas où l'échange repasse pour éviter un autre échange avec
    // un identifiant forcé
    if ($this instanceof CExchangeAny) {
      $exchange_id = $this->_id;
      $this->_id = null;
    }
    
    if (!$ack_data = CEAIDispatcher::dispatch($this->_message, $sender, $this->_id)) {
      // Dans le cas d'un échange générique on le supprime
      if ($this instanceof CExchangeAny) {
        $this->_id = $exchange_id;
        if ($msg = $this->delete()) {
          throw new CMbException("CMbObject-msg-delete-failed", $msg);
        }
      }
    }
    
    $this->load($this->_id);
    
    // Dans le cas d'un échange générique on le supprime
    if ($this instanceof CExchangeAny) {
      $this->_id = $exchange_id;
      if ($msg = $this->delete()) {
        throw new CMbException("CMbObject-msg-delete-failed", $msg);
      }
    }

    if (!$ack_data) {
      return;
    }
    
    if ($this instanceof CEchangeHprim) {
      $dom_evt = $sender->_data_format->_family_message->getHPrimXMLEvenements($this->_message);
      $ack = CHPrimXMLAcquittements::getAcquittementEvenementXML($dom_evt);
      $ack->loadXML($ack_data);
      $ack_valid = $ack->schemaValidate();
      if ($ack_valid) {
        $this->statut_acquittement = $ack->getStatutAcquittement();
      }
    }
    
    if ($this instanceof CEchangeHprim21) {
      $ack = new CHPrim21Acknowledgment($sender->_data_format->_family_message);
      $ack->handle($ack_data);
      $this->statut_acquittement = $ack->getStatutAcknowledgment(); 
      $ack_valid = $ack->message->isOK(CHL7v2Error::E_ERROR);
    }
    
    if ($this instanceof CExchangeIHE) {
      $evt               = $sender->_data_format->_family_message;
      $evt->_data_format = $sender->_data_format;

      // Récupération des informations du message - CHL7v2MessageXML
      $dom_evt = $evt->handle($this->_message);
      $dom_evt->_is_i18n = $evt->_is_i18n;

      $ack = $dom_evt->getEventACK($evt);
      $ack->handle($ack_data);

      $this->statut_acquittement = $ack->getStatutAcknowledgment();
      $ack_valid = $ack->message->isOK(CHL7v2Error::E_ERROR);
    }
    
    $this->date_echange        = CMbDT::dateTime();
    $this->acquittement_valide = $ack_valid ? 1 : 0;
    $this->_acquittement       = $ack_data;
    $this->reprocess++;

    if ($msg = $this->store()) {
      throw new CMbException("CMbObject-msg-store-failed", $msg);
    }
  }
}