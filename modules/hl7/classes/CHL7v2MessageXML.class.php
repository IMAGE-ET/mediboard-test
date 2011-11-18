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
      // Création d'une venue - Mise à jour d'information de la venue
      case "CHL7v2EventADTA01" : 
      case "CHL7v2EventADTA02" :
      case "CHL7v2EventADTA03" :
      case "CHL7v2EventADTA04" :
      case "CHL7v2EventADTA05" :
      case "CHL7v2EventADTA06" :
      case "CHL7v2EventADTA07" :
      case "CHL7v2EventADTA11" :
      case "CHL7v2EventADTA12" :
      case "CHL7v2EventADTA13" :
      case "CHL7v2EventADTA38" :
      case "CHL7v2EventADTA44" :
      case "CHL7v2EventADTA54" :
      case "CHL7v2EventADTA55" : 
      case "CHL7v2EventADTZ99" : 
        return new CHL7v2RecordAdmit();     
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

    return $data[$nodeName] = $xpath->queryUniqueNode("$nodeName", $contextNode);
  }
  
  function queryNodes($nodeName, DOMNode $contextNode = null, &$data = null) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    $nodeList = $xpath->query("$nodeName");
    foreach ($nodeList as $_node) {
      $data[$nodeName][] = $_node;
    }
    
    return $nodeList;
  }
  
  function queryTextNode($nodeName, DOMNode $contextNode) {
    $xpath = new CHL7v2MessageXPath($this);   
    
    return $xpath->queryTextNode($nodeName, $contextNode);
  }
  
  function getMSHEvenementXML() {
    $data = array();
    
    $MSH = $this->queryNode("//MSH");
    
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
    $PID_18 = $this->queryNode("PID.18", $node);
    
    if ($this->queryTextNode("CX.5", $PID_18) == "AN") {
      $data["AN"] = $this->queryTextNode("CX.1", $PID_18);
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
    $PV1_5 = $this->queryNode("PV1.5", $node);
    /* @todo On gère que l'identifiant de MB dans un premier temps */
    
    if (($this->queryTextNode("CX.5", $PV1_5) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $PV1_5) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
      $data["RI"] = $this->queryTextNode("CX.1", $PV1_5);
    }
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
  
  function getAdmitIdentifiers(DOMNode $nodePID, DOMNode $nodePV1) {    
    $data = array();
    
    // RI - Resource identifier 
    $PV1_19 = $this->queryNode("PV1.19", $nodePV1);
    $this->getRIIdentifiers($PV1_19, $data);
      
    // PA - Preadmit Number
    $this->getNPAIdentifiers($nodePV1, $data);
    
    // AN - Patient Account Number
    $this->getANIdentifier($nodePID, $data);
    
    return $data;
  }
  
  function getContentNodes() {
    $data  = array();
    
    $PID = $this->queryNode("//PID", null, $data);
    
    $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID);

    $this->queryNode("//PD1", null, $data);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {}
}

?>