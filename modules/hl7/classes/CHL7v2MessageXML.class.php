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
  var $_ref_exchange_ihe = null;
  var $_ref_sender       = null;
  var $_ref_receiver     = null;
  var $_is_i18n          = null;
  
  static function getEventType($event_code = null, $encoding = "utf-8") {
    switch ($event_code) {
      // Création d'un nouveau patient - Mise à jour d'information du patient
      case "CHL7v2EventADTA28" : 
      case "CHL7v2EventADTA28_FR" :   
      case "CHL7v2EventADTA31" :
      case "CHL7v2EventADTA31_FR" :
        return new CHL7v2RecordPerson($encoding);
      // Fusion de deux patients
      case "CHL7v2EventADTA40" : 
      case "CHL7v2EventADTA40_FR" : 
        return new CHL7v2MergePersons($encoding);
      // Changement de la liste d'identifiants du patient
      case "CHL7v2EventADTA47" : 
      case "CHL7v2EventADTA47_FR" : 
        return new CHL7v2ChangePatientIdentifierList($encoding);
      // Création d'une venue - Mise à jour d'information de la venue
      case "CHL7v2EventADTA01" :
      case "CHL7v2EventADTA01_FR" : 
      case "CHL7v2EventADTA02" :
      case "CHL7v2EventADTA02_FR" :
      case "CHL7v2EventADTA03" :
      case "CHL7v2EventADTA03_FR" :
      case "CHL7v2EventADTA04" :
      case "CHL7v2EventADTA04_FR" :
      case "CHL7v2EventADTA05" :
      case "CHL7v2EventADTA05_FR" :
      case "CHL7v2EventADTA06" :
      case "CHL7v2EventADTA06_FR" :
      case "CHL7v2EventADTA07" :
      case "CHL7v2EventADTA07_FR" :
      case "CHL7v2EventADTA08" :  
      case "CHL7v2EventADTA11" :
      case "CHL7v2EventADTA11_FR" :
      case "CHL7v2EventADTA12" :
      case "CHL7v2EventADTA12_FR" :
      case "CHL7v2EventADTA13" :
      case "CHL7v2EventADTA13_FR" :
      case "CHL7v2EventADTA14" :
      case "CHL7v2EventADTA14_FR" :
      case "CHL7v2EventADTA16" :
      case "CHL7v2EventADTA16_FR" :
      case "CHL7v2EventADTA25" :
      case "CHL7v2EventADTA25_FR" :  
      case "CHL7v2EventADTA38" :
      case "CHL7v2EventADTA38_FR" :  
      case "CHL7v2EventADTA44" :
      case "CHL7v2EventADTA44_FR" :
      case "CHL7v2EventADTA54" :
      case "CHL7v2EventADTA54_FR" :
      case "CHL7v2EventADTA55" : 
      case "CHL7v2EventADTA55_FR" :
      case "CHL7v2EventADTZ80_FR" : 
      case "CHL7v2EventADTZ81_FR" : 
      case "CHL7v2EventADTZ84_FR" :
      case "CHL7v2EventADTZ85_FR" : 
      case "CHL7v2EventADTZ99_FR" : 
        return new CHL7v2RecordAdmit($encoding);  
      // Création des résultats d'observations
      case "CHL7v2EventORUR01" : 
        return new CHL7v2RecordObservationResultSet($encoding);  
      default : 
        return new CHL7v2MessageXML($encoding);
    }
  }
  
  function __construct($encoding = "utf-8") {
    parent::__construct($encoding);

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
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
    
    if ($contextNode)
      return $xpath->query($nodeName, $contextNode);
    else 
      return $xpath->query($nodeName);
  }
  
  function queryNode($nodeName, DOMNode $contextNode = null, &$data = null, $root = false) {
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
        
    return $data[$nodeName] = $xpath->queryUniqueNode($root ? "//$nodeName" : "$nodeName", $contextNode);
  }
  
  function queryNodes($nodeName, DOMNode $contextNode = null, &$data = null, $root = false) {    
    $nodeList = $this->query("$nodeName", $contextNode);
    foreach ($nodeList as $_node) {
      $data[$nodeName][] = $_node;
    }
    
    return $nodeList;
  }
  
  function queryTextNode($nodeName, DOMNode $contextNode, $root = false) {
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
    
    return $xpath->queryTextNode($nodeName, $contextNode);
  }
  
  function getSegment($name, $data, $object) {
    if (!array_key_exists($name, $data) || $data[$name] === NULL) {
      return;
    }
    
    $function = "get$name";
    
    $this->$function($data[$name], $object);
  }
  
  function getMSHEvenementXML() {
    $data = array();
    
    $MSH = $this->queryNode("MSH", null, $foo, true);
    
    $data['dateHeureProduction'] = mbDateTime($this->queryTextNode("MSH.7/TS.1", $MSH));
    $data['identifiantMessage']  = $this->queryTextNode("MSH.10", $MSH);
    
    return $data;
  }
  
  function getPIIdentifier(DOMNode $node, &$data) {
    if (CHL7v2Message::$handle_mode == "simple") {
      $data["PI"] = $this->queryTextNode("CX.1", $node);
    } 
    else {
      if ($this->queryTextNode("CX.5", $node) == "PI") {
        $data["PI"] = $this->queryTextNode("CX.1", $node);
      }
    }
    
  }
  
  function getANIdentifier(DOMNode $node, &$data) {
    if (CHL7v2Message::$handle_mode == "simple") {
      $data["AN"] = $this->queryTextNode("CX.1", $node);
    } 
    else {
      if ($this->queryTextNode("CX.5", $node) == "AN") {
        $data["AN"] = $this->queryTextNode("CX.1", $node);
      }
    }
  }
  
  function getVNIdentifiers(DOMNode $node, &$data, CInteropSender $sender) {
    if (($this->queryTextNode("CX.5", $node) == "VN")) {
      $data["VN"] = $this->queryTextNode("CX.1", $node);
    }
  }
  
  function getRIIdentifiers(DOMNode $node, &$data, CInteropSender $sender) {
    // Notre propre RI
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
      $data["RI"] = $this->queryTextNode("CX.1", $node);
      return;
    }

    // RI de l'expéditeur
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == $sender->_configs["assigning_authority_universal_id"])) {
      $data["RI_Sender"] = $this->queryTextNode("CX.1", $node);
      return;
    }
    
    // RI des autres systèmes
    if (($this->queryTextNode("CX.5", $node) == "RI")) {
      $data["RI_Others"] = $this->queryTextNode("CX.1", $node);
    }
  }
  
  function getNPAIdentifiers(DOMNode $node, &$data) {
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
      $data["NPA"] = $this->queryTextNode("CX.1", $node);
    }
  }
  
  function getPersonIdentifiers($nodeName, DOMNode $contextNode, CInteropSender $sender) {
    $data = array();
    
    foreach ($this->query($nodeName, $contextNode) as $_node) {
      // RI - Resource identifier 
      $this->getRIIdentifiers($_node, $data, $sender);
      
      // PI - Patient internal identifier
      $this->getPIIdentifier($_node, $data);
      
      // INS-C - Identifiant national de santé calculé
      if ($this->queryTextNode("CX.5", $_node) == "INS-C") {
        $data["INSC"] = $this->queryTextNode("CX.1", $_node);
      } 
      
      // SS - Numéro de Sécurité Social
      if ($this->queryTextNode("CX.5", $_node) == "SS") {
        $data["SS"] = $this->queryTextNode("CX.1", $_node);
      } 
    }
    
    // AN - Patient Account Number (NDA)
    if ($PID_18 = $this->queryNode("PID.18", $contextNode)) {
      $this->getANIdentifier($PID_18, $data);
    }    
 
    return $data;
  }
  
  function getAdmitIdentifiers(DOMNode $contextNode, CInteropSender $sender) {
    $data = array();
    
    // RI - Resource identifier 
    // VN - Visit Number
    // AN - On peut également retrouver le numéro de dossier dans ce champ
    if ($PV1_19 = $this->queryNode("PV1.19", $contextNode)) {
      switch ($sender->_configs["handle_NDA"]) {
        case 'PV1_19':
          $this->getANIdentifier($PV1_19, $data);
          break;
        default:
          // RI - Resource Identifier
          $this->getRIIdentifiers($PV1_19, $data, $sender);

          // VN - Visit Number
          $this->getVNIdentifiers($PV1_19, $data, $sender);
    
          break;
      }
    }
      
    // PA - Preadmit Number
    if ($PV1_5 = $this->queryNode("PV1.5", $contextNode)) {
      $this->getNPAIdentifiers($PV1_5, $data);
    }
        
    return $data;
  }
  
  function getContentNodes() {
    $data  = array();
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();
   
    $this->_ref_sender = $sender;
    
    $this->queryNode("EVN", null, $data, true);
    
    $PID = $this->queryNode("PID", null, $data, true);
    
    $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);

    $this->queryNode("PD1", null, $data, true);
    
    return $data;
  }
  
  function getBoolean($value) {
    return ($value == "Y") ? 1 : 0;
  }
  
  function getPhone($string) {
    return preg_replace("/[^0-9]/", "", $string);
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {}
}

?>