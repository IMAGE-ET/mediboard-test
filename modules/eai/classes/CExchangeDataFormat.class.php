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
  public $group_id;
  public $date_production;
  public $sender_id;
  public $sender_class;
  public $receiver_id;
  public $type;
  public $sous_type;
  public $date_echange;
  public $message_content_id;
  public $acquittement_content_id;
  public $statut_acquittement;
  public $message_valide;
  public $acquittement_valide;
  public $id_permanent;
  public $object_id;
  public $object_class;
  public $reprocess;
  public $master_idex_missing;
  
  // Filter fields
  public $_date_min;
  public $_date_max;
  
  // Form fields
  public $_self_sender;
  public $_self_receiver;
  public $_message;
  public $_acquittement;
  public $_count_exchanges;
  public $_count_msg_invalide;
  public $_count_ack_invalide;
  public $_observations             = array();
  public $_doc_errors_msg           = array();
  public $_doc_warnings_msg         = array();
  public $_doc_errors_ack           = array();
  public $_doc_warnings_ack         = array();
  public $_load_content             = true;
  public $_messages_supported_class = array();
  public $_to_treatment             = true;
  public $_family_message_class;
  public $_family_message;
  public $_configs_format;
  public $_delayed;

  /** @var CGroups */
  public $_ref_group;

  /** @var CInteropSender */
  public $_ref_sender;
  
  /** @var CInteropReceiver */
  public $_ref_receiver;

  /** @var  CContentAny */
  public $_ref_message_content;
  /** @var  CContentAny */
  public $_ref_acquittement_content;

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
    $props["object_id"]           = "ref class|CMbObject meta|object_class unlink";
    $props["reprocess"]           = "num min|0 max|".CAppUI::conf("eai max_reprocess_retries")." default|0";
    $props["master_idex_missing"] = "bool show|0";
    
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
    $this->_ref_group = $this->loadFwdRef("group_id", true);
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
    return $this->_ref_sender = $this->loadFwdRef("sender_id", true);
  }
  
  /**
   * Load interop receiver
   *
   * @return CInteropReceiver
   */
  function loadRefReceiver(){
    return $this->_ref_receiver = $this->loadFwdRef("receiver_id", true);
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
   * @return string[] Data format classes collection
   */
  static function getAll($class = "CExchangeDataFormat") {    
    return CApp::getChildClasses($class, true);
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
    $where['message_valide']     = " = '0'";
    if (isset($this->_specs["message_content_id"])) {
      $where['message_content_id'] = "IS NOT NULL";
    }
    $this->_count_msg_invalide = $this->countList($where);
    
    // Total des acquittements invalides
    $where = array();
    $where['acquittement_valide']     = " = '0'";

    if (isset($this->_specs["acquittement_content_id"])) {
      $where['acquittement_content_id'] = "IS NOT NULL";
    }
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

        $message_supported->loadEventByName();
        $message_supported->_data_format = $this;

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
   * Send exchange
   *
   * @throws CMbException
   *
   * @return void
   */
  function send() {
    $this->loadRefsInteropActor();

    if (!$this->message_valide) {
      throw new CMbException("CExchangeDataFormat-msg-Invalid exchange");
    }

    $receiver = $this->_ref_receiver;
    $receiver->loadConfigValues();

    $evenement = null;

    $msg = $this->_message;
    if ($receiver instanceof CReceiverHL7v2) {
      if ($receiver->_configs["encoding"] == "UTF-8") {
        $msg = utf8_encode($msg);
      }

      $evenement   = "evenementsPatient";
      $data_format = CIHE::getEvent($this);
    }

    if ($receiver instanceof CDestinataireHprim) {
      if ($receiver->_configs["encoding"] == "UTF-8") {
        $msg = utf8_encode($msg);
      }

      if ($this->type == "patients") {
        $evenement   = "evenementPatient";
        $data_format = CHPrimXMLEventPatient::getHPrimXMLEvenements($this->_message);
      }

      if ($this->type == "pmsi") {
        $data_format = CHPrimXMLEventServeurActivitePmsi::getHPrimXMLEvenements($this->_message);
        $evenement = $data_format->sous_type;
      }
    }

    if ($receiver instanceof CPhastDestinataire) {
      $data_format = CPhastEvenementsPN13::getXMLEvenementsPN13($this->_message);
      $evenement = $data_format->sous_type;
    }

    if (!$evenement) {
      throw new CMbException("CExchangeDataFormat-msg-No events defined");
    }

    $source = CExchangeSource::get("$receiver->_guid-$evenement");

    if (!$source->_id  || !$source->active) {
      throw new CMbException("CExchangeDataFormat-msg-No source for this actor");
    }

    // Si on n'a pas d'IPP et NDA
    if ($this->master_idex_missing) {
      throw new CMbException("CExchangeDataFormat-msg-Master idex missing");
    }

    $source->setData($msg, false, $this);
    $source->send();

    $this->date_echange = CMbDT::dateTime();

    // Si on n'a pas d'acquittement
    if (!$ack_data = $source->getACQ()) {
      $this->store();
      return;
    }

    if ($this instanceof CEchangeHprim) {
      $ack_data = utf8_decode($ack_data);
      $ack = CHPrimXMLAcquittements::getAcquittementEvenementXML($data_format);
      $ack->loadXML($ack_data);
      $doc_valid = $ack->schemaValidate();
      if ($doc_valid) {
        $this->statut_acquittement = $ack->getStatutAcquittement();
      }
    }

    if ($this instanceof CExchangeHL7v2) {
      $ack = new CHL7v2Acknowledgment($data_format);
      $ack->handle($ack_data);
      $this->statut_acquittement = $ack->getStatutAcknowledgment();
      $this->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    }

    if ($this instanceof CExchangePhast) {
      $ack = new CPhastAcquittementsPN13();
      $ack->loadXML($ack_data);
      $doc_valid = $ack->schemaValidate();
      if ($doc_valid) {
        $this->statut_acquittement = $ack->getCodeAcquittement();
      }
    }

    $this->_acquittement = $ack_data;
    $this->store();
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

    $ack_valid = 0;
    if ($this instanceof CEchangeHprim) {
      $dom_evt = $sender->_data_format->_family_message->getHPrimXMLEvenements($this->_message);
      $ack = CHPrimXMLAcquittements::getAcquittementEvenementXML($dom_evt);
      $ack->loadXML($ack_data);
      $ack_valid = $ack->schemaValidate(null, false, false);
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
    
    if ($this instanceof CExchangeHL7v2) {
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

  /**
   * Inject master idex (IPP/NDA) missing
   *
   * @throws CMbException
   *
   * @return void
   */
  function injectMasterIdexMissing() {
    $patient = null;
    $sejour  = null;
    if ($this->object_class && $this->object_id) {
      $object = CMbObject::loadFromGuid("$this->object_class-$this->object_id");

      if ($object instanceof CPatient) {
        $patient = $object;
        $patient->loadIPP($this->group_id);

        if (!$patient->_IPP) {
          return;
        }
      }

      if ($object instanceof CSejour) {
        $sejour = $object;
        $sejour->loadNDA($this->group_id);
        $object->loadRefPatient()->loadIPP($this->group_id);

        $patient = $sejour->_ref_patient;

        if (!$sejour->_NDA) {
          return;
        }
      }
    }

    $pattern = "===IPP_MISSING===";
    if (strpos($this->_message, $pattern) !== false) {
      $this->_message = str_replace("===IPP_MISSING===", $patient->_IPP, $this->_message);
    }

    $pattern = "===NDA_MISSING===";
    if (strpos($this->_message, $pattern) !== false) {
      $this->_message = str_replace("===NDA_MISSING===", $sejour->_NDA, $this->_message);
    }

    $this->master_idex_missing = strpos($this->_message, "===NDA_MISSING===") !== false ||
                                 strpos($this->_message, "===IPP_MISSING===") !== false;

    if ($msg = $this->store()) {
      throw new CMbException(CAppUI::tr("$this->_class-msg-store-failed") . $msg);
    }
  }
}