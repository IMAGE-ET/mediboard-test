<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Files
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CDocumentItem class
 */
class CDocumentItem extends CMbMetaObject {
  public $file_category_id;
  
  public $etat_envoi;
  public $author_id;
  
  // Derivated fields
  public $_extensioned;

  public $_send_problem;

  // Behavior Field
  public $_send; 
  public $_is_editable = true;

  /** @var CMediusers */
  public $_ref_author;

  /** @var CFilesCategory */
  public $_ref_category;
  
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
   *
   * @return string Binary Content
   */
  function getBinaryContent() {
  }

  /**
   * Retrieve extensioned like file name
   *
   * @return string Binary Content
   */
  function getExtensioned() {
    return $this->_extensioned;
  }

  /**
   * Try and instanciate document sender according to module configuration
   *
   * @return CDocumentSender sender or null on error
   */
  static function getDocumentSender() {
    if (null == $system_sender = CAppUI::conf("dPfiles system_sender")) {
      return null;
    }
    
    if (!is_subclass_of($system_sender, "CDocumentSender")) {
      trigger_error("Instanciation du Document Sender impossible.");
      return null;
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
   *
   * @return string Store-like problem message
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
   *
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
        if (!$sender->send($this)) {
          return "Erreur lors de l'envoi.";
        }
        CAppUI::setMsg("Document transmis.");
        break;
      case "oui" :
        if (!$sender->cancel($this)) {
          return "Erreur lors de l'invalidation de l'envoi.";
        }
        CAppUI::setMsg("Document annulé."); 
        break;
      case "obsolete" :
        if (!$sender->resend($this)) {
          return "Erreur lors du renvoi.";
        }
        CAppUI::setMsg("Document annulé/transmis.");
        break;
      default:
        return "Fonction d'envoi '$this->etat_envoi' non reconnue.";
    }
  }  
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefCategory();
    $this->loadRefAuthor();
  }

  /**
   * @return CFilesCategory
   */
  function loadRefCategory() {
    return $this->_ref_category = $this->loadFwdRef("file_category_id", true);
  }

  /**
   * @return CMediusers
   */
  function loadRefAuthor() {
    if (!$this->_id) {
      return null;
    }
    
    return $this->_ref_author = $this->loadFwdRef("author_id", true);
  }
  
  function canRead() {
    if (!$this->private) {
      return parent::canRead();
    }
    
    $author = $this->loadRefAuthor();

    global $can;
    return $this->_canRead = ($author->function_id == CAppUI::$user->function_id || $can->admin) && $this->getPerm(PERM_READ);
  }
  
  /**
   * Load aggregated doc item ownership
   *
   * @return array collection of arrays with docs_count, docs_weight and author_id keys
   */
  function getUsersStats() {
    return array();
  }
  
  /**
   * Load details doc item ownership for a user collection
   *
   * @param array $user_ids ID collection of CUser
   *
   * @return array collection of arrays with docs_count, docs_weight, object_class and category_id keys
   */
  function getUsersStatsDetails($user_ids) {
    return array();
  }
}
