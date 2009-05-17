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
  var $etat_envoi = null;
  
  // Behavior Field
  var $_send = null; 
  var $_is_sendable = null;
	
  function getProps() {
    $specs = parent::getProps();
    $specs["etat_envoi"] = "enum notNull list|oui|non|obsolete default|non";
    return $specs;
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

  	if ($sender = self::getDocumentSender()) {
   		$this->_is_sendable = $sender->isSendable($this);
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
      	if (!$sender->cancel($this)) return "Erreur lors de l'invalidation."; 
      	CAppUI::setMsg("Document annulé."); 
        break;
      case "obsolete" :
        if (!$sender->resend($this)) return "Erreur lors de l'invalidation / envoi."; 
        CAppUI::setMsg("Document annulé/transmis.");
        break;
      default:
      	return "Fonction d'envoi '$this->etat_envoi' non reconnue.";
    }
  }  
  
  
}

?>