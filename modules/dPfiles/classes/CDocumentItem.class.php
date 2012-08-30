<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision$
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CDocumentItem class
 */

class CDocumentItem extends CMbMetaObject {
  
  // DB Fields
  var $file_category_id  = null;
  var $etat_envoi        = null;
  var $author_id         = null;
  
  // Derivated fields
  var $_extensioned      = null;

  // Distant field
  var $_send_problem     = null;
  var $_ref_author       = null;

  // Behavior Field
  var $_send             = null; 
  var $_is_editable      = true;
  // References
  var $_ref_category     = null;
  
  function getProps() {
    $props = parent::getProps();
    
    $props["file_category_id"] = "ref class|CFilesCategory";
    $props["etat_envoi"]       = "enum notNull list|oui|non|obsolete default|non";
    $props["author_id"]        = "ref class|CMediusers";
    
    $props["_extensioned"]     = "str notNull";
    $props["_send_problem"]    = "text";

    return $props;
  }
  
  /**
   * Retrieve content as binary data
   * @return binary Content
   */
  function getBinaryContent() {
  }

  /**
   * Retrieve extensioned like file name
   * @return binary Content
   */
  function getExtensioned() {
    return $this->_extensioned;
  }

  /**
   * Try and instanciate document sender according to module configuration
   * @return CDocumentSender sender or null on error
   */
  static function getDocumentSender() {
    if (null == $system_sender = CAppUI::conf("dPfiles system_sender")) {
      return;
    }
    
    if (!is_subclass_of($system_sender, "CDocumentSender")) {
      trigger_error("Instanciation du Document Sender impossible.");
      return;
    }
    
    return new $system_sender;
  }
  
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->getSendProblem();
    $this->loadRefCategory();
  }
  
  /**
   * Retrieve send problem user friendly message
   * @return string message Store-like problem message
   */
  function getSendProblem() {
    if ($sender = self::getDocumentSender()) {
       $this->_send_problem = $sender->getSendProblem($this);
     }
  }
  
  function store() {
    $this->completeField("etat_envoi");
    $this->completeField("object_class");
    $this->completeField("object_id");

    if ($msg = $this->handleSend()) {
      return $msg;
    }

    return parent::store();
  }
  
  /**
   * Handle document sending store behaviour
   * @return string Store-like error message 
   */
  function handleSend() {
    if (!$this->_send) {
      return;
    }
    
    $this->_send = false;
    
    if (null == $sender = self::getDocumentSender()) {
      return "Document Sender not available";
    }
    
    switch ($this->etat_envoi) {
      case "non" :
        if (!$sender->send($this)) return "Erreur lors de l'envoi.";
        CAppUI::setMsg("Document transmis.");
        break;
      case "oui" :
        if (!$sender->cancel($this)) return "Erreur lors de l'invalidation de l'envoi."; 
        CAppUI::setMsg("Document annulé."); 
        break;
      case "obsolete" :
        if (!$sender->resend($this)) return "Erreur lors du renvoi."; 
        CAppUI::setMsg("Document annulé/transmis.");
        break;
      default:
        return "Fonction d'envoi '$this->etat_envoi' non reconnue.";
    }
  }  
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefCategory();
    $this->_ref_author = $this->loadFwdRef("author_id");
  }
  
  function loadRefCategory() {
    return $this->_ref_category = $this->loadFwdRef("file_category_id", true);
  }
  
  function loadRefAuthor() {
    if (!$this->_id) {
      return;
    }
    
    return $this->_ref_author = CMediusers::get($this->author_id);
  }
  
  function canRead() {
    if (!$this->private) {
      return parent::canRead();
    }
    
    $this->loadRefAuthor();

    global $can;
    return $this->_canRead = ($this->_ref_author->function_id == CAppUI::$user->function_id || $can->admin) && $this->getPerm(PERM_READ);
  }
  
  /**
   * Load aggregated doc item ownership
   * @return array collection of arrays with 
   *   docs_count, docs_weight and author_id keys
   */
  function getUsersStats() {
    return array();
  }
  
  /**
   * Load details doc item ownership for a user collection
   * @param  array ID collection of CUser
   * @return array collection of arrays with 
   *   docs_count, docs_weight, object_class and category_id keys
   */
  function getUsersStatsDetails($user_ids) {
    return array();
  }
}

?>