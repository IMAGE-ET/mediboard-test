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
CAppUI::requireSystemClass('mbMetaObject');

class CDocumentItem extends CMbMetaObject {
  
	// DB Fields
  var $file_category_id  = null;
  var $etat_envoi = null;
  
  // Derivated fields
  var $_extensioned = null;

  // Distant field
  var $_send_problem = null;
  
  // Behavior Field
  var $_send = null; 
  
  // References
  var $_ref_category = null;
	
  function getProps() {
    $specs = parent::getProps();
    $specs["file_category_id"] = "ref class|CFilesCategory";
    $specs["etat_envoi"]       = "enum notNull list|oui|non|obsolete default|non";

    $specs["_extensioned"]     = "str notNull";
    $specs["_send_problem"]    = "text";
    return $specs;
  }
  
  /**
   * Retrieve content as binary data
   * @return binary Content
   */
  function getContent() {
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
  }
  
  function loadRefCategory() {
    $this->_ref_category = $this->loadFwdRef("file_category_id", true);
  }
}

?>