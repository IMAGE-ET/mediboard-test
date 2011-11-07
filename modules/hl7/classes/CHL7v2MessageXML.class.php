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
  
  static function getEventType($event_code = null) {
    switch ($event_code) {
      // Création d'un nouveau patient - Mise à jour d'information du patient
      case "CHL7v2EventADTA28" : 
      case "CHL7v2EventADTA31" :
        return new CHL7v2RecordPerson();
      // Fusion de deux patients
      case "CHL7v2EventADTA40" : 
        return new CHL7v2MergePersons();
      default : 
        return new CHL7v2MessageXML();
    }
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
  
  function query($nodeName, $contextNode = null) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    return $xpath->query($nodeName, $contextNode);
  }
  
  function queryNode($nodeName, $contextNode = null, &$data = null) {
    $xpath = new CHL7v2MessageXPath($this);   

    return $data[$nodeName] = $xpath->queryUniqueNode("//$nodeName", $contextNode);
  }
  
  function queryNodes($nodeName, $contextNode = null, &$data = null) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    $nodeList = $xpath->query("//$nodeName");
    foreach ($nodeList as $_node) {
      $data[$nodeName][] = $_node;
    }
  }
  
  function queryTextNode($nodeName, $contextNode) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    return $xpath->queryTextNode($nodeName, $contextNode);
  }
  
  function getMSHEvenementXML() {
    $data = array();
    
    $MSH = $this->queryNode("MSH");
    
    $data['dateHeureProduction'] = mbDateTime($this->queryTextNode("MSH.7/TS.1", $MSH));
    $data['identifiantMessage']  = $this->queryTextNode("MSH.10", $MSH);
    
    return $data;
  }
  
  function getPatientIdentifiers(DOMNode $node) {
    $data = array();
    
    // PID/PID.3
    foreach ($this->query("PID.3", $node) as $_PID3) {
      // RI - Resource identifier 
      if (($this->queryTextNode("CX.5", $_PID3) == "RI") && 
          ($this->queryTextNode("CX.4/HD.2", $_PID3) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
        $data["RI"] = $this->queryTextNode("CX.1", $_PID3);
      }
      // PI - Patient internal identifier
      if ($this->queryTextNode("CX.5", $_PID3) == "PI") {
        $data["PI"] = $this->queryTextNode("CX.1", $_PID3);
      }
      // INS-C - Identifiant national de santé calculé
      if ($this->queryTextNode("CX.5", $_PID3) == "INS-C") {
        $data["INSC"] = $this->queryTextNode("CX.1", $_PID3);
      }
    }
   
    return $data;
  }
  
  function getContentNodes() {
    $data  = array();
    
    $data["PID"] = $PID = $this->queryNode("PID", null, $data);
    
    $data["patientIdentifiers"] = $this->getPatientIdentifiers($PID);
    
    $data["PD1"] = $this->queryNode("PD1", null, $data);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {}
}

?>