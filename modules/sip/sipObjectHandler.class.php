<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CSipObjectHandler 
 * @abstract Event handler class for CMbObject
 */

class CSipObjectHandler extends CMbObjectHandler {
  static $handled = array ("CPatient");
    
  static function isHandled(CMbObject &$mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }
  
  function onStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
    if (!$mbObject->_ref_last_log) {
    	return;
    }
    
    // Passe pas dans le handler lors d'une notification 
    if (isset($mbObject->_coms_from_sip) && ($mbObject->_coms_from_sip == 1)) {
    	return;
    }

    $dest_hprim = new CDestinataireHprim();
    $dest_hprim->type = "sip";
    $dest_hprim->loadMatchingObject();
    
    $domEvenement = new CHPrimXMLEvenementsPatients();
    $domEvenement->_emetteur = CAppUI::conf('mb_id');
    $domEvenement->_destinataire = $dest_hprim->destinataire;
    $messageEvtPatient = $domEvenement->generateEvenementsPatients($mbObject);

    if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password)) {
      trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
    }
    
    // Récupère le message d'acquittement après l'execution la methode evenementPatient
    if (null == $acquittement = $client->evenementPatient($messageEvtPatient)) {
    	trigger_error("Notification d'evenement patient impossible sur le CIP : ".$dest_hprim->url);
    }

    $echange_hprim = new CEchangeHprim();
    $echange_hprim->load($domEvenement->_identifiant);
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->acquittement = $acquittement;
    
    $echange_hprim->store();
  }

  function onMerge(CMbObject &$mbObject) {
    $this->onStore($mbObject);
  }
  
  function onDelete(CMbObject &$mbObject) {
  } 
}
?>