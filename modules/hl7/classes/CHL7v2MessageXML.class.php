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

  /** @var CExchangeHL7v2 */
  public $_ref_exchange_hl7v2;

  /** @var CInteropSender */
  public $_ref_sender;

  /** @var CInteropReceiver */
  public $_ref_receiver;

  /** @var string */
  public $_is_i18n;

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

      // Changement du patient par un autre
      if (CMbArray::in($event_code, CHL7v2MoveAccountInformation::$event_codes)) {
        return new CHL7v2MoveAccountInformation($encoding);
      }

      // Association / Déssociation
      if (CMbArray::in($event_code, CHL7v2LinkUnlink::$event_codes)) {
        return new CHL7v2LinkUnlink($encoding);
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
   * @param string $elParent Parent element
   * @param string $elName   Name
   * @param string $elValue  Value
   * @param string $elNS     Namespace
   *
   * @return mixed
   */
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:hl7-org:v2xml") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }

  /**
   * Query
   *
   * @param string  $nodeName    The XPath to the node
   * @param DOMNode $contextNode The context node from which the XPath starts
   *
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
   * Get the nodeList corresponding to an XPath
   *
   * @param string       $nodeName    The XPath to the node
   * @param DOMNode|null $contextNode The context node from which the XPath starts
   * @param array|null   &$data       Nodes data
   * @param boolean      $root        Is root node ?
   *
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
   * Get the text of a node corresponding to an XPath
   *
   * @param string       $nodeName    The XPath to the node
   * @param DOMNode|null $contextNode The context node from which the XPath starts
   * @param boolean      $root        Is root node ?
   *
   * @return string
   */
  function queryTextNode($nodeName, DOMNode $contextNode, $root = false) {
    $xpath = new CHL7v2MessageXPath($contextNode ? $contextNode->ownerDocument : $this);   
    
    return $xpath->queryTextNode($nodeName, $contextNode);
  }

  /**
   * Get segment
   *
   * @param string    $name   Segment name
   * @param array     $data   Data
   * @param CMbObject $object Object
   *
   * @return void
   */
  function getSegment($name, $data, $object) {
    if (!array_key_exists($name, $data) || $data[$name] === null) {
      return;
    }
    
    $function = "get$name";
    
    $this->$function($data[$name], $object, $data);
  }

  /**
   * Get fields in MSH segment
   *
   * @return array
   */
  function getMSHEvenementXML() {
    $data = array();
    
    $MSH = $this->queryNode("MSH", null, $foo, true);
    
    $data['dateHeureProduction']   = CMbDT::dateTime($this->queryTextNode("MSH.7/TS.1", $MSH));
    $data['identifiantMessage']    = $this->queryTextNode("MSH.10", $MSH);

    $data['receiving_application'] = $this->queryTextNode("MSH.3/HD.1", $MSH);
    $data['receiving_facility']    = $this->queryTextNode("MSH.4/HD.1", $MSH);
    
    return $data;
  }

  /**
   * Get PI identifier
   *
   * @param DOMNode        $node   Node
   * @param array          &$data  Data
   * @param CInteropSender $sender Sender
   *
   * @return void
   */
  function getPIIdentifier(DOMNode $node, &$data, CInteropSender $sender) {
    if (CHL7v2Message::$handle_mode == "simple") {
      $data["PI"] = $this->queryTextNode("CX.1", $node);

      return;
    }

    $control_identifier_type_code = CValue::read($sender->_configs, "control_identifier_type_code");

    $search_master_IPP = CValue::read($sender->_configs, "search_master_IPP");
    if ($search_master_IPP) {
      $domain = CDomain::getMasterDomain("CPatient", $sender->group_id);

      if ($domain->namespace_id != $this->queryTextNode("CX.4/HD.1", $node)) {
        return;
      }

      if ($control_identifier_type_code) {
        if ($this->queryTextNode("CX.5", $node) == "PI") {
          $data["PI"] = $this->queryTextNode("CX.1", $node);
        }
      }
      else {
        $data["PI"] = $this->queryTextNode("CX.1", $node);
      }

      return;
    }

    if ($this->queryTextNode("CX.5", $node) == "PI") {
      $data["PI"] = $this->queryTextNode("CX.1", $node);
    }
  }

  /**
   * Get AN identifier
   *
   * @param DOMNode        $node   Node
   * @param array          &$data  Data
   * @param CInteropSender $sender Sender
   *
   * @return void
   */
  function getANIdentifier(DOMNode $node, &$data, CInteropSender $sender) {
    if (CHL7v2Message::$handle_mode == "simple") {
      $data["AN"] = $this->queryTextNode("CX.1", $node);

      return;
    }

    $control_identifier_type_code = CValue::read($sender->_configs, "control_identifier_type_code");

    $search_master_NDA = CValue::read($sender->_configs, "search_master_NDA");
    if ($search_master_NDA) {
      $domain = CDomain::getMasterDomain("CSejour", $sender->group_id);

      if ($domain->namespace_id != $this->queryTextNode("CX.4/HD.1", $node)) {
        return;
      }

      if ($control_identifier_type_code) {
        if ($this->queryTextNode("CX.5", $node) == "AN") {
          $data["AN"] = $this->queryTextNode("CX.1", $node);
        }
      }
      else {
        $data["AN"] = $this->queryTextNode("CX.1", $node);
      }

      return;
    }

    if ($this->queryTextNode("CX.5", $node) == "AN") {
      $data["AN"] = $this->queryTextNode("CX.1", $node);
    }
  }

  /**
   * Get AN mother identifier
   *
   * @param DOMNode $node Node
   *
   * @return string
   */
  function getANMotherIdentifier(DOMNode $node) {
    $PID_21 = $this->queryNodes("PID.21", $node);
    foreach ($PID_21 as $_PID21) {
      if (CHL7v2Message::$handle_mode == "simple") {
        return $this->queryTextNode("CX.1", $_PID21);
      }
      else {
        if ($this->queryTextNode("CX.5", $_PID21) == "AN") {
          return $this->queryTextNode("CX.1", $_PID21);
        }
      }
    }

    return null;
  }

  /**
   * Get PI mother identifier
   *
   * @param DOMNode $node Node
   *
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
   * Get VN identifier
   *
   * @param DOMNode $node  Node
   * @param array   &$data Data
   *
   * @return void
   */
  function getVNIdentifiers(DOMNode $node, &$data) {
    if (($this->queryTextNode("CX.5", $node) == "VN")) {
      $data["VN"] = $this->queryTextNode("CX.1", $node);
    }
  }

  /**
   * Get RI identifiers
   *
   * @param DOMNode        $node   Node
   * @param array          &$data  Data
   * @param CInteropSender $sender Sender
   *
   * @return void
   */
  function getRIIdentifiers(DOMNode $node, &$data, CInteropSender $sender) {
    $control_identifier_type_code = CValue::read($sender->_configs, "control_identifier_type_code");

    // Notre propre RI
    if ($this->queryTextNode("CX.4/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id")) {
      if ($control_identifier_type_code && $this->queryTextNode("CX.5", $node) != "RI") {
        return;
      }

      $data["RI"] = $this->queryTextNode("CX.1", $node);

      return;
    }

    // RI de l'expéditeur
    if (($this->queryTextNode("CX.5", $node) == "RI") && 
        ($this->queryTextNode("CX.4/HD.2", $node) == $sender->_configs["assigning_authority_universal_id"])
    ) {
      $data["RI_Sender"] = $this->queryTextNode("CX.1", $node);
      return;
    }
    
    // RI des autres systèmes
    if (($this->queryTextNode("CX.5", $node) == "RI")) {
      $data["RI_Others"] = $this->queryTextNode("CX.1", $node);
    }
  }

  /**
   * Get NPA identifiers
   *
   * @param DOMNode        $node   Node
   * @param array          &$data  Data
   * @param CInteropSender $sender Sender
   *
   * @return void
   */
  function getNPAIdentifiers(DOMNode $node, &$data, CInteropSender $sender) {
    if (CHL7v2Message::$handle_mode == "simple") {
      $data["NPA"] = $this->queryTextNode("CX.1", $node);
    }
    else {
      if (($this->queryTextNode("CX.5", $node) == "AN")) {
        $data["NPA"] = $this->queryTextNode("CX.1", $node);
      }
    }
  }

  /**
   * Get person identifiers
   *
   * @param string         $nodeName    Node name
   * @param DOMNode        $contextNode Node
   * @param CInteropSender $sender      Sender
   *
   * @return array
   */
  function getPersonIdentifiers($nodeName, DOMNode $contextNode, CInteropSender $sender) {
    $data = array();
    
    foreach ($this->query($nodeName, $contextNode) as $_node) {
      // RI - Resource identifier 
      $this->getRIIdentifiers($_node, $data, $sender);
      
      // PI - Patient internal identifier
      $this->getPIIdentifier($_node, $data, $sender);
      
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
      $this->getANIdentifier($_node, $data, $sender);
    }    
 
    return $data;
  }

  /**
   * Get admit identifiers
   *
   * @param DOMNode        $contextNode Node
   * @param CInteropSender $sender      Sender
   *
   * @return array
   */
  function getAdmitIdentifiers(DOMNode $contextNode, CInteropSender $sender) {
    $data = array();
    
    // RI - Resource identifier 
    // VN - Visit Number
    // AN - On peut également retrouver le numéro de dossier dans ce champ
    $handle_NDA = CValue::read($sender->_configs, "handle_NDA");
    foreach ($this->query("PV1.19", $contextNode) as $_node) {
      switch ($handle_NDA) {
        case 'PV1_19':
          $this->getANIdentifier($_node, $data, $sender);
          break;
          
        default:
          // RI - Resource Identifier
          $this->getRIIdentifiers($_node, $data, $sender);

          // VN - Visit Number
          $this->getVNIdentifiers($_node, $data);
    
          break;
      }
    }

    $handle_PV1_50 = CValue::read($sender->_configs, "handle_PV1_50");
    switch ($handle_PV1_50) {
      // Il s'agit-là du sejour_id qui fait office de "NDA temporaire"
      case 'sejour_id':
        foreach ($this->query("PV1.50", $contextNode) as $_node) {
          if (($this->queryTextNode("CX.5", $_node) == "AN")) {
            $data["RI"] = $this->queryTextNode("CX.1", $_node);
          }
        }

        break;

      default:
    }

    // PA - Preadmit Number
    if ($PV1_5 = $this->queryNode("PV1.5", $contextNode)) {
      $this->getNPAIdentifiers($PV1_5, $data, $sender);
    }
        
    return $data;
  }

  /**
   * Return the Object with the information of the medecin in the message
   *
   * @param DOMNode   $node   Node
   * @param CMbObject $object object
   *
   * @return int|null|string
   */
  function getDoctor(DOMNode $node, CMbObject $object) {
    $type_id    = $this->queryTextNode("XCN.13", $node);
    $id         = $this->queryTextNode("XCN.1", $node);
    $last_name  = $this->queryTextNode("XCN.2/FN.1", $node);
    $first_name = $this->queryTextNode("XCN.3", $node);

    switch ($type_id) {
      case "RPPS":
        $object->rpps = $id;
        break;

      case "ADELI":
        $object->adeli = $id;
        break;

      case "RI":
        // Notre propre RI
        if (($this->queryTextNode("XCN.9/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id"))) {
          return $id;
        }

      default:
        // Recherche du praticien par son idex
        $idex  = CIdSante400::getMatch($object->_class, $this->_ref_sender->_tag_mediuser, $id);
        if ($idex->_id) {
          return $idex->object_id;
        }

        if ($object instanceof CMediusers) {
          $object->_user_first_name = $first_name;
          $object->_user_last_name  = $last_name;
        }
        if ($object instanceof CMedecin) {
          $object->prenom = $first_name;
          $object->nom    = $last_name;
        }

        break;
    }

    // Cas où l'on a aucune information sur le médecin
    if (!$object->rpps && !$object->adeli && !$object->_id &&
        (($object instanceof CMediusers && !$object->_user_last_name) ||
        ($object instanceof CMedecin && !$object->nom))
    ) {
      return null;
    }

    if ($object instanceof CMedecin && $object->loadMatchingObjectEsc()) {
      return $object->_id;
    }

    $sender = $this->_ref_sender;
    $ds     = $object->getDS();

    if ($object instanceof CMediusers) {
      $ljoin = array();
      $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

      $where   = array();
      $where["functions_mediboard.group_id"] = " = '$sender->group_id'";

      if (($object->rpps || $object->adeli)) {
        if ($object->rpps) {
          $where[] = $ds->prepare("rpps = %", $object->rpps);
        }
        if ($object->adeli) {
          $where[] = $ds->prepare("adeli = %", $object->adeli);
        }

        // Dans le cas où le praticien recherché par son ADELI ou RPPS est multiple
        if ($object->countList($where, null, $ljoin) > 1) {
          $ljoin["users"] = "users_mediboard.user_id = users.user_id";
          $where[]        = $ds->prepare("users.user_last_name = %" , $last_name);
        }

        $object->loadObject($where, null, null, $ljoin);

        if ($object->_id) {
          return $object->_id;
        }
      }

      $user = new CUser;

      $ljoin = array();
      $ljoin["users_mediboard"]     = "users.user_id = users_mediboard.user_id";
      $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

      $where   = array();
      $where["functions_mediboard.group_id"] = " = '$sender->group_id'";
      $where[] = $ds->prepare("users.user_first_name = %", $first_name);
      $where[] = $ds->prepare("users.user_last_name = %" , $last_name);

      $order = "users.user_id ASC";
      if ($user->loadObject($where, $order, null, $ljoin)) {
        return $user->_id;
      }

      $object->_user_first_name = $first_name;
      $object->_user_last_name  = $last_name;

      return $this->createDoctor($object);
    }
  }

  /**
   * Create the mediuser
   *
   * @param CMediusers $mediuser mediuser
   *
   * @return int
   */
  function createDoctor(CMediusers $mediuser) {
    $sender = $this->_ref_sender;

    $function = new CFunctions();
    $function->text = CAppUI::conf("hl7 importFunctionName");
    $function->group_id = $sender->group_id;
    $function->loadMatchingObjectEsc();
    if (!$function->_id) {
      $function->type = "cabinet";
      $function->compta_partagee = 0;
      $function->store();
    }
    $mediuser->function_id = $function->_id;
    $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name, null, true);
    $mediuser->_user_type = 13; // Medecin
    $mediuser->actif = CAppUI::conf("hl7 doctorActif") ? 1 : 0;

    $user = new CUser();
    $user->user_last_name   = $mediuser->_user_last_name;
    $user->user_first_name  = $mediuser->_user_first_name;
    // On recherche par le seek
    $users                  = $user->seek("$user->user_last_name $user->user_first_name");
    if (count($users) == 1) {
      $user = reset($users);
      $user->loadRefMediuser();
      $mediuser = $user->_ref_mediuser;
    }
    else {
      // Dernière recherche si le login est déjà existant
      $user = new CUser();
      $user->user_username = $mediuser->_user_username;
      if ($user->loadMatchingObject()) {
        // On affecte un username aléatoire
        $mediuser->_user_username .= rand(1, 10);
      }

      $mediuser->store();
    }

    return $mediuser->_id;
  }

  /**
   * Get content nodes
   *
   * @return array
   */
  function getContentNodes() {
    $data  = array();

    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender       = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();
   
    $this->_ref_sender = $sender;
    
    $this->queryNode("EVN", null, $data, true);
    
    $PID = $this->queryNode("PID", null, $data, true);

    $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);

    $this->queryNode("PD1", null, $data, true);
    
    return $data;
  }

  /**
   * Get AN number
   *
   * @param CInteropSender $sender Sender
   * @param array          $data   Data
   *
   * @return string
   */
  function getVenueAN($sender, $data) {
    switch ($sender->_configs["handle_NDA"]) {
      case 'PV1_19':
        return CValue::read($data['admitIdentifiers'], "AN");

      default:
        return CValue::read($data['personIdentifiers'], "AN");
    }
  }

  /**
   * Get boolean
   *
   * @param bool $value Value
   *
   * @return int
   */
  function getBoolean($value) {
    return ($value == "Y") ? 1 : 0;
  }

  /**
   * Get phone
   *
   * @param string $string Value
   *
   * @return mixed
   */
  function getPhone($string) {
    return preg_replace("/[^0-9]/", "", $string);
  }

  /**
   * Get segement OBX
   *
   * @param DOMNode   $node   Node
   * @param CMbObject $object Object
   * @param array     $data   Data
   *
   * @return void
   */
  function getOBX(DOMNode $node, CMbObject $object, $data) {
    $type  = $this->queryTextNode("OBX.3/CE.2", $node);
    $value = floatval($this->queryTextNode("OBX.5", $node));
    
    $constante_medicale = new CConstantesMedicales();
    
    if ($object instanceof CSejour) {
      $constante_medicale->context_class = "CSejour";
      $constante_medicale->context_id    = $object->_id;
      $constante_medicale->patient_id    = $object->patient_id;
    }
    else if ($object instanceof CPatient) {
      $constante_medicale->context_class = "CPatient";
      $constante_medicale->context_id    = $object->_id;
      $constante_medicale->patient_id    = $object->_id;
    }
    
    $constante_medicale->datetime = $this->queryTextNode("EVN.2/TS.1", $data["EVN"]);
    $constante_medicale->loadMatchingObject();
    switch ($type) {
      case "WEIGHT":
        $constante_medicale->poids  = $value;
        break;
      
      case "HEIGHT":
        $constante_medicale->taille = $value;
        break;

      default:
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

  /**
   * Verifies that the message is for this actor
   *
   * @param array         $data  Data
   * @param CInteropActor $actor Actor
   *
   * @return bool
   */
  function checkApplicationAndFacility($data, CInteropActor $actor) {
    if (empty($actor->_configs["check_receiving_application_facility"])) {
      return true;
    }

    return ($data['receiving_application'] == $actor->_configs["receiving_application"]) &&
           ($data['receiving_facility'] == $actor->_configs['receiving_facility']);
  }
}
