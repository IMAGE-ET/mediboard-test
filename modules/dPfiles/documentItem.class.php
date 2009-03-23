<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision: $
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
  
  function updateFormFields() {
  	parent::updateFormFields();
  	$system_sender = CAppUI::conf("dPfiles system_sender");

  	if ($system_sender && !is_subclass_of($system_sender, "CDocumentSender")) {
       trigger_error("Instanciation du Document Sender impossible.");
    }    
  	if($system_sender) {
  		$sender = new $system_sender;
  		$this->_is_sendable = $sender->isSendable($this);
  	}
  }
  
  function store() {
  	$this->completeField("etat_envoi");
    $this->completeField("object_class");
    $this->completeField("object_id");
  	
    if ($this instanceof CCompteRendu) {
    	$this->completeField("nom");
    	$this->completeField("source");
    }
    
    if ($this instanceof CFile) {
    	$this->completeField("file_name");
    	$this->completeField("file_real_filename");
    	$this->completeField("file_type");
    	$this->completeField("file_date");
    	$this->completeField("_file_path");
    }   

    $this->handleSend();

    return parent::store();
  }
  
  function handleSend() {
  	global $AppUI;
    if (!$this->_send) {
      return;
    }
    
    $this->_send = false;
    
    $system_sender = CAppUI::conf("dPfiles system_sender");
    
    if ($system_sender && !is_subclass_of($system_sender, "CDocumentSender")) {
       trigger_error("Instanciation du Document Sender impossible.");
    }
    
    $sender = new $system_sender;
    
    switch($this->etat_envoi) {
    	case "non" :
    	  ($sender->send($this)) ? $AppUI->setMsg("Document transmis.") : $AppUI->setMsg("Erreur lors de l'envoi.", UI_MSG_ERROR);
        break;
      case "oui" :
      	($sender->cancel($this)) ? $AppUI->setMsg("Document annulé.") : $AppUI->setMsg("Erreur lors de l'invalidation.", UI_MSG_ERROR);
        break;
      case "obsolete" :
      	($sender->resend($this)) ? $AppUI->setMsg("Document annulé/transmis.") : $AppUI->setMsg("Erreur lors de l'invalidation / envoi.", UI_MSG_ERROR);
        break;
      default:
      	$AppUI->setMsg("Fonction non reconnue.", UI_MSG_ERROR);
    }
  }  
  
  
}

?>