<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimSanteMessageXML
 * Message XML HPR
 */
class CHPrimSanteMessageXML extends CMbXMLDocument {
  /** @var  CExchangeHprimSante */
  public $_ref_exchange_hpr;
  public $_ref_sender;
  public $_ref_receiver;

  /**
   * return the event type
   *
   * @param String $event_name event name
   * @param string $encoding   encoding
   *
   * @return CHPrimSanteMessageXML|CHPrimSanteRecordADM|CHPrimSanteRecordFiles|CHPrimSanteRecordPayment
   */
  static function getEventType($event_name = null, $encoding = "utf-8") {
    if (!$event_name) {
      return new CHPrimSanteMessageXML($encoding);
    }

    // Transfert de données d'admission
    if (substr($event_name, 0, 14) == "CHPrimSanteADM") {
      return new CHPrimSanteRecordADM($encoding);
    }

    // Transfert de données de règlement
    if (substr($event_name, 0, 14) == "CHPrimSanteREG") {
      return new CHPrimSanteRecordPayment($encoding);
    }

    // Transfert de données de règlement
    if (substr($event_name, 0, 14) == "CHPrimSanteORU") {
      return new CHPrimSanteRecordFiles($encoding);
    }

    return new CHPrimSanteMessageXML($encoding);
  }

  /**
   * @see parent::__construct
   */
  function __construct($encoding = "utf-8") {
    parent::__construct($encoding);

    $this->formatOutput = true;
  }

