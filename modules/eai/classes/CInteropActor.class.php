<?php

/**
 * Interop Actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CInteropActor 
 * Interoperability Actor
 */
class CInteropActor extends CMbObject {
  // DB Fields
  var $nom                       = null;
  var $libelle                   = null;
  var $group_id                  = null;
  var $actif                     = null;
  
  // Form fields
  var $_reachable                = null;
  var $_parent_class             = null;
  var $_delete_file              = true;
  
  var $_tag_patient              = null;
  var $_tag_sejour               = null;
  var $_tag_mediuser             = null;
  var $_tag_service              = null;
  var $_tag_chambre              = null;
  var $_tag_lit                  = null;
  var $_tag_movement             = null;
  var $_tag_visit_number         = null;
  var $_tag_hprimxml             = null;
  
  // Forward references
  var $_ref_group                = null;
  var $_ref_exchanges_sources    = null;
  var $_ref_last_message         = null;
  var $_ref_messages_supported   = null;
  var $_ref_msg_supported_family = array();
  
  function getProps() {
    $props = parent::getProps();
    $props["nom"]        = "str notNull";
    $props["libelle"]    = "str";
    $props["group_id"]   = "ref notNull class|CGroups autocomplete|text";
    $props["actif"]      = "bool notNull";
    
    $props["_reachable"]        = "bool";
    $props["_parent_class"]     = "str";
    
    $props["_tag_patient"]      = "str";
    $props["_tag_sejour"]       = "str";
    $props["_tag_mediuser"]     = "str";
    $props["_tag_service"]      = "str";
    $props["_tag_chambre"]      = "str";
    $props["_tag_lit"]          = "str";
    $props["_tag_movement"]     = "str";
    $props["_tag_visit_number"] = "str";
    $props["_tag_hprimxml"]     = "str";
    
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
        
    $this->_view = $this->libelle ? $this->libelle : $this->nom;
    $this->_type_echange = $this->_class;

    $this->_tag_patient       = CPatient::getTagIPP($this->group_id);  
    $this->_tag_sejour        = CSejour::getTagNDA($this->group_id);
    $this->_tag_mediuser      = CMediusers::getTagMediusers($this->group_id);
    $this->_tag_service       = CService::getTagService($this->group_id);
    $this->_tag_chambre       = CChambre::getTagChambre($this->group_id);
    $this->_tag_lit           = CLit::getTagLit($this->group_id);
    $this->_tag_movement      = CMovement::getTagMovement($this->group_id);
    $this->_tag_visit_number  = CSmp::getTagVisitNumber($this->group_id);

    $this->_tag_hprimxml      = CHprimXML::getDefaultTag($this->group_id);
    
    if (CModule::getActive("phast")) {
      $this->_tag_phast  = CPhast::getTagPhast($this->group_id);
    }
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["messages_supported"] = "CMessageSupported object_id";
    
    return $backProps;
  }

  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", 1);
  }
  
  function loadRefUser() {}
  
  /**
   * Get exchanges sources
   * 
   * @return void
   */
  function loadRefsExchangesSources() {}

  /**
   * Sender is reachable ?
   * 
   * @return boolean reachable
   */
  function isReachable() {
    if (!$this->_ref_exchanges_sources) {
      $this->loadRefsExchangesSources();
    }
  }
  
  function lastMessage() {}
  
  function register($name) {
    $this->nom = $name;
    $this->loadMatchingObject();
    
    // Enregistrement automatique d'un destinataire ?
    if (!$this->_id) {}
  }
  
  function loadRefsMessagesSupported() {
    return $this->_ref_messages_supported = $this->loadBackRefs("messages_supported");
  }
  
  function isMessageSupported($message) {
    $msg_supported               = new CMessageSupported();
    $msg_supported->object_class = $this->_class;
    $msg_supported->object_id    = $this->_id;
    $msg_supported->message      = $message;
    $msg_supported->active       = 1;

    return $msg_supported->countMatchingList() > 0;
  }
  
  function getMessagesSupportedByFamily() {    
    $family = array();
    
    foreach (CExchangeDataFormat::getAll() as $_data_format_class) {
      $_data_format = new $_data_format_class;
      $temp = $_data_format->getFamily();
      $family = array_merge($family, $temp);
    }
    
    if (empty($family)) {
      return $this->_ref_msg_supported_family;
    }
    
    $supported = $this->loadRefsMessagesSupported();
    
    foreach($family as $_family => $_root_class) {
      $root  = new $_root_class;   

      foreach ($root->getEvenements() as $_evt => $_evt_class) {
        foreach ($supported as $_msg_supported) {
          if (!$_msg_supported->active) {
            continue;
          }
          
          if ($_msg_supported->message != $_evt_class) {
            continue;
          }
          
          $messages = $this->_spec->messages;
          if (isset($messages[$root->type])) {
            $this->_ref_msg_supported_family = array_merge($this->_ref_msg_supported_family, $messages[$root->type]);
            continue 3;
          }
        }
      }
    }

    return $this->_ref_msg_supported_family;
  }
  
  function getFormatObjectHandlers() {
    return array();
  }
}

?>