<?php /* $Id:$ */

/**
 * Record patient, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordPatient 
 * Record patient, message XML HL7
 */

class CHL7v2RecordPatient extends CHL7v2MessageXML {
  function getContentsXML() {
    $data = array();

    $xpath = new CHL7v2MessageXPath($this);
    
    $data["PID"] = $PID = $xpath->queryUniqueNode("//PID");
    
    $data["patientIdentifiers"] = $this->getPatientIdentifiers($PID);
    
    $data["PD1"] = $PD1 = $xpath->queryUniqueNode("//PD1");
    
    $NK1 = $xpath->query("//NK1");
    foreach ($NK1 as $_NK1) {
      $data["NK1"][] = $_NK1;
    }
    
    $ROL = $xpath->query("//ROL");
    foreach ($ROL as $_ROL) {
      $data["ROL"][] = $_ROL;
    }
    
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
    // Mapping de l'INS-C
    if (array_key_exists("INSC", $data["patientIdentifiers"])) {
      $newPatient->INSC = $data["patientIdentifiers"]["INSC"];
    }
    
    // Segment PID
    $this->getPID($data["PID"], $newPatient);
    
    // Segment PD1
    $this->getPD1($data["PD1"], $newPatient);
    
    // Correspondants médicaux
    if ($newPatient->_id && array_key_exists("ROL", $data)) {
      foreach ($data["ROL"] as $_ROL) {
        $this->getROL($_ROL, $newPatient);
      }
    }
    
    // Correspondances
    // Possible ssi le patient est déjà enregistré
    if ($newPatient->_id && array_key_exists("NK1", $data)) {
      foreach ($data["NK1"] as $_NK1) {
        $this->getNK1($_NK1, $newPatient);
      }
    }
  }
  
  function getPID(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
    $PID5 = $xpath->query("PID.5", $node);
    foreach ($PID5 as $_PID5) {
      // Nom(s)
      $this->getNames($_PID5, $newPatient, $PID5);
      
      // Prenom(s)
      $this->getFirstNames($_PID5, $newPatient);
      
      // Civilité
      $newPatient->civilite = $xpath->queryTextNode("XPN.5", $_PID5);
    }
    
    // Date de naissance
    $newPatient->naissance = mbDate($xpath->queryTextNode("PID.7/TS.1", $node));
    
    // Sexe
    $newPatient->sexe = CHL7v2TableEntry::mapFrom("1", $xpath->queryTextNode("PID.8", $node));
    
    // Adresse(s)
    $this->getAdresses($node, $newPatient);
    
    // Téléphones
    $this->getPhones($node, $newPatient);
    
    //NSS
    $newPatient->matricule = $xpath->queryTextNode("PID.19", $node);
  }
  
  function getNames(DOMNode $node, CPatient $newPatient, DOMNodeList $PID5) {
    $xpath = new CHL7v2MessageXPath($this);
    
    if ($xpath->queryTextNode("XPN.7", $node) == "D") {
      $newPatient->nom = $xpath->queryTextNode("XPN.1/FN.1", $node);
    }
    if ($xpath->queryTextNode("XPN.7", $node) == "L") {
      // Dans le cas où l'on a pas de nom de nom de naissance le legal name
      // est le nom du patient
      if ($PID5->length > 1) {
        $newPatient->nom_jeune_fille = $xpath->queryTextNode("XPN.1/FN.1", $node);
      } 
      else {
        $newPatient->nom = $xpath->queryTextNode("XPN.1/FN.1", $node);
      }
    }
  }
  
  function getFirstNames(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
    $newPatient->prenom = $xpath->queryTextNode("XPN.2", $node);
    $first_names = explode(",", $xpath->queryTextNode("XPN.3", $node));
    $newPatient->prenom_2 = CValue::read($first_names, 1);
    $newPatient->prenom_3 = CValue::read($first_names, 2);
    $newPatient->prenom_4 = CValue::read($first_names, 3);
  }
  
  function getAdresses(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
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
  }
  
  function getAdress($adress, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
    $newPatient->adresse    = $adress["adresse"];
    $newPatient->ville      = $adress["ville"];
    $newPatient->cp         = $adress["cp"];
    $newPatient->pays_insee = $adress["pays_insee"];
  }
  
  function getPhones(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);

    $PID13 = $xpath->query("PID.13", $node);
    $phones = array();
    foreach ($PID13 as $_PID13) {
      $tel_number = $xpath->queryTextNode("XTN.1", $_PID13);
      switch ($xpath->queryTextNode("XTN.2", $_PID13)) {
        case "PRN" :
          $newPatient->tel  = $tel_number;
          break;
        case "ORN" :
          $newPatient->tel2 = $tel_number;
          break;
        default :
          $newPatient->tel_autre = $tel_number;
          break;
      }
    }
  }
  
  function getPD1(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
    // VIP ?
    $newPatient->vip = ($xpath->queryTextNode("PD1.12", $node) == "Y") ? 1 : 0; 
  }
  
  function getROL(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);

    switch ($xpath->queryTextNode("ROL.3/CE.1", $node)) {
      // Médecin traitant
      case "ODRP" :
        $newPatient->medecin_traitant = $this->getMedecin($xpath->queryUniqueNode("ROL.4", $node));
        break;
      case "RT" : 
        $correspondant = new CCorrespondant();
        $correspondant->patient_id = $newPatient->_id;
        $correspondant->medecin_id = $this->getMedecin($xpath->queryUniqueNode("ROL.4", $node));
        if (!$correspondant->loadMatchingObject()) {
          $correspondant->store();
        } 
        break;
    }
  }
  
  function getNK1(DOMNode $node, CPatient $newPatient) {
    $xpath = new CHL7v2MessageXPath($this);
    
    /* @todo voir quand on aura les tables de correspondances */
  }
  
  function getMedecin(DOMNode $node) {
    $xpath = new CHL7v2MessageXPath($this);
    
    $xcn1  = $xpath->queryTextNode("XCN.1", $node);
    $xcn2  = $xpath->queryTextNode("XCN.2/FN.1", $node);
    $xcn3  = $xpath->queryTextNode("XCN.3", $node);
      
    $medecin = new CMedecin();
    
    switch ($xpath->queryTextNode("XCN.13", $node)) {
      case "RPPS" :
        $medecin->rpps  = $xcn1;
        $medecin->loadMatchingObject();
        break;
      case "ADELI" :
        $medecin->adeli = $xcn1;
        $medecin->loadMatchingObject();
        break;
      case "RI" :
        // Gestion de l'identifiant MB
        if ($xpath->queryTextNode("XCN.9/CX.4/HD.2", $node) == CAppUI::conf("hl7 assigningAuthorityUniversalID")) {
          $medecin->load($xcn1);
        }
        /* @todo Gestion des id externes */
        break;
    }
    
    // Si pas retrouvé par son identifiant
    if (!$medecin->_id) {
      $medecin->nom    = $xcn2;
      $medecin->prenom = $xcn3;
      $medecin->loadMatchingObject();
      
      // Dans le cas où il n'est pas connu dans MB on le créé
      $medecin->store();
    }
    
    return $medecin->_id;
  }
}

?>