  /**
   * add name spaces
   *
   * @param String $name name
   *
   * @return void
   */
  function addNameSpaces($name) {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns", "urn:hpr-org:v2xml");
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "urn:hpr-org:v2xml");
  }

  /**
   * @see parent::addElement
   */
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:hpr-org:v2xml") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }

  /**
   * query
   *
   * @param String  $nodeName    node name
   * @param DOMNode $contextNode contexte node
   *
   * @return DOMNodeList
   */
  function query($nodeName, DOMNode $contextNode = null) {
    $xpath = new CHPrimSanteMessageXPath($contextNode ? $contextNode->ownerDocument : $this);

    if ($contextNode) {
      return $xpath->query($nodeName, $contextNode);
    }

    return $xpath->query($nodeName);
  }

  /**
   * query node
   *
   * @param String   $nodeName    node name
   * @param DOMNode  $contextNode context node
   * @param String[] &$data       data
   * @param bool     $root        root
   *
   * @return DOMElement
   */
  function queryNode($nodeName, DOMNode $contextNode = null, &$data = null, $root = false) {
    $xpath = new CHPrimSanteMessageXPath($contextNode ? $contextNode->ownerDocument : $this);

    return $data[$nodeName] = $xpath->queryUniqueNode($root ? "//$nodeName" : "$nodeName", $contextNode);
  }

  /**
   * query nodes
   *
   * @param String   $nodeName    node name
   * @param DOMNode  $contextNode context node
   * @param String[] &$data       data
   * @param bool     $root        root
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
   * query text node
   *
   * @param String  $nodeName    node name
   * @param DOMNode $contextNode context node
   * @param bool    $root        root
   *
   * @return string
   */
  function queryTextNode($nodeName, DOMNode $contextNode, $root = false) {
    $xpath = new CHPrimSanteMessageXPath($contextNode ? $contextNode->ownerDocument : $this);

    return $xpath->queryTextNode($nodeName, $contextNode);
  }

  /**
   * get the segment
   *
   * @param String    $name   name
   * @param String    $data   data
   * @param CMbObject $object object
   *
   * @return void
   */
  function getSegment($name, $data, $object) {
    if (!array_key_exists($name, $data) || $data[$name] === null) {
      return;
    }

    $function = "get$name";

    $this->$function($data[$name], $object);
  }

  /**
   * get the person identifiers
   *
   * @param DOMNode $node node
   *
   * @return String[]
   */
  function getPersonIdentifiers($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $data = array();

    $data["identifier"]       = $xpath->queryTextNode("P.2/FNM.1", $node);
    $data["identifier_merge"] = $xpath->queryTextNode("P.2/FNM.2", $node);
    $data["merge"]            = $xpath->queryTextNode("P.2/FNM.3", $node);

    return $data;
  }

  /**
   * get the sejour identifiers
   *
   * @param DOMNode $node node
   *
   * @return String[]
   */
  function getSejourIdentifier($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $data = array();

    $data["sejour_identifier"] = $xpath->queryTextNode("P.4/HD.1", $node);
    $data["rang"]              = $xpath->queryTextNode("P.4/HD.2", $node);

    return $data;
  }

  /**
   * get death date
   *
   * @param DOMNode $node node
   *
   * @return string
   */
  function getDeathDate($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $date = $xpath->queryTextNode("P.33/TS.1", $node);
    if ($date) {
      $date = CMbDT::transform(null, $date, "%Y-%m-%d %H:%i:%s");
    }
    return $date;
  }

  /**
   * get marital status
   *
   * @param DOMNode $node node
   *
   * @return null|string
   */
  function getMaritalStatus($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $marital = $xpath->queryTextNode("P.28", $node);
    return $marital == "U" ? null: $marital;
  }

  /**
   * get location
   *
   * @param DOMNode $node node
   *
   * @return array|null
   */
  function getLocalisation($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    if (!$p25 = $xpath->queryUniqueNode("P.25", $node)) {
      return null;
    };
    $data = array();
    $data["lit"]     = $xpath->queryTextNode("PL.1", $p25);
    $data["chambre"] = $xpath->queryTextNode("PL.2", $p25);
    $data["service"] = $xpath->queryTextNode("PL.3", $p25);

    return $data;
  }

  /**
   * get sejour status
   *
   * @param DOMNode $node node
   *
   * @return array
   */
  function getSejourStatut($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $data = array();

    $nodes = $xpath->query("P.23", $node);

    if ($nodes->length >= 1) {
      $data["entree"] = $xpath->queryTextNode("TS.1", $nodes->item(0));
    }
    if ($nodes->length >= 2) {
      $data["sortie"] = $xpath->queryTextNode("TS.1", $nodes->item(1));
    }

    $data["statut"] = $xpath->queryTextNode("P.24", $node);

    return $data;
  }

  /**
   * get the name person
   *
   * @param DOMNode $node node
   *
   * @return array
   */
  function getNamePerson($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $data = array();

    $data["family_name"] = $xpath->queryTextNode("P.6"     , $node);
    $data["name"]        = $xpath->queryTextNode("P.5/PN.1", $node);
    $data["firstname"]   = $xpath->queryTextNode("P.5/PN.2", $node);
    $data["secondname"]  = $xpath->queryTextNode("P.5/PN.3", $node);
    $data["pseudonyme"]  = $xpath->queryTextNode("P.5/PN.4", $node);
    $data["civilite"]    = $xpath->queryTextNode("P.5/PN.5", $node);
    $data["diplome"]     = $xpath->queryTextNode("P.5/PN.6", $node);

    return $data;
  }

  /**
   * get the birthdate
   *
   * @param DOMNode $node node
   *
   * @return null|string
   */
  function getBirthdate($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $birthdate = $xpath->queryTextNode("P.7", $node);
    return $birthdate ? CMbDT::transform(null, $birthdate, "%Y-%m-%d"): null;
  }

  /**
   * get the sex of the person
   *
   * @param DOMNode $node node
   *
   * @return string
   */
  function getSexPerson($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);

    return $xpath->queryTextNode("P.8", $node);
  }

  /**
   * get the address
   *
   * @param DOMNode $node node
   *
   * @return array
   */
  function getAddress($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $data = array();

    $data["street"]  = $xpath->queryTextNode("P.10/AD.1", $node);
    $data["comp"]    = $xpath->queryTextNode("P.10/AD.2", $node);
    $data["city"]    = $xpath->queryTextNode("P.10/AD.3", $node);
    $data["state"]   = $xpath->queryTextNode("P.10/AD.4", $node);
    $data["postal"]  = $xpath->queryTextNode("P.10/AD.5", $node);
    $data["country"] = $xpath->queryTextNode("P.10/AD.6", $node);

    return $data;
  }

  /**
   * get the phone
   *
   * @param DOMNode $node node
   *
   * @return array
   */
  function getPhone($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $node_phone = $xpath->query("P.12", $node);
    $data = array();
    foreach ($node_phone as $_node) {
      $data[] = $xpath->queryTextNode(".", $_node);
    }

    return $data;
  }

  /**
   * get the INS
   *
   * @param DOMNode $node node
   *
   * @return array
   */
  function getINS($node) {
    $xpath = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $nodeINS = $xpath->query("P.11");
    $ins = array();
    foreach ($nodeINS as $_node) {
      $ins[]["ins"]  = $xpath->queryTextNode("INS.1", $_node);
      $ins[]["type"] = $xpath->queryTextNode("INS.2", $_node);
      $ins[]["date"] = $xpath->queryTextNode("INS.3", $_node);
    }

    return $ins;
  }

  /**
   * Return or create the doctor of the message
   *
   * @param DOMNode $node Node
   *
   * @return CMediusers|int|null
   */
  function getDoctor($node) {
    $xpath      = new CHPrimSanteMessageXPath($node ? $node->ownerDocument : $this);
    $nodeDoctor = $xpath->query("P.13", $node);
    $code       = null;
    $nom        = null;
    $prenom     = null;
    $type_code  = null;

    foreach ($nodeDoctor as $_node_doctor) {
      $code       = $xpath->queryTextNode("CNA.1"     , $_node_doctor);
      $nom        = $xpath->queryTextNode("CNA.2/PN.1", $_node_doctor);
      $prenom     = $xpath->queryTextNode("CNA.2/PN.2", $_node_doctor);
      $type_code  = $xpath->queryTextNode("CNA.3"     , $_node_doctor);
      if ($code && $nom) {
        break;
      }
    }

    $mediuser   = new CMediusers();

    $mediuser->_user_last_name = $nom;

    switch ($type_code) {
      case "R":
        $mediuser->rpps  = $code;
        break;
      case "A":
        $mediuser->adeli = $code;
        break;
      default:
    }

    // Cas où l'on a aucune information sur le médecin
    if (!$mediuser->rpps && !$mediuser->adeli && !$mediuser->_id && !$mediuser->_user_last_name) {
      return null;
    }

    $sender = $this->_ref_sender;
    $ds     = $mediuser->getDS();

    $ljoin = array();
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

    $where   = array();
    $where["functions_mediboard.group_id"] = " = '$sender->group_id'";

    if (($mediuser->rpps || $mediuser->adeli)) {
      if ($mediuser->rpps) {
        $where[] = $ds->prepare("rpps = %", $mediuser->rpps);
      }
      if ($mediuser->adeli) {
        $where[] = $ds->prepare("adeli = %", $mediuser->adeli);
      }

      // Dans le cas où le praticien recherché par son ADELI ou RPPS est multiple
      if ($mediuser->countList($where, null, $ljoin) > 1) {
        $ljoin["users"] = "users_mediboard.user_id = users.user_id";
        $where[]        = $ds->prepare("users.user_last_name = %" , $nom);
      }

      $mediuser->loadObject($where, null, null, $ljoin);

      if ($mediuser->_id) {
        return $mediuser;
      }
    }

    $user = new CUser;

    $ljoin = array();
    $ljoin["users_mediboard"]     = "users.user_id = users_mediboard.user_id";
    $ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

    $where   = array();
    $where["functions_mediboard.group_id"] = " = '$sender->group_id'";
    $where[] = $ds->prepare("users.user_first_name = %", $prenom);
    $where[] = $ds->prepare("users.user_last_name = %" , $nom);

    $order = "users.user_id ASC";
    if ($user->loadObject($where, $order, null, $ljoin)) {
      return $user->loadRefMediuser();
    }

    $mediuser->_user_first_name = $prenom;
    $mediuser->_user_last_name  = $nom;

    return $this->createDoctor($mediuser);
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
    $function->text = CAppUI::conf("hprimsante importFunctionName");
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
    $mediuser->actif = CAppUI::conf("hprimsante doctorActif") ? 1 : 0;

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
   * get the H evenement
   *
   * @return array
   */
  function getHEvenementXML() {
    $data = array();

    $H = $this->queryNode("H", null, $foo, true);

    $data['dateHeureProduction'] = CMbDT::dateTime($this->queryTextNode("H.13/TS.1", $H));
    $data['filename']            = $this->queryTextNode("H.2", $H);

    return $data;
  }

  /**
   * get the content nodes
   *
   * @return array
   */
  function getContentNodes() {
    $data  = array();

    return $data;
  }

  /**
   * handle
   *
   * @param CHPrimSanteAcknowledgment $ack        Acknowledgment
   * @param CMbObject                 $newPatient Object
   * @param String                    $data       data
   *
   * @return void
   */
  function handle($ack, CMbObject $newPatient, $data) {
  }
}