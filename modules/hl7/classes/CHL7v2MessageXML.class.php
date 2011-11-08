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
  
  function query($nodeName, DOMNode $contextNode = null) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    return $xpath->query($nodeName, $contextNode);
  }
  
  function queryNode($nodeName, DOMNode $contextNode = null, &$data = null) {
    $xpath = new CHL7v2MessageXPath($this);   

    return $data[$nodeName] = $xpath->queryUniqueNode("//$nodeName", $contextNode);
  }
  
  function queryNodes($nodeName, DOMNode $contextNode = null, &$data = null) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    $nodeList = $xpath->query("//$nodeName");
    foreach ($nodeList as $_node) {
      $data[$nodeName][] = $_node;
    }
  }
  
  function queryTextNode($nodeName, DOMNode $contextNode) {
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
  
  function getPIIdentifier(DOMNode $node, &$data) {
    if ($this->queryTextNode("CX.5", $node) == "PI") {
      $data["PI"] = $this->queryTextNode("CX.1", $node);
    }
  }
  
  function getANIdentifier(DOMNode $node, &$data) {
    if ($this->queryTextNode("CX.5", $node) == "AN") {
      $data["AN"] = $this->queryTextNode("CX.1", $node);
    }
  }
  
  function getRIIdentifiers(DOMNode $node, &$data) {
    /* @todo On gère que l'identifiant de MB dans un premier temps */
    
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
      $data["RI"] = $this->queryTextNode("CX.1", $node);
    }
  }
  
  function getNPAIdentifiers(DOMNode $node, &$data) {
    
  }
  
  function getPersonIdentifiers($nodeName, DOMNode $contextNode) {
    $data = array();
    
    foreach ($this->query($nodeName, $contextNode) as $_node) {
      // RI - Resource identifier 
      $this->getRIIdentifiers($_node, $data);
      
      // PI - Patient internal identifier
      $this->getPIIdentifier($_node, $data);
      
      // INS-C - Identifiant national de santé calculé
      if ($this->queryTextNode("CX.5", $_node) == "INS-C") {
        $data["INSC"] = $this->queryTextNode("CX.1", $_node);
      }
    }
   
    return $data;
  }
  
  function getAdmitIdentifiers() {    
    // RI - Resource identifier 
    $this->getRIIdentifiers($_node, $data);
      
    // PA - Preadmit Number
    //$this->getNPAIdentifiers($_node, $data);
    
    // AN - Patient Account Number
    //$PID18 = $this->queryNode("//PID/PID.18");
    //$this->getANIdentifier($_node, $data);
  }
  
  function getContentNodes() {
    $data  = array();
    
    $PID = $this->queryNode("PID", null, $data);
    
    $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID);

    $this->queryNode("PD1", null, $data);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {}
}

?>