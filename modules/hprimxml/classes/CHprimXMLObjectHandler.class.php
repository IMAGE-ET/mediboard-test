<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 12588 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHprimXMLObjectHandler {
  static $handled = array ();
  
  function generateTypeEvenement($evenement, CMbObject $mbObject, $referent = null, $initiateur = null) {
    $receiver = $mbObject->_receiver;
    
    if (!$receiver->isMessageSupported($evenement)) {
      return;
    }
    
    $dom = new $evenement;
    $dom->_ref_receiver = $receiver;
    $dom->generateTypeEvenement($mbObject, true, $initiateur);
  }
  
  function sendEvenementPatient($evenement, CMbObject $mbObject, $referent = null, $initiateur = null) {
    $receiver = $mbObject->_receiver;
    
    if (!$receiver->isMessageSupported($evenement)) {
      return;
    }
    
    $dom = new $evenement;
    $dom->_ref_receiver = $receiver;
    $receiver->sendEvenementPatient($dom, $mbObject);
  }
  
  function sendEvenementPMSI($evenement, CMbObject $mbObject) {
    $receiver = $mbObject->_receiver;
    
    if (!$receiver->isMessageSupported($evenement)) {
      return;
    }
    
    $dom = new $evenement;
    $dom->_ref_receiver = $receiver;
    $receiver->sendEvenementPMSI($dom, $mbObject);
  }
}