<?php /* $Id:$ */

/**
 * Message XML HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2MessageXML 
 * Message XML HL7
 */

class CHL7v2MessageXML extends CMbXMLDocument implements CHL7MessageXML {
  var $_ref_exchange_ihe     = null;
  
  static function getEventType($event_name) {
    return new CHL7v2RecordPatient();
    return new CHL7v2MessageXML();
  }
  
  function __construct() {
    parent::__construct("utf-8");

    $this->formatOutput = true;
  }
  
  function addNameSpaces($name) {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns", "urn:hl7-org:v2xml");
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "urn:hl7-org:v2xml $name.xsd");
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:hl7-org:v2xml") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }
  
  function getMSHEvenementXML() {
    $data = array();
    
    $xpath = new CHL7v2MessageXPath($this);   
    $MSH = $xpath->queryUniqueNode("//MSH");
    
    $data['dateHeureProduction'] = mbDateTime($xpath->queryTextNode("MSH.7/TS.1", $MSH));
    $data['identifiantMessage']  = $xpath->queryTextNode("MSH.10", $MSH);
    
    return $data;
  }
  
  function getContentsXML() {
    $data = array();

    $xpath = new CHL7v2MessageXPath($this);
    
    $data["PID"] = $PID = $xpath->queryUniqueNode("//PID");
    
    $data["patientIdentifiers"] = $this->getPatientIdentifiers($PID);
    
    $data["PD1"] = $PD1 = $xpath->queryUniqueNode("//PD1");
  }
  
  function mergePersons(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    
    
  }
}

?>