<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 10912 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("eai", "CEAIOperator");

/**
 * The COperatorHprimXML class
 */

class COperatorHprimXML extends CEAIOperator {
  function event(CExchangeDataFormat $data_format) {
    $msg    = $data_format->_message;
    $data   = array();
    
    $dom_evenement = $data_format->_family_message->getHPrimXMLEvenements($msg);
    
    $supported = false;
    $dom_evenement_class_name = get_class($dom_evenement);
    if (in_array($dom_evenement_class_name, $data_format->_messages_supported_class)) {
      $supported = true;
    }

    if (!$supported) {
      throw new CMbException(CAppUI::tr("CEAIDispatcher-no_message_supported_for_this_actor", $dom_evenement_class_name));
    }
    mbTrace($data_format, "data_format", true);
    
    // Récupération des informations du message XML
    $dom_evenement->loadXML($msg);
    $doc_errors = $dom_evenement->schemaValidate(null, true);
    
    // Récupération du noeud racine
    $root = $dom_evenement->documentElement;
    $nodeName = $root->nodeName;
    
    $data = $dom_evenement->getEnteteEvenementXML($nodeName);
    
    mbTrace($data, "data", true);
  }
  
  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare facility.
   * @param CHPrimXMLEvenementsPatients messagePatient
   * @return CHPrimXMLAcquittementsPatients messageAcquittement 
   **/
  function eventPatient(CExchangeDataFormat $data_format) {
    $msgAcq = null;
  }
}
?>