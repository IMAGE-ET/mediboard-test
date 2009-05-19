<?php
/**
 *  @package Mediboard
 *  @subpackage dPfiles
 *  @version $Revision: 6226 $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CEcDocumentSender extends CDocumentSender {
  // Non dépendant de la clinique courante ????
	// Envisager d'utiliser CMedicap::$tags
  public static $docTag = "ecap document";
  public static $catTag = "ecap type";
  
  public static $sendables = array(
	  "CPatient"            => array ("PA"),
	  "CSejour"             => array ("SJ", "AT"),
	  "COperation"          => array ("IN"),
	  "CConsultation"       => array ("PA"),
	  "CConsultationAnesth" => array ("PA"),
  );

  var $clientSOAP = null;
  
  /**
   * Instanciate Soap Client
   * @return bool Job-done value
   */
  function initClientSOAP () {
    if ($this->clientSOAP instanceof SoapClient) {
      return;
    }
    
    try {
      CMedicap::makeUrls();
      
			$serviceURL = CMedicap::$urls["soap"]["documents"];
			
			if (!url_exists($serviceURL)) {
			  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur wep inatteignable à l'adresse : $serviceURL");
			  return;
			}
			
			$this->clientSOAP = new SoapClient("$serviceURL?WSDL");
    } 
    catch (Exception $e) {
      trigger_error("Instanciation du SoapClient impossible : ".$e);
    }
  }
  
  /**
   * Get Target Id for object anf target ecap category
   * @param CMbObject $object Mediboard Object
   * @param string $ecType ecap Type : [PA|SJ|AT|IN]
   * @return string ecap identifier
   */
  static function getTargetIdFor(CMbObject $object, $ecType) {
    if ($object instanceof CPatient) {
    	;
    }
    
  }
    
  function send(CDocumentItem $docItem) {
    global $AppUI;
    
    // identifiant externe du document 
    $idDocItem = new CIdSante400();
    $idDocItem->loadLatestFor($docItem, self::$docTag);
    $idDocItem->last_update = mbDateTime(); 
    $idDocItem->id400 = "123";
    mbTrace($idDocItem->getDBFields(), "Identifiant externe du document");
    
    // identifiant externe de la catégorie
    $docItem->loadRefCategory();
    $idCategory = new CIdSante400();
    $idCategory->loadLatestFor($docItem->_ref_category, CEcDocumentSender::$catTag);
    mbTrace($idCategory->getDBFields(), "Identifiant externe de la categorie");
    
    
    // Chargement de la cible
    $docItem->loadTargetObject();
    $target = $docItem->_ref_object;    
    

    // Change l'etat du document
    $docItem->etat_envoi = "oui";

    if (!$this->initClientSOAP()) {
      return false;
    }
    
    
    return true; 
  }
  
  function cancel($docItem) {
    $this->initClientSOAP();

    // Change l'etat du document
    $docItem->etat_envoi = "non"; 
    
    return true;
  }
  
  function resend($docItem) {
    // Annulation de la transaction
    if (null == $this->cancel($docItem)) {
      return;
    }
    
    // Renvoi du document
    if (null == $this->send($docItem)) {
      return;
    }
    
    return true;
  }
  
  function isSendable(CDocumentItem $docItem) {
    $docItem->loadTargetObject();
    foreach (array_keys(self::$sendables) as $_sendable) {
      if ($docItem->_ref_object instanceOf $_sendable) {
        return true;
      }
    }
    
    return false;
    
  }
}

?>