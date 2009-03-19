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
  
  var $clientSOAP = null;
  
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
    
    if (isset($mbObject->_coms_from_sip) && ($mbObject->_coms_from_sip == 1)) {
    	return;
    }
    
    $this->initClientSOAP();
    
    $domEvenement      = new CHPrimXMLEvenementsPatients();
    $messageEvtPatient = $domEvenement->generateEvenementsPatients($mbObject);

    // Récupère le message d'acquittement après l'execution la methode evenementPatient
    if (null == $acquittement = $this->clientSOAP->evenementPatient($messageEvtPatient)) {
      return;
    }
  }

  function onMerge(CMbObject &$mbObject) {
    $this->onStore($mbObject);
  }
  
  function onDelete(CMbObject &$mbObject) {
  } 
  
  function initClientSOAP () {
    $rooturl = CAppUI::conf('sip soap rooturl');

		if (preg_match('#\%u#', $rooturl)) 
		  $rooturl = str_replace('%u', CAppUI::conf('sip soap user'), $rooturl);
		
		if (preg_match('#\%p#', $rooturl)) 
		  $rooturl = str_replace('%p', CAppUI::conf('sip soap pass'), $rooturl);

    if ($this->clientSOAP instanceof SoapClient) 
      return;
 
    if (!$this->clientSOAP = new CMbSOAPClient($rooturl)) {
      trigger_error("Instanciation du SoapClient impossible.");
    }
  }  
}
?>