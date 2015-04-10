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
 * Class CHL7v2RecordPerson 
 * Record person, message XML HL7
 */
class CHL7v2RecordPerson extends CHL7v2MessageXML {

  /** @var string */
  static $event_codes = array("A28", "A29", "A31");

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data = parent::getContentNodes();

    $this->queryNodes("NK1", null, $data, true);

    $this->queryNodes("ROL", null, $data, true);

    $this->queryNodes("OBX", null, $data, true);

    return $data;
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack        Acknowledgement
   * @param CPatient           $newPatient Person
   * @param array              $data       Nodes data
   *
   * @return null|string
   */
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender         = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$data['personIdentifiers']) {
      return $exchange_hl7v2->setAckAR($ack, "E100", null, $newPatient);
    }

    switch ($exchange_hl7v2->code) {
      // A29 - Delete person information
      case "A29":
        $eventCode = "A29";
        break;

      // All events
      default:
        $eventCode = "All";
    }

    $function_handle = "handle$eventCode";
    if (!method_exists($this, $function_handle)) {
      return $exchange_hl7v2->setAckAR($ack, "E006", null, $newPatient);
    }

    return $this->$function_handle($ack, $newPatient, $data);
  }

  /**
   * Handle all ITI-30 events
   *
   * @param CHL7Acknowledgment $ack        Acknowledgement
   * @param CPatient           $newPatient Person
   * @param array              $data       Nodes data
   *
   * @return null|string
   */
  function handleAll(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $_modif_patient = false;

    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender       = $this->_ref_sender;

    $patientRI       = CValue::read($data['personIdentifiers'], "RI");
    $patientRISender = CValue::read($data['personIdentifiers'], "RI_Sender");
    $patientPI       = CValue::read($data['personIdentifiers'], "PI");
      
    $IPP = new CIdSante400();
    
    if ($patientPI) {
      $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    }

    // PI non connu (non fourni ou non retrouvé)
    if (!$patientPI || !$IPP->_id) {
      // RI fourni
      if ($patientRI) {
        // Recherche du patient par son RI
        if ($newPatient->load($patientRI)) {
          $recoveredPatient = clone $newPatient;
          
          // Mapping primaire du patient
          $this->primaryMappingPatient($data, $newPatient);
          
          // Le patient retrouvé est-il différent que celui du message ?
          if (!$this->checkSimilarPatient($recoveredPatient, $newPatient)) {
            $commentaire = "Le nom ($newPatient->nom / $recoveredPatient->nom) ".
                           "et/ou le prénom ($newPatient->prenom / $recoveredPatient->prenom) sont très différents.";
            return $exchange_hl7v2->setAckAR($ack, "E123", $commentaire, $newPatient);
          }
          
          // On store le patient
          if ($msgPatient = CEAIPatient::storePatient($newPatient, $sender)) {
            return $exchange_hl7v2->setAckAR($ack, "E101", $msgPatient, $newPatient);
          }
                    
          $code_IPP      = "I121";
          $_modif_patient = true;
        } 
        // Patient non retrouvé par son RI
        else {
          $code_IPP = "I120";
        }
      }
      else {
        // Aucun IPP fourni
        if (!$patientPI) {
          $code_IPP = "I125";
        } 
        // Association de l'IPP
        else {
          $code_IPP = "I122";
        }        
      }      
      
      if (!$newPatient->_id) {
        // Mapping primaire du patient
          $this->primaryMappingPatient($data, $newPatient);

        // Patient retrouvé
        if ($newPatient->loadMatchingPatient()) {
          // Mapping primaire du patient
          $this->primaryMappingPatient($data, $newPatient);
                
          $code_IPP      = "A121";
          $_modif_patient = true;
        }

        // On store le patient
        $newPatient->_IPP = $IPP->id400;
        if ($msgPatient = CEAIPatient::storePatient($newPatient, $sender)) {
          return $exchange_hl7v2->setAckAR($ack, "E101", $msgPatient, $newPatient);
        }
      }
      
      $newPatient->_generate_IPP = false;
      // Mapping secondaire (correspondants, médecins) du patient
      if ($msgPatient = $this->secondaryMappingPatient($data, $newPatient)) {
        return $exchange_hl7v2->setAckAR($ack, "E101", $msgPatient, $newPatient);
      }

      if ($msgIPP = CEAIPatient::storeIPP($IPP, $newPatient, $sender)) {
        return $exchange_hl7v2->setAckAR($ack, "E102", $msgIPP, $newPatient);
      }
      
      $codes = array (($_modif_patient ? "I102" : "I101"), $code_IPP);
      
      $comment  = CEAIPatient::getComment($newPatient);
      $comment .= CEAIPatient::getComment($IPP);
    } 
    // PI connu
    else {
      $newPatient->load($IPP->object_id);
      
      $recoveredPatient = clone $newPatient;
          
      // Mapping primaire du patient
      $this->primaryMappingPatient($data, $newPatient);
      
      // Le patient retrouvé est-il différent que celui du message ?
      if (!$this->checkSimilarPatient($recoveredPatient, $newPatient)) {
        $commentaire = "Le nom ($newPatient->nom / $recoveredPatient->nom) ".
                       "et/ou le prénom ($newPatient->prenom / $recoveredPatient->prenom) sont très différents.";
        return $exchange_hl7v2->setAckAR($ack, "E124", $commentaire, $newPatient);
      }
                      
      // RI non fourni
      if (!$patientRI) {
        $code_IPP = "I123"; 
      }
      else {
        $tmpPatient = new CPatient();
        // RI connu
        if ($tmpPatient->load($patientRI)) {
          if ($tmpPatient->_id != $IPP->object_id) {
            $comment = "L'identifiant source fait référence au patient : $IPP->object_id".
                       "et l'identifiant cible au patient : $tmpPatient->_id.";
            return $exchange_hl7v2->setAckAR($ack, "E101", $comment, $newPatient);
          }
          $code_IPP = "I124"; 
        }
        // RI non connu
        else {
          $code_IPP = "A120";
        }
      }
      
      // On store le patient
      if ($msgPatient = CEAIPatient::storePatient($newPatient, $sender)) {
        return $exchange_hl7v2->setAckAR($ack, "E101", $msgPatient, $newPatient);
      }
      
      // Mapping secondaire (correspondants, médecins) du patient
      if ($msgPatient = $this->secondaryMappingPatient($data, $newPatient)) {
        return $exchange_hl7v2->setAckAR($ack, "E101", $msgPatient, $newPatient);
      }
            
      $codes = array ("I102", $code_IPP);
      
      $comment = CEAIPatient::getComment($newPatient);
    }

    if ($patientRISender) {
      CEAIPatient::storeRISender($patientRISender, $newPatient, $sender);
    }

    if ($sender->_configs["ins_integrated"]) {
      $this->getINS($data["PID"], $newPatient);
    }
    
    return $exchange_hl7v2->setAckAA($ack, $codes, $comment, $newPatient);
  }

  /**
   * Handle A29 event - Delete person information
   *
   * @param CHL7Acknowledgment $ack        Acknowledgement
   * @param CPatient           $newPatient Person
   * @param array              $data       Nodes data
   *
   * @return null|string
   */
  function handleA29(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";

    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender       = $this->_ref_sender;

    $patientPI = CValue::read($data['personIdentifiers'], "PI");
    $IPP = new CIdSante400();
    if ($patientPI) {
      $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    }

    if (!$patientPI || !$IPP->_id) {
      return $exchange_hl7v2->setAckAR($ack, "E150", null, $newPatient);
    }

    $newPatient->load($IPP->object_id);

    // Passage en trash de l'IPP du patient
    if ($msg = $newPatient->trashIPP($IPP)) {
      return $exchange_hl7v2->setAckAR($ack, "E151", $msg, $newPatient);
    }

    // Annulation de tous les séjours du patient qui n'ont pas d'entrée réelle
    $where = array();
    $where['entree_reelle'] = "IS NULL";
    $where['group_id']      = " = '$sender->group_id'";

    $sejours = $newPatient->loadRefsSejours($where);

    foreach ($sejours as $_sejour) {
      // Notifier les autres destinataires autre que le sender
      $_sejour->_eai_sender_guid = $sender->_guid;
      // Pas de génération de NDA
      $_sejour->_generate_NDA = false;
      // On ne check pas la cohérence des dates des consults/intervs
      $_sejour->_skip_date_consistencies = true;

      // On annule le séjour
      $_sejour->annule = 1;
      $_sejour->store();
    }

    $codes = array ("I150");

    return $exchange_hl7v2->setAckAA($ack, $codes, $comment, $newPatient);
  }

  /**
   * Primary mapping person
   *
   * @param array    $data       Datas
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function primaryMappingPatient($data, CPatient $newPatient) {
    // Segment PID
    $this->getPID($data["PID"], $newPatient, $data);
    
    // Segment PD1
    $this->getSegment("PD1", $data, $newPatient);
  }

  /**
   * Secondary mapping person
   *
   * @param array    $data       Datas
   * @param CPatient $newPatient Person
   *
   * @return string
   */
  function secondaryMappingPatient($data, CPatient $newPatient) {
    $sender = $this->_ref_sender;
    
    // Possible seulement dans le cas où le patient 
    if (!$newPatient->_id) {
      return;
    }
    
    // Correspondants médicaux
    if (array_key_exists("ROL", $data)) {
      foreach ($data["ROL"] as $_ROL) {
        $this->getROL($_ROL, $newPatient);
      }
    }
    
    // Correspondances
    // Possible ssi le patient est déjà enregistré
    if (array_key_exists("NK1", $data)) {
      foreach ($data["NK1"] as $_NK1) {
        $this->getNK1($_NK1, $newPatient);
      }
    }
    
    // Constantes du patient
    if (array_key_exists("OBX", $data)) {
      foreach ($data["OBX"] as $_OBX) {
        $this->getOBX($_OBX, $newPatient, $data);
      }
    }
    
    // On store le patient
    return CEAIPatient::storePatient($newPatient, $sender);
}

  /**
   * Check similar person
   *
   * @param CPatient $recoveredPatient Person recovered
   * @param CPatient $newPatient       Person
   *
   * @return bool
   */
  function checkSimilarPatient(CPatient $recoveredPatient, CPatient $newPatient) {
    return $recoveredPatient->checkSimilar($newPatient->nom, $newPatient->prenom, false);
}

  /**
   * Get PID segment
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   * @param array    $data       Datas
   *
   * @return void
   */
  function getPID(DOMNode $node, CPatient $newPatient, $data = null) {
    $PID5 = $this->query("PID.5", $node);
    foreach ($PID5 as $_PID5) {
      // Nom(s)
      $this->getNames($_PID5, $newPatient, $PID5);
      
      // Prenom(s)
      $this->getFirstNames($_PID5, $newPatient);
    }
    
    // Date de naissance
    $PID_7 = $this->queryTextNode("PID.7/TS.1", $node);
    $newPatient->naissance = $PID_7 ? CMbDT::date($PID_7) : null;
    
    // Cas d'un patient anonyme
    if ($newPatient->naissance && !$newPatient->prenom) {
      $newPatient->prenom = CValue::read($data['personIdentifiers'], "PI");
    }
    
    // Sexe
    $newPatient->sexe = CHL7v2TableEntry::mapFrom("1", $this->queryTextNode("PID.8", $node));

    // Civilité
    $newPatient->civilite = $newPatient->civilite ? $newPatient->civilite : "guess";
    /* @todo Voir comment faire ! Nouvelle table HL7 ? */
    //$newPatient->civilite = $this->queryTextNode("XPN.5", $_PID5);
    
    // Adresse(s)
    $this->getAdresses($node, $newPatient);
    
    // Téléphones
    $this->getPhones($node, $newPatient);
    
    // E-mail
    $this->getEmail($node, $newPatient);

    // Rang naissance
    $this->getRangNaissance($node, $newPatient);

    // Décès
    $this->getDeces($node, $newPatient);

    // NSS
    $this->getNSS($node, $newPatient);

    if ($data["personIdentifiers"]) {
      // Mapping de l'INS-C
      if ($data && array_key_exists("INSC", $data["personIdentifiers"])) {
        //@todo créé un l'insc avec un CINSPatient
        //$newPatient->INSC = $data["personIdentifiers"]["INSC"];
      }

      // NSS
      if (array_key_exists("SS", $data["personIdentifiers"])) {
        $newPatient->matricule = $data["personIdentifiers"]["SS"];
      }
    }

    $sender = $this->_ref_sender;
    if (!$sender) {
      return;
    }

    // AVS ?
    if ($sender->_configs["handle_PID_31"] == "avs") {
      $newPatient->avs = $this->queryTextNode("PID.31", $node);
    }

    $this->getPatientState($node, $newPatient);
  }

  /**
   * Get names
   *
   * @param DOMNode     $node       Node
   * @param CPatient    $newPatient Person
   * @param DOMNodeList $PID5       PID5
   *
   * @return void
   */
  function getNames(DOMNode $node, CPatient $newPatient, DOMNodeList $PID5) {
    $fn1 = $this->queryTextNode("XPN.1/FN.1", $node);

    switch ($this->queryTextNode("XPN.7", $node)) {
      case "D":
        $newPatient->nom = $fn1;
        break;

      case "L":
        // Dans le cas où l'on a pas de nom de nom de naissance le legal name
        // est le nom du patient
        if ($PID5->length > 1) {
          $newPatient->nom_jeune_fille = $fn1;
        }
        else {
          $newPatient->nom = $fn1;
        }
        break;

      default:
        $newPatient->nom = $fn1;
    }
  }

  /**
   * Get first name
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getFirstNames(DOMNode $node, CPatient $newPatient) {
    $newPatient->prenom = $this->queryTextNode("XPN.2", $node);
    $first_names = explode(",", $this->queryTextNode("XPN.3", $node));
    $newPatient->prenom_2 = CValue::read($first_names, 1);
    $newPatient->prenom_3 = CValue::read($first_names, 2);
    $newPatient->prenom_4 = CValue::read($first_names, 3);
  }

  /**
   * Get adresses
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getAdresses(DOMNode $node, CPatient $newPatient) {
    $PID11 = $this->query("PID.11", $node);
    $addresses = array();
    foreach ($PID11 as $_PID11) {
      $adress_type = $this->queryTextNode("XAD.7", $_PID11);
      /* @todo Ajouter la gestion des multi-lignes - SAD.2 */
      $addresses[$adress_type]["adresse"]      = $this->queryTextNode("XAD.1", $_PID11);
      $addresses[$adress_type]["adresse_comp"] = $this->queryTextNode("XAD.2", $_PID11);
      $addresses[$adress_type]["ville"]        = $this->queryTextNode("XAD.3", $_PID11);
      $addresses[$adress_type]["cp"]           = $this->queryTextNode("XAD.5", $_PID11);
      $addresses[$adress_type]["pays_insee"]   = $this->queryTextNode("XAD.6", $_PID11);
    }

    // Lieu de l'accouchement
    if (array_key_exists("BDL", $addresses)) {
      $newPatient->lieu_naissance       = CValue::read($addresses["BDL"], "ville");
      $newPatient->cp_naissance         = CValue::read($addresses["BDL"], "cp");

      if ($alpha_3 = CValue::read($addresses["BDL"], "pays_insee")) {
        $pays = CPaysInsee::getPaysByAlpha($alpha_3);

        $newPatient->pays_naissance_insee = $pays->numerique;
      }

      unset($addresses["BDL"]);
    }

    // Adresse de naissance == Lieu de l'accouchement
    if (array_key_exists("BR", $addresses)) {
      $newPatient->lieu_naissance       = CValue::read($addresses["BR"], "ville");
      $newPatient->cp_naissance         = CValue::read($addresses["BR"], "cp");

      if ($alpha_3 = CValue::read($addresses["BR"], "pays_insee")) {
        $pays = CPaysInsee::getPaysByAlpha($alpha_3);

        $newPatient->pays_naissance_insee = $pays->numerique;
      }

      unset($addresses["BR"]);
    }

    // Adresse
    if (array_key_exists("H", $addresses)) {
      $this->getAdress($addresses["H"], $newPatient);
    }
    else {
      foreach ($addresses as $_address) {
        $this->getAdress($_address, $newPatient);
      }
    }
  }

  /**
   * Get first name
   *
   * @param string   $adress     Adress
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getAdress($adress, CPatient $newPatient) {
    $newPatient->adresse    = $adress["adresse"];
    if ($adress["adresse_comp"]) {
        $newPatient->adresse  .= $this->getCompAdress($adress["adresse_comp"]);
    }

    $newPatient->ville      = $adress["ville"];
    $newPatient->cp         = $adress["cp"];
    if ($adress["pays_insee"]) {
      $pays = CPaysInsee::getPaysByAlpha($adress["pays_insee"]);

      $newPatient->pays_insee = $pays->numerique;
    }
  }

  /**
   * Get formatted adress
   *
   * @param string $adress Adress
   *
   * @return string
   */
  function getCompAdress($adress) {
    return "\n". str_replace("\\S\\", "\n", $adress);
  }

  /**
   * Get phones
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getPhones(DOMNode $node, CPatient $newPatient) {
    $PID13 = $this->query("PID.13", $node);
    
    foreach ($PID13 as $_PID13) {
      $tel_number = $this->queryTextNode("XTN.12", $_PID13);
      
      if (!$tel_number) {
        $tel_number = $this->queryTextNode("XTN.1", $_PID13);
      }
      
      switch ($this->queryTextNode("XTN.2", $_PID13)) {
        case "PRN":
          if ($this->queryTextNode("XTN.3", $_PID13) == "PH") {
            $newPatient->tel  = $this->getPhone($tel_number);
          }    
          
          if ($this->queryTextNode("XTN.3", $_PID13) == "CP") {
            $newPatient->tel2 = $this->getPhone($tel_number);
          }
          break;
          
        case "ORN":
          if ($this->queryTextNode("XTN.3", $_PID13) == "CP") {
            $newPatient->tel2 = $this->getPhone($tel_number);
          }
          break;

        case "WPN":
          if ($this->queryTextNode("XTN.3", $_PID13) == "PH") {
            $newPatient->tel_autre  = $this->getPhone($tel_number);
          }
          break;

        default:
          $newPatient->tel_autre = $tel_number;
      }
    }
  }

  /**
   * Get email
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getEmail(DOMNode $node, CPatient $newPatient) {
    $PID13 = $this->query("PID.13", $node);
    
    foreach ($PID13 as $_PID13) {
      if ($this->queryTextNode("XTN.2", $_PID13) != "NET") {
        continue;
      }  
      
      if ($this->queryTextNode("XTN.3", $_PID13) != "Internet") {
        continue;
      }
      
      $newPatient->email = $this->queryTextNode("XTN.4", $_PID13);
    }
  }

  /**
   * Get birth order
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getRangNaissance(DOMNode $node, CPatient $newPatient) {
    if ($rang_naissance = $this->queryTextNode("PID.25", $node)) {
      $newPatient->rang_naissance = $rang_naissance;
    }
  }

  /**
   * Get patient death datetime
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getDeces(DOMNode $node, CPatient $newPatient) {
    if ($deces = $this->queryTextNode("PID.29/TS.1", $node)) {
      $newPatient->deces = CMbDT::dateTime($deces);
    }
  }

  /**
   * Get the patient state
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Patient
   *
   * @return void
   */
  function getPatientState(DOMNode $node, CPatient $newPatient) {
    if ($states = $this->queryNodes("PID.32", $node)) {
      $list_state = array();
      foreach ($states as $_state) {
        $list_state[] = $this->queryTextNode(".", $_state);
      }

      $state = CMbArray::extract($list_state, 0);
      if (!$state) {
        return;
      }

      if ($state == "CACH") {
        $newPatient->vip = true;
        $status = CMbArray::get($list_state, 1);
        if ($status) {
          $state = $status;
        }
      }
      else {
        if (in_array("CACH", $list_state)) {
          $newPatient->vip = true;
        }
      }
      $newPatient->_status_no_guess = true;
      $newPatient->status = $state;
    }
  }

  /**
   * Get PD1 segment
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getPD1(DOMNode $node, CPatient $newPatient) {
    // VIP ?
    $newPatient->vip = ($this->queryTextNode("PD1.12", $node) == "Y") ? 1 : 0;
  }

  /**
   * Get ROL segment
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getROL(DOMNode $node, CPatient $newPatient) {
    $sender = $this->_ref_sender;
    
    $ROL_4 = $this->queryNodes("ROL.4", $node)->item(0);

    switch ($this->queryTextNode("ROL.3/CE.1", $node)) {
      // Médecin traitant
      case "ODRP":
        $newPatient->medecin_traitant = $this->getMedecin($ROL_4);
        break;
      case "RT":
        $correspondant = new CCorrespondant();
        $correspondant->patient_id = $newPatient->_id;
        $correspondant->medecin_id = $this->getMedecin($ROL_4);
        if (!$correspondant->loadMatchingObjectEsc()) {
          // Notifier les autres destinataires autre que le sender
          $correspondant->_eai_sender_guid = $sender->_guid;
          $correspondant->store();
        } 
        break;

      default:
    }
  }

  /**
   * Get NK1 segment
   *
   * @param DOMNode  $node       Node
   * @param CPatient $newPatient Person
   *
   * @return void
   */
  function getNK1(DOMNode $node, CPatient $newPatient) {
    $sender = $this->_ref_sender;
        
    $NK1_2  = $this->queryNode("NK1.2", $node);
    $nom    = $this->queryTextNode("XPN.1/FN.1", $NK1_2);
    $prenom = $this->queryTextNode("XPN.2", $NK1_2);

    if ($prenom == "") {
      $prenom = null;
    }
    
    $parente = $this->queryTextNode("NK1.3/CE.1", $node);
    $parente_autre = null;
    if ($parente == "OTH") {
      $parente_autre = $this->queryTextNode("NK1.3/CE.2", $node);
    }
    
    $NK1_4   = $this->queryNode("NK1.4", $node);
    $adresse = $this->queryTextNode("XAD.1/SAD.1", $NK1_4);
    $cp      = $this->queryTextNode("XAD.5", $NK1_4);
    $ville   = $this->queryTextNode("XAD.3", $NK1_4); 

    $NK1_5 = $this->queryNodes("NK1.5", $node)->item(0);

    $tel = null;
    if ($NK1_5) {
      $tel = $this->queryTextNode("XTN.12", $NK1_5);

      if (!$tel) {
        $tel = $this->queryTextNode("XTN.1", $NK1_5);
      }
    }
    
    $relation = $this->queryTextNode("NK1.7/CE.1", $node);
    $relation_autre = null;
    if ($relation == "O") {
      $relation_autre = $this->queryTextNode("NK1.7/CE.2", $node);
    }
    
    $corres_patient = new CCorrespondantPatient();
    $corres_patient->patient_id = $newPatient->_id;
    $corres_patient->nom        = $nom;
    $corres_patient->prenom     = $prenom;
    $corres_patient->loadMatchingObjectEsc();
    
    $corres_patient->adresse  = $adresse;
    $corres_patient->cp       = $cp;
    $corres_patient->ville    = $ville;
    $corres_patient->tel      = $tel;
    $corres_patient->parente = CHL7v2TableEntry::mapFrom("63", $parente);
    $corres_patient->parente_autre = $parente_autre; 
    $corres_patient->relation = CHL7v2TableEntry::mapFrom("131", $relation);
    $corres_patient->relation_autre = $relation_autre;
    
    // Notifier les autres destinataires autre que le sender
    $corres_patient->_eai_sender_guid = $sender->_guid;
    
    $corres_patient->store();
  }

  /**
   * Get doctor
   *
   * @param DOMNode $node Node
   *
   * @return int
   */
  function getMedecin(DOMNode $node) {
    $xcn1  = $this->queryTextNode("XCN.1", $node);
    $xcn2  = $this->queryTextNode("XCN.2/FN.1", $node);
    $xcn3  = $this->queryTextNode("XCN.3", $node);

    $medecin = new CMedecin();
    
    switch ($this->queryTextNode("XCN.13", $node)) {
      case "RPPS":
        $medecin->rpps  = $xcn1;
        $medecin->loadMatchingObjectEsc();
        break;

      case "ADELI":
        $medecin->adeli = $xcn1;
        $medecin->loadMatchingObjectEsc();
        break;

      case "RI":
        // Gestion de l'identifiant MB
        if ($this->queryTextNode("XCN.9/CX.4/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id")) {
          $medecin->load($xcn1);
        }

      default:
    }
    
    // Si pas retrouvé par son identifiant
    if (!$medecin->_id) {
      $medecin->nom    = $xcn2;
      $medecin->prenom = $xcn3;
      $medecin->loadMatchingObjectEsc();
      
      // Dans le cas où il n'est pas connu dans MB on le créé
      $medecin->store();
    }
    
    return $medecin->_id;
  }

  /**
   * Récupère le numéro de sécurité social du patient
   *
   * @param DOMNode  $node    PID
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function getNSS(DOMNode $node, CPatient $patient) {
    $sender = $this->_ref_sender;

    if ($sender->_configs["handle_NSS"] == "PID_19") {
      $patient->matricule = $this->queryTextNode("PID.19", $node);
    }
  }

  /**
   * Récupère les INS du patient
   *
   * @param DOMNode  $node    PID3
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function getINS(DOMNode $node, CPatient $patient) {
    if (!$patient->_id) {
      return;
    }

    $list_ins        = $this->query("PID.3[CX.5[text() = 'INS-C' or text() = 'INS-A']]", $node);
    $ins             = new CINSPatient();
    $ins->patient_id = $patient->_id;

    foreach ($list_ins as $_ins) {
      $ins->ins_patient_id = null;
      $ins->date           = null;
      $ins->provider       = null;
      $valeur = $this->queryTextNode("CX.1", $_ins);
      $date   = $this->queryTextNode("CX.7", $_ins);
      $type   = $this->queryTextNode("CX.5", $_ins);

      if (!$valeur) {
        continue;
      }
      $type = substr($type, -1);

      $ins->ins  = $valeur;
      $ins->type = $type;
      $ins->loadMatchingObject();

      if ($date && $ins->date < $date) {
        $ins->date     = CMbDT::dateTime($date);
        $ins->provider = $this->_ref_sender->nom;
      }

      $ins->store();
    }
  }
}