<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2MessageXML 
 * Message XML HL7
 */
class CHL7v2MessageXML extends CMbXMLDocument {
  /**
   * @var CExchangeIHE
   */
  var $_ref_exchange_ihe = null;
  /**
   * @var CInteropSender
   */
  var $_ref_sender       = null;
  /**
   * @var CInteropReceiver
   */
  var $_ref_receiver     = null;
  /**
   * @var string
   */
  var $_is_i18n          = null;

  /**
   * Get event
   *
   * @param string $event_name Event name
   * @param string $encoding   Encoding
   *
   * @return CHL7v2MessageXML
   */
  static function getEventType($event_name = null, $encoding = "utf-8") {
    if (!$event_name) {
      return new CHL7v2MessageXML($encoding);
    }
    list($event_type, $event_code) = str_split($event_name, strlen("CHL7vXEventXXX"));
    $event_code = substr($event_code, 0, 3);

    if ($event_type == "CHL7v2EventADT") {
      // Création d'un nouveau patient - Mise à jour d'information du patient
      if (CMbArray::in($event_code, CHL7v2RecordPerson::$event_codes)) {
        return new CHL7v2RecordPerson($encoding);
      }
      
      // Fusion de deux patients
      if (CMbArray::in($event_code, CHL7v2MergePersons::$event_codes)) {
        return new CHL7v2MergePersons($encoding);
      }
      
      // Changement de la liste d'identifiants du patient
      if (CMbArray::in($event_code, CHL7v2ChangePatientIdentifierList::$event_codes)) {
        return new CHL7v2ChangePatientIdentifierList($encoding);
      }  
      
      // Création d'une venue - Mise à jour d'information de la venue
      if (CMbArray::in($event_code, CHL7v2RecordAdmit::$event_codes)) {
        return new CHL7v2RecordAdmit($encoding);
      }  
    }    
    
    // Création des résultats d'observations  
    if ($event_type == "CHL7v2EventORU") {
      return new CHL7v2RecordObservationResultSet($encoding);  
    }
    
    // Création des consultations
    if ($event_type == "CHL7v2EventSIU") {
      return new CHL7v2RecordAppointment($encoding);  
    }

    // Récupération des résultats du PDQ
    if ($event_type == "CHL7v2EventQBP") {
      // Analyse d'une réponse reçu après une requête
      if (CMbArray::in($event_code, CHL7v2ReceivePatientDemographicsResponse::$event_codes)) {
        return new CHL7v2ReceivePatientDemographicsResponse($encoding);
      }

      // Produire une réponse sur une requête
      if (CMbArray::in($event_code, CHL7v2GeneratePatientDemographicsResponse::$event_codes)) {
        return new CHL7v2GeneratePatientDemographicsResponse($encoding);
      }
    }

    // Récupération des résultats du QCN
    if ($event_type == "CHL7v2EventQCN") {
      // Suppression d'une requête
      if (CMbArray::in($event_code, CHL7v2CancelPatientDemographicsQuery::$event_codes)) {
        return new CHL7v2CancelPatientDemographicsQuery($encoding);
      }
    }

    if ($event_type == "CHL7v2EventORM") {
      // Analyse d'une réponse reçu après une requête
      if (CMbArray::in($event_code, CHL7v2ReceiveOrderMessage::$event_codes)) {
        return new CHL7v2ReceiveOrderMessage($encoding);
      }
    }
    
    return new CHL7v2MessageXML($encoding);
}

  /**
   * Construct
   *
   * @param string $encoding Encoding
   *
   * @return \CHL7v2MessageXML
   */
  function __construct($encoding = "utf-8") {
    parent::__construct($encoding);

    $this->formatOutput = true;
}

