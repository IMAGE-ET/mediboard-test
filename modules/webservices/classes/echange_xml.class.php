<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireSystemClass('mbMetaObject');

class CEchangeXML extends CMbMetaObject {
  // DB Fields
  var $group_id             = null;
  var $date_production      = null;
  var $emetteur_id          = null;
  var $identifiant_emetteur = null;
  var $destinataire_id      = null;
  var $type                 = null;
  var $sous_type            = null;
  var $date_echange         = null;
  var $message_content_id      = null;
  var $acquittement_content_id = null;
  var $statut_acquittement  = null;
  var $initiateur_id        = null;
  var $message_valide       = null;
  var $acquittement_valide  = null;
  var $id_permanent         = null;

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
  var $_ref_notifications = null;
  var $_ref_emetteur      = null;
  var $_ref_destinataire  = null;  
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["date_production"]         = "dateTime notNull";
    $props["group_id"]                = "ref notNull class|CGroups";
    $props["identifiant_emetteur"]    = "str";
    $props["type"]                    = "str";
    $props["sous_type"]               = "str";
    $props["date_echange"]            = "dateTime";
    $props["message_content_id"]      = "ref class|CContentXML show|0 cascade";
    $props["acquittement_content_id"] = "ref class|CContentXML show|0 cascade";   
    $props["statut_acquittement"]     = "str show|0";
    $props["message_valide"]          = "bool show|0";
    $props["acquittement_valide"]     = "bool show|0";
    $props["id_permanent"]            = "str";
    $props["object_id"]               = "ref class|CMbObject meta|object_class unlink";
    
    $props["_self_emetteur"]          = "bool";
    $props["_self_destinataire"]      = "bool notNull";
    $props["_observations"]           = "str";
    
    $props["_date_min"]               = "dateTime";
    $props["_date_max"]               = "dateTime";
    
    $props["_message"]                = "xml";
    $props["_acquittement"]           = "xml";
    
    return $props;
  }
   
  function loadContent() {
    $content = new CContentXML();
    $content->load($this->message_content_id);
    $this->_message = $content->content;
    
    $content = new CContentXML();
    $content->load($this->acquittement_content_id);
    $this->_acquittement = $content->content;
  }
  
  function loadRefGroups() {
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
  }
  
  function loadRefsDestinataireInterop() {
    $this->_ref_emetteur     = $this->loadFwdRef("emetteur_id");
    $this->_ref_destinataire = $this->loadFwdRef("destinataire_id");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Chargement des contents 
    $this->loadContent();
     
    $this->_self_emetteur     = $this->emetteur_id     === null;
    $this->_self_destinataire = $this->destinataire_id === null;
  }
  
  function getObservations() {}
  
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
  
  function setObjectIdClass($object_class, $object_id) {
    $this->object_id    = $object_id;
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
