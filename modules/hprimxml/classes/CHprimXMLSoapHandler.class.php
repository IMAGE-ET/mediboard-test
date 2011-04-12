<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 10912 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("eai", "eai_soap_handler");

/**
 * The CHprimSoapHandler class
 */
class CHprimXMLSoapHandler extends CEAISoapHandler {
  static $paramSpecs = array(
    
  );
  
  function event($data, CInteropSender $actor) {
    
  }
  
  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare facility.
   * @param CHPrimXMLEvenementsPatients messagePatient
   * @return CHPrimXMLAcquittementsPatients messageAcquittement 
   **/
  function eventPatient($messagePatient) {
    
  }
}
?>