  /**
   * Add namespaces
   *
   * @param string $name Schema
   *
   * @return void
   */
  function addNameSpaces($name) {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns", "urn:hl7-org:v2xml");
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "urn:hl7-org:v2xml $name.xsd");
}

  /**
   * Add element
   *
   * @param string $elParent
   * @param string $elName
   * @param string $elValue
   * @param string $elNS
   *
   * @return mixed
   */
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:hl7-org:v2xml") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
}

  /**
   * @param         $nodeName
   * @param DOMNode $contextNode
   * @return DOMNodeList
   */
  function query($nodeName, DOMNode $contextNode = null) {
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
    
    if ($contextNode) {
      return $xpath->query($nodeName, $contextNode);
    }
    
    return $xpath->query($nodeName);
  }

  /**
   * Get the node corresponding to an XPath
   *
   * @param string       $nodeName    The XPath to the node
   * @param DOMNode|null $contextNode The context node from which the XPath starts
   * @param array|null   &$data       Nodes data
   * @param boolean      $root        Is root node ?
   *
   * @return DOMNode The node
   */
  function queryNode($nodeName, DOMNode $contextNode = null, &$data = null, $root = false) {
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
        
    return $data[$nodeName] = $xpath->queryUniqueNode($root ? "//$nodeName" : "$nodeName", $contextNode);
  }

  /**
   * @param         $nodeName
   * @param DOMNode $contextNode
   * @param null    $data
   * @param bool    $root
   * @return DOMNodeList
   */
  function queryNodes($nodeName, DOMNode $contextNode = null, &$data = null, $root = false) {
    $nodeList = $this->query("$nodeName", $contextNode);
    foreach ($nodeList as $_node) {
      $data[$nodeName][] = $_node;
    }
    
    return $nodeList;
  }

  /**
   * @param         $nodeName
   * @param DOMNode $contextNode
   * @param bool    $root
   * @return string
   */
  function queryTextNode($nodeName, DOMNode $contextNode, $root = false) {
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
    
    return $xpath->queryTextNode($nodeName, $contextNode);
  }

  /**
   * @param $name
   * @param $data
   * @param $object
   */function getSegment($name, $data, $object) {
    if (!array_key_exists($name, $data) || $data[$name] === null) {
      return;
    }
    
    $function = "get$name";
    
    $this->$function($data[$name], $object);
  }

  /**
   * @return array
   */
  function getMSHEvenementXML() {
    $data = array();
    
    $MSH = $this->queryNode("MSH", null, $foo, true);
    
    $data['dateHeureProduction'] = mbDateTime($this->queryTextNode("MSH.7/TS.1", $MSH));
    $data['identifiantMessage']  = $this->queryTextNode("MSH.10", $MSH);
    
    return $data;
}

  /**
   * @param DOMNode $node
   * @param         $data
   */
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

  /**
   * @param DOMNode $node
   * @param         $data
   */
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

  /**
   * @param DOMNode $node
   * @return string
   */
  function getANMotherIdentifier(DOMNode $node) {
    $PID_21 = $this->queryNodes("PID.21", $node);
    foreach ($PID_21 as $_PID21) {
      if ($this->queryTextNode("CX.5", $_PID21) == "AN") {
        return $this->queryTextNode("CX.1", $_PID21);
      }
    }
  }

  /**
   * @param DOMNode $node
   * @return string
   */
  function getPIMotherIdentifier(DOMNode $node) {
    $PID_21 = $this->queryNodes("PID.21", $node);
    foreach ($PID_21 as $_PID21) {
      if ($this->queryTextNode("CX.5", $_PID21) == "PI") {
        return $this->queryTextNode("CX.1", $_PID21);
      }
    }
  }

  /**
   * @param DOMNode        $node
   * @param                $data
   * @param CInteropSender $sender
   */
  function getVNIdentifiers(DOMNode $node, &$data, CInteropSender $sender) {
    if (($this->queryTextNode("CX.5", $node) == "VN")) {
      $data["VN"] = $this->queryTextNode("CX.1", $node);
    }
  }

  /**
   * @param DOMNode        $node
   * @param                $data
   * @param CInteropSender $sender
   */
  function getRIIdentifiers(DOMNode $node, &$data, CInteropSender $sender) {
    // Notre propre RI
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id"))) {
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

  /**
   * @param DOMNode $node
   * @param         $data
   */
  function getNPAIdentifiers(DOMNode $node, &$data) {
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id"))) {
      $data["NPA"] = $this->queryTextNode("CX.1", $node);
    }
  }

  /**
   * @param                $nodeName
   * @param DOMNode        $contextNode
   * @param CInteropSender $sender
   * @return array
   */
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
    foreach ($this->query("PID.18", $contextNode) as $_node) {  
      $this->getANIdentifier($_node, $data);
    }    
 
    return $data;
  }

  /**
   * @param DOMNode        $contextNode
   * @param CInteropSender $sender
   * @return array
   */
  function getAdmitIdentifiers(DOMNode $contextNode, CInteropSender $sender) {
    $data = array();
    
    // RI - Resource identifier 
    // VN - Visit Number
    // AN - On peut également retrouver le numéro de dossier dans ce champ
    foreach ($this->query("PV1.19", $contextNode) as $_node) {
      switch ($sender->_configs["handle_NDA"]) {
        case 'PV1_19':
          $this->getANIdentifier($_node, $data);
          break;
          
        default:
          // RI - Resource Identifier
          $this->getRIIdentifiers($_node, $data, $sender);

          // VN - Visit Number
          $this->getVNIdentifiers($_node, $data, $sender);
    
          break;
      }
    }
      
    // PA - Preadmit Number
    if ($PV1_5 = $this->queryNode("PV1.5", $contextNode)) {
      $this->getNPAIdentifiers($PV1_5, $data);
    }
        
    return $data;
  }

  /**
   * @return array
   */
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

  /**
   * @param $value
   * @return int
   */
  function getBoolean($value) {
    return ($value == "Y") ? 1 : 0;
  }

  /**
   * @param $string
   * @return mixed
   */
  function getPhone($string) {
    return preg_replace("/[^0-9]/", "", $string);
  }

  /**
   * @param DOMNode   $node
   * @param CMbObject $mbObject
   * @param           $data
   */
  function getOBX(DOMNode $node, CMbObject $mbObject, $data) {
    $type  = $this->queryTextNode("OBX.3/CE.2", $node);
    $value = floatval($this->queryTextNode("OBX.5", $node));
    
    $constante_medicale = new CConstantesMedicales();
    
    if ($mbObject instanceof CSejour) {
      $constante_medicale->context_class = "CSejour";
      $constante_medicale->context_id    = $mbObject->_id;
      $constante_medicale->patient_id    = $mbObject->patient_id;
    }
    else if ($mbObject instanceof CPatient) {
      $constante_medicale->context_class = "CPatient";
      $constante_medicale->context_id    = $mbObject->_id;
      $constante_medicale->patient_id    = $mbObject->_id;
    }
    
    $constante_medicale->datetime = $this->queryTextNode("EVN.2/TS.1", $data["EVN"]);
    $constante_medicale->loadMatchingObject();
    switch ($type) {
      case "WEIGHT" :
        $constante_medicale->poids  = $value;
        break;
      
      case "HEIGHT" :
        $constante_medicale->taille = $value;
        break;
      default :
        return;  
    }
    $constante_medicale->_new_constantes_medicales = true;
    
    // Pour le moment pas de retour d'erreur dans l'acquittement
    $constante_medicale->store();
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack    Acknowledgment
   * @param CMbObject          $object Object
   * @param array              $data   Data
   *
   * @return void|string
   */
  function handle($ack, CMbObject $object, $data) {
  }

  /**
   * Get event acknowledgment
   *
   * @param CHL7Event $event Event
   *
   * @return CHL7v2Acknowledgment|CHL7v2PatientDemographicsAndVisitResponse
   */
  function getEventACK(CHL7Event $event) {
    // Pour l'acquittement du PDQ on retourne une réponse à la requête
    if ($this instanceof CHL7v2GeneratePatientDemographicsResponse) {
      return new CHL7v2PatientDemographicsAndVisitResponse($event);
    }

    // Pour l'acquittement du ORM on retourne un message ORR
    if ($this instanceof CHL7v2ReceiveOrderMessage) {
      return new CHL7v2ReceiveOrderMessageResponse($event);
    }

    // Génère un acquittement classique
    return new CHL7v2Acknowledgment($event);
  }
}
