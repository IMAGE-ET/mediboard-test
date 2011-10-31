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
    
    $data["NK1"] = $NK1 = $xpath->query("//NK1");
    
    $data["ROL"] = $NK1 = $xpath->query("//ROL");
    
    return $data;
  }
  
  function getPatientIdentifiers(DOMNode $node) {
    $data = array();
    
    $xpath = new CHL7v2MessageXPath($this);
    // PID/PID.3
    foreach ($xpath->query("PID.3", $node) as $_PID3) {
      // RI - Resource identifier 
      if (($xpath->queryTextNode("CX.5", $_PID3) == "RI") && 
          ($xpath->queryTextNode("CX.4/HD.2", $_PID3) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
        $data["RI"] = $xpath->queryTextNode("CX.1", $_PID3);
      }
      // PI - Patient internal identifier
      if ($xpath->queryTextNode("CX.5", $_PID3) == "PI") {
        $data["PI"] = $xpath->queryTextNode("CX.1", $_PID3);
      }
      // INS-C - Identifiant national de santé calculé
      if ($xpath->queryTextNode("CX.5", $_PID3) == "INS-C") {
        $data["INSC"] = $xpath->queryTextNode("CX.1", $_PID3);
      }
    }
   
    return $data;
  }
 
  function recordPerson(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    $_modif_patient = false;
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $patientRI = $patientPI = null;
    foreach ($data['patientIdentifiers'] as $identifier_type => $_patient_identifier) {
      switch ($identifier_type) {
        case "RI":
          $patientRI = $_patient_identifier;  
          break;
        case "PI":
          $patientPI = $_patient_identifier;  
          break;
      }
    }
    
    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$patientRI && !$patientPI) {
      return $exchange_ihe->setAckAR($ack, "E003", null, $newPatient);
    }
        
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);

    // PI non connu
    if (!$IPP->_id) {
      // RI fourni
      if ($patientRI) {
        // Recherche du patient par son RI
        if ($newPatient->load($patientRI)) {
          // Mapping du patient
          $this->mappingPatient($data, $newPatient);
          
          // Notifier les autres destinataires autre que le sender
          $newPatient->_eai_initiateur_group_id = $sender->group_id;
          if ($msgPatient = $newPatient->store()) {
            return $exchange_ihe->setAckAR($ack, "E101", $msgPatient, $newPatient);
          }
                    
          $code_IPP      = "I121";
          $_modif_patient = true; 
        } 
        // Patient non retrouvé par son RI
        else {
          $code_IPP = "I120";
        }
      } else {
        $code_IPP = "I122";
      }      
      
      if (!$newPatient->_id) {
        // Mapping du patient
        $this->mappingPatient($data, $newPatient);
        
        // Patient retrouvé
        if ($newPatient->loadMatchingPatient()) {
          // Mapping du patient
          $this->mappingPatient($data, $newPatient);
                
          $code_IPP      = "A121";
          $_modif_patient = true; 
        }
        
        // Notifier les autres destinataires autre que le sender
        $newPatient->_eai_initiateur_group_id = $sender->group_id;
        if ($msgPatient = $newPatient->store()) {
          return $exchange_ihe->setAckAR($ack, "E101", $msgPatient, $newPatient);
        }
      }

      if ($msgIPP = CEAIPatient::storeIPP($IPP, $newPatient)) {
        return $exchange_ihe->setAckAR($ack, "E102", $msgIPP, $newPatient);
      }
      
      $codes = array (($_modif_patient ? "I102" : "I101"), $code_IPP);
      
      $comment  = CEAIPatient::getComment($newPatient);
      $comment .= CEAIPatient::getComment($IPP);
    } 
    // PI connu
    else {
      $newPatient->load($IPP->object_id);
      
      // Mapping du patient
      $this->mappingPatient($data, $newPatient);
                      
      // RI non fourni
      if (!$patientRI) {
        $code_IPP = "I123"; 
      } else {
        $tmpPatient = new CPatient();
        // RI connu
        if ($tmpPatient->load($patientRI)) {
          if ($tmpPatient->_id != $IPP->object_id) {
            $comment = "L'identifiant source fait référence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
            return $exchange_ihe->setAckAR($ack, "E100", $comment, $newPatient);
          }
          $code_IPP = "I124"; 
        }
        // RI non connu
        else {
          $code_IPP = "A120";
        }
      }
      
      // Notifier les autres destinataires autre que le sender
      $newPatient->_eai_initiateur_group_id = $sender->group_id;
      if ($msgPatient = $newPatient->store()) {
        return $exchange_ihe->setAckAR($ack, "E101", $msgPatient, $newPatient);
      }
            
      $codes = array ("I102", $code_IPP);
      
      $comment = CEAIPatient::getComment($newPatient);
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newPatient);
  }    
  
  function mappingPatient($data, CPatient $newPatient) {
    $this->getPID($data["PID"], $newPatient);
    mbLog($newPatient);
  }
  
  function getPID(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
    $PID5 = $xpath->query("PID.5", $node);
    foreach ($PID5 as $_PID5) {
      // Nom(s)
      if ($xpath->queryTextNode("XPN.7", $_PID5) == "D") {
        $newPatient->nom = $xpath->queryTextNode("XPN.1/FN.1", $_PID5);
      }
      if ($xpath->queryTextNode("XPN.7", $_PID5) == "L") {
        // Dans le cas où l'on a pas de nom de nom de naissance le legal name
        // est le nom du patient
        if ($PID5->length > 1) {
          $newPatient->nom_jeune_fille = $xpath->queryTextNode("XPN.1/FN.1", $_PID5);
        } 
        else {
          $newPatient->nom = $xpath->queryTextNode("XPN.1/FN.1", $_PID5);
        }
      }
      
      // Prenom(s)
      $newPatient->prenom = $xpath->queryTextNode("XPN.2", $_PID5);
      $first_names = explode(",", $xpath->queryTextNode("XPN.3", $_PID5));
      $newPatient->prenom_2 = CValue::read($first_names, 1);
      $newPatient->prenom_3 = CValue::read($first_names, 2);
      $newPatient->prenom_4 = CValue::read($first_names, 3);
      
      // Civilité
      $newPatient->civilite = $xpath->queryTextNode("XPN.5", $_PID5);
    }
    
    // Date de naissance
    $newPatient->naissance = mbDate($xpath->queryTextNode("PID.7/TS.1", $node));
    
    // Sexe
    $newPatient->sexe = CHL7v2TableEntry::mapFrom("1", $xpath->queryTextNode("PID.8", $node));
    
    // Adresse(s)
    $PID11 = $xpath->query("PID.11", $node);
    $addresses = array();
    foreach ($PID11 as $_PID11) {
      $adress_type = $xpath->queryTextNode("XAD.7", $_PID11);
      /* @todo Ajouter la gestion des multi-lignes - SAD.2 */
      $addresses[$adress_type]["adresse"]    = $xpath->queryTextNode("XAD.1", $_PID11);
      $addresses[$adress_type]["ville"]      = $xpath->queryTextNode("XAD.3", $_PID11);
      $addresses[$adress_type]["cp"]         = $xpath->queryTextNode("XAD.5", $_PID11);
      $addresses[$adress_type]["pays_insee"] = $xpath->queryTextNode("XAD.6", $_PID11);
    }
    // Adresse  naissance
    if (array_key_exists("BR", $addresses)) {
      $newPatient->lieu_naissance       = CValue::read($addresses["BR"], "ville");
      $newPatient->cp_naissance         = CValue::read($addresses["BR"], "cp");
      $newPatient->pays_naissance_insee = CValue::read($addresses["BR"], "pays_insee");
      
      unset($addresses["BR"]);
    }
    // Adresse
    if (array_key_exists("H", $addresses)) {
      $this->getAdress($addresses["H"], $newPatient);
    } else {
      foreach ($addresses as $adress_type => $_address) {
        $this->getAdress($_address, $newPatient);
      }
    }
    
    // Téléphones
    
  }
  
  function getAdress($adress, CPatient $newPatient) {
    $newPatient->adresse    = $adress["adresse"];
    $newPatient->ville      = $adress["ville"];
    $newPatient->cp         = $adress["cp"];
    $newPatient->pays_insee = $adress["pays_insee"];
  }
}

?>