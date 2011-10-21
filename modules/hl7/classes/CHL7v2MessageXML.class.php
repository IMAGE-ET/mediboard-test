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
      if ($xpath->queryTextNode("CX.5", $_PID3) == "RI") {
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
    $avertissement = $msgID400 = $msgIPP = "";
    $_IPP_create   = $_modif_patient = false;
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender     = $exchange_ihe->_ref_sender;
    
    $patientRI = $data['patientIdentifiers']['RI'];
    $patientPI = $data['patientIdentifiers']['PI'];
    
    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$patientRI && !$patientPI) {
      return $exchange_ihe->setAckError($ack, "E005", $comment, $newPatient);
    }
        
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);

    // PI non connu
    if (!$IPP->_id) {
      // RI fourni
      if ($patientRI) {
        if ($newPatient->load($patientRI)) {
          
        } else {
          
        }
      } else {
        
      }
      

    } 
    // PI connu
    else {
      
    }
    
    
  }    
  
  function mappingPatient() {
    
  }
}

?>