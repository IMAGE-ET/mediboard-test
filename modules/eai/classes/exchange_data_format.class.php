<?php

/**
 * Echange Data Format EAI
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
 * Echange Data Format
 */
CAppUI::requireSystemClass('mbMetaObject');

class CExchangeDataFormat extends CMbMetaObject {  
  // DB Fields
  var $group_id                = null;
  var $date_production         = null;
  var $emetteur_id             = null;
  var $destinataire_id         = null;
  var $type                    = null;
  var $sous_type               = null;
  var $date_echange            = null;
  var $message_content_id      = null;
  var $acquittement_content_id = null;
  var $statut_acquittement     = null;
  var $message_valide          = null;
  var $acquittement_valide     = null;
  var $id_permanent            = null;
  var $object_id               = null;
  var $object_class            = null;
  
  // Filter fields
  var $_date_min                = null;
  var $_date_max                = null;
  
  // Form fields
  var $_self_emetteur           = null;
  var $_self_destinataire       = null;
  var $_message                 = null;
  var $_acquittement            = null;
  var $_count_exchanges         = null;
  var $_count_msg_invalide      = null;
  var $_count_ack_invalide      = null;
  var $_observations            = array();
  var $_doc_errors_msg          = array();
  var $_doc_errors_ack          = array();
  var $_load_content            = true;
  var $_messages_supported_class = array();
  var $_family_message          = null;
  
  // Forward references
  var $_ref_group          = null;
  var $_ref_emetteur       = null;
  var $_ref_destinataire   = null;
  
  function getProps() {
    $props = parent::getProps();
    
    $props["date_production"]     = "dateTime notNull";
    $props["group_id"]            = "ref notNull class|CGroups autocomplete|text";
    $props["type"]                = "str";
    $props["sous_type"]           = "str";
    $props["date_echange"]        = "dateTime";
    $props["statut_acquittement"] = "str show|0";
    $props["message_valide"]      = "bool show|0";
    $props["acquittement_valide"] = "bool show|0";
    $props["id_permanent"]        = "str";
    $props["object_id"]           = "ref class|CMbObject meta|object_class unlink";
    
    $props["_self_emetteur"]      = "bool";
    $props["_self_destinataire"]  = "bool notNull";
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
  
  function loadRefGroups() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsDestinataireInterop() {
    $this->_ref_emetteur     = $this->loadFwdRef("emetteur_id");
    $this->_ref_destinataire = $this->loadFwdRef("destinataire_id");
  }
  
  function getObservations() {}
  
  function getErrors() {}

  function updateFormFields() {
    parent::updateFormFields();
    
    // Chargement des tags
    $this->_tag_patient  = CPatient::getTagIPP($this->group_id);   
    $this->_tag_sejour   = CSejour::getTagNumDossier($this->group_id);
    $this->_tag_mediuser = CMediusers::getTagMediusers($this->group_id);
    $this->_tag_service  = CService::getTagService($this->group_id); 
    
    // Chargement des contents 
    if ($this->_load_content) {
      $this->loadContent();
    }   
     
    $this->_self_emetteur     = $this->emetteur_id     === null;
    $this->_self_destinataire = $this->destinataire_id === null;
  }
  
  /**
   * Get child exchanges
   * 
   * @return array CExchangeDataFormat collection 
   */
  static function getAll($class = "CExchangeDataFormat") {    
    return CApp::getChildClasses($class, array(), true);
  }
 
  function countExchanges() {
    // Total des échanges
    $this->_count_exchanges    = $this->countList();
    
    // Total des messages invalides
    $where = array();
    $where['message_valide'] = " = '0'";
    $this->_count_msg_invalide = $this->countList($where);
    
    // Total des acquittements invalides
    $where = array();
    $where['acquittement_valide'] = " = '0'";
    $this->_count_ack_invalide = $this->countList($where);
  }
  
  function isWellForm($data) {}
  
  function understand($data, CInteropActor $actor = null) {}
  
  function handle() {}
  
  function getMessagesSupported($actor_guid, $all = true, $evenement = null, $show_actif = null) {
    list($object_class, $object_id) = explode("-", $actor_guid);
    $messages = array();

    foreach ($this->getMessages() as $_message => $_root_class) {
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
        
        $messages[$_root_class][]          = $message_supported;
      }
    }

    return $messages;
  }
  
  function getMessages() {
    return array(); 
  }
}

?>