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
 * Class CHL7v2RecordObservationResultSet 
 * Record observation result set, message XML
 */
class CHL7v2RecordObservationResultSet extends CHL7v2MessageXML {
  static $event_codes = array("R01");

  public $codes = array();

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data = $patient_results = array();
    
    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender         = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();
    
    $patient_results = $this->queryNodes("ORU_R01.PATIENT_RESULT", null, $varnull, true);
    
    foreach ($patient_results as $_patient_result) {
      // Patient
      $oru_patient = $this->queryNode("ORU_R01.PATIENT", $_patient_result, $varnull);
      $PID = $this->queryNode("PID", $oru_patient, $data, true);
      $data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);
      
      // Venue
      $oru_visit = $this->queryNode("ORU_R01.VISIT", $oru_patient, $varnull);
      $PV1 = $this->queryNode("PV1", $oru_visit, $data, true);
      if ($PV1) {
        $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);
      }
      
      // Observations
      $order_observations = $this->queryNodes("ORU_R01.ORDER_OBSERVATION", $_patient_result, $varnull);
      $data["observations"] = array();
      foreach ($order_observations as $_order_observation) {
        $tmp = array();
        // OBR
        $this->queryNode("OBR", $_order_observation, $tmp, true);
        
        // OBXs
        $oru_observations = $this->queryNodes("ORU_R01.OBSERVATION", $_order_observation, $varnull);
        foreach ($oru_observations as $_oru_observation) {
          $this->queryNodes("OBX", $_oru_observation, $tmp, true);
        }
        
        $data["observations"][] = $tmp;
      }
    }
    
    return $data;
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack     Acknowledgement
   * @param CPatient           $patient Person
   * @param array              $data    Nodes data
   *
   * @return null|string
   */
  function handle(CHL7Acknowledgment $ack, CPatient $patient, $data) {
    // Traitement du message des erreurs
    $comment = "";
    $object  = null;

    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $exchange_hl7v2->_ref_sender->loadConfigValues();
    $sender = $this->_ref_sender = $exchange_hl7v2->_ref_sender;

    // Patient
    $patientPI = CValue::read($data['personIdentifiers'], "PI");
    if (!$patientPI) {
      return $exchange_hl7v2->setAckAR($ack, "E007", null, $patient);
    }
   
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
    // Patient non retrouvé par son IPP
    if (!$IPP->_id) {
      return $exchange_hl7v2->setAckAR($ack, "E105", null, $patient);
    }
    $patient->load($IPP->object_id);

    $sejour = null;

    // Sejour
    $venueAN = CValue::read($data["admitIdentifiers"], "AN");
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);

      // Séjour non retrouvé par son NDA
      if (!$NDA->_id) {
        return $exchange_hl7v2->setAckAR($ack, "E205", null, $patient);
      }

      /** @var CSejour $sejour */
      $sejour = $NDA->loadTargetObject();

      if (!$sejour->_id) {
        return $exchange_hl7v2->setAckAR($ack, "E220", null, $patient);
      }

      if ($sejour->patient_id !== $patient->_id) {
        return $exchange_hl7v2->setAckAR($ack, "E606", null, $patient);
      }
    }

    // Pas d'observations
    $first_result = reset($data["observations"]);
    if (!$first_result) {
      return $exchange_hl7v2->setAckAR($ack, "E225", null, $patient);
    }

    // Recherche par date
    $observation_dt = $this->getOBRObservationDateTime($first_result["OBR"]);
    $sejours = $patient->getCurrSejour($observation_dt); //FIXME ignorer les annulés
    $sejour = reset($sejours);

    if (!$sejour->_id) {
      return $exchange_hl7v2->setAckAR($ack, "E220", null, $patient);
    }

    // Récupération des observations
    foreach ($data["observations"] as $_observation) {
      // Récupération de la date du relevé
      $observation_dt = $this->getOBRObservationDateTime($_observation["OBR"]);
      $name           = $this->getOBRServiceIdentifier($_observation["OBR"]);

      // OBR identity identifier
      $OBR_identity_identifier = null;

      $handle_OBR_identity_identifier = $sender->_configs["handle_OBR_identity_identifier"];
      if ($handle_OBR_identity_identifier) {
        $OBR_identity_identifier = $this->queryTextNode($handle_OBR_identity_identifier, $_observation["OBR"]);
      }

      foreach ($_observation["OBX"] as $key => $_OBX) {
        $status = $this->getObservationResultStatus($_OBX);
        if ($status === "X") {
          continue;
        }

        // OBX.2 : Value type
        $value_type   = $this->getOBXValueType($_OBX);
        $date         = $observation_dt ? $observation_dt : $this->getOBXObservationDateTime($_OBX);
        $praticien_id = $this->getObservationAuthor($_OBX);

        // Recherche de l'objet avec la date correspondante fourni dans l'observation
        $object = $this->getObjectWithDate($date, $patient, $praticien_id, $sejour);

        if (!$object) {
          return $exchange_hl7v2->setAckAR($ack, "E301", null, $patient);
        }

        $name = $name.$key;

        switch ($value_type) {
          // Reference Pointer to External Report
          case "RP":
            if (!$this->getReferencePointerToExternalReport($_OBX, $object, $name)) {
              return $exchange_hl7v2->setAckAR($ack, $this->codes, null, $object);
            }

            break;

          // Encapsulated Data
          case "ED":
            if (!$this->getEncapsulatedData($_OBX, $object, $name)) {
              return $exchange_hl7v2->setAckAR($ack, $this->codes, null, $object);
            }

            break;

          // Pulse Generator and Lead Observation Results
          case "ST":
          case "CWE":
          case "DTM":
          case "NM":
          case "SN":
            if (!$this->getPulseGeneratorAndLeadObservationResults($_OBX, $patient, $object)) {
              return $exchange_hl7v2->setAckAR($ack, $this->codes, null, $object);
            }

            break;

          // Not supported
          default:
            return $exchange_hl7v2->setAckAR($ack, "E302", null, $object);
        }

        // On store l'idex de l'identifiant du système tiers
        $idex = new CIdSante400();
        $idex->object_class = $object->_class;
        $idex->object_id    = $object->_id;
        $idex->tag          = "OBR_$sender->_tag_hl7";
        $idex->id400        = $OBR_identity_identifier;
        $idex->loadMatchingObject();

        $idex->store();
      }
    }
    
    return $exchange_hl7v2->setAckCA($ack, $this->codes, $comment, $object);
  }

  /**
   * Filler number
   *
   * @param DOMNode $node node
   *
   * @return string
   */
  function getOBRFillerNumber(DOMNode $node) {
    return $this->queryTextNode("OBR.2/EI.1", $node);
  }

  /**
   * Placer number
   *
   * @param DOMNode $node node
   *
   * @return string
   */
  function getOBRPlacerNumber(DOMNode $node) {
    return $this->queryTextNode("OBR.3/EI.1", $node);
  }



  /**
   * Return the object for attach the document
   *
   * @param String   $date         date
   * @param CPatient $patient      patient
   * @param String   $praticien_id praticien id
   * @param CSejour  $sejour       sejour
   *
   * @return CConsultation|COperation|CSejour
   */
  function getObjectWithDate($date, $patient, $praticien_id, $sejour) {
    //Recherche de la consutlation dans le séjour
    $date         = CMbDT::date($date);
    $date_before  = CMbDT::date("- 2 DAY", $date);
    $consultation = new CConsultation();
    $where = array(
      "patient_id"           => "= '$patient->_id'",
      "annule"               => "= '0'",
      "plageconsult.date"    => "BETWEEN '$date_before' AND '$date'",
      "plageconsult.chir_id" => "= '$praticien_id'",
      "sejour_id"            => "= '$sejour->_id'",
    );

    $leftjoin = array("plageconsult" => "consultation.plageconsult_id = plageconsult.plageconsult_id");
    $consultation->loadObject($where, "plageconsult.date DESC", null, $leftjoin);

    //Recherche d'une consultation qui pourrait correspondre
    if (!$consultation->_id) {
      unset($where["sejour_id"]);
      $consultation->loadObject($where, "plageconsult.date DESC", null, $leftjoin);
    }

    //Consultation trouvé dans un des deux cas
    if ($consultation->_id) {
      return $consultation;
    }

    //Recherche d'une opération dans le séjour
    $where = array(
      "sejour.patient_id"  => "= '$patient->_id'",
      "plagesop.date"      => "BETWEEN '$date_before' AND '$date'",
      "operations.chir_id" => "= '$praticien_id'",
      "operations.annulee" => "= '0'",
      "sejour.sejour_id"   => "= '$sejour->_id'",
    );
    $leftjoin = array(
      "plagesop" => "operations.plageop_id = plagesop.plageop_id",
      "sejour"   => "operations.sejour_id = sejour.sejour_id",
    );
    $operation = new COperation();
    $operation->loadObject($where, "plagesop.date DESC", null, $leftjoin);

    if ($operation->_id) {
      return $operation;
    }

    /*if (!$sejour) {
      $where = array(
        "patient_id" => "= '$patient->_id'",
        "annule"     => "= '0'",
      );
      $sejours = CSejour::loadListForDate($date, $where, null, 1);
      $sejour = reset($sejours);

      if (!$sejour) {
        return null;
      }
    }*/

    return $sejour;
  }

  /**
   * Get observation date time
   *
   * @param DOMNode $node DOM node
   *
   * @return string
   */
  function getOBRObservationDateTime(DOMNode $node) {
    return $this->queryTextNode("OBR.7", $node);
  }

  /**
   * Get observation date time
   *
   * @param DOMNode $node DOM node
   *
   * @return string
   */
  function getOBRServiceIdentifier(DOMNode $node) {
    return $this->queryTextNode("OBR.4/CE.1", $node);
  }

  /**
   * Get value type
   *
   * @param DOMNode $node DOM node
   *
   * @return string
   */
  function getOBXValueType(DOMNode $node) {
    return $this->queryTextNode("OBX.2", $node);
  }

  /**
   * Get observation date time
   *
   * @param DOMNode $node DOM node
   *
   * @return string
   */
  function getOBXObservationDateTime(DOMNode $node) {
    return $this->queryTextNode("OBX.14/TS.1", $node);
  }

  /**
   * Get observation date time
   *
   * @param DOMNode            $node   DOM node
   * @param CObservationResult $result Result
   *
   * @return string
   */
  function mappingObservationResult(DOMNode $node, CObservationResult $result) {
    // OBX-3: Observation Identifier
    $this->getObservationIdentifier($node, $result);
    
    // OBX-6: Units
    $this->getUnits($node, $result);
    
    // OBX-5: Observation Value (Varies)
    $result->value = $this->getObservationValue($node);
    
    // OBX-11: Observation Result Status
    $result->status =$this->getObservationResultStatus($node);
  }

  /**
   * Get observation identifier
   *
   * @param DOMNode            $node   DOM node
   * @param CObservationResult $result Result
   *
   * @return string
   */
  function getObservationIdentifier(DOMNode $node, CObservationResult $result) {
    $identifier    = $this->queryTextNode("OBX.3/CE.1", $node);
    $text          = $this->queryTextNode("OBX.3/CE.2", $node);
    $coding_system = $this->queryTextNode("OBX.3/CE.3", $node);
    
    $value_type = new CObservationValueType();
    $result->value_type_id = $value_type->loadMatch($identifier, $coding_system, $text);
  }

  /**
   * Get unit
   *
   * @param DOMNode            $node   DOM node
   * @param CObservationResult $result Result
   *
   * @return string
   */
  function getUnits(DOMNode $node, CObservationResult $result) {
    $identifier    = $this->queryTextNode("OBX.6/CE.1", $node);
    $text          = $this->queryTextNode("OBX.6/CE.2", $node);
    $coding_system = $this->queryTextNode("OBX.6/CE.3", $node);
    
    $unit_type = new CObservationValueUnit();
    $result->unit_id = $unit_type->loadMatch($identifier, $coding_system, $text);
  }

  /**
   * Get observation value
   *
   * @param DOMNode $node DOM node
   *
   * @return string
   */
  function getObservationValue(DOMNode $node) {
    $observation = $this->queryTextNode("OBX.5", $node);
    $message_object = $this->_ref_exchange_hl7v2->getMessage();
    return $message_object->unescape($observation);
  }

  /**
   * Get observation result status
   *
   * @param DOMNode $node DOM node
   *
   * @return string
   */
  function getObservationResultStatus(DOMNode $node) {
    return $this->queryTextNode("OBX.11", $node);
  }

  /**
   * Return the author of the document
   *
   * @param DOMNode $node node
   *
   * @return String
   */
  function getObservationAuthor(DOMNode $node) {
    $xcn = $this->queryNode("OBX.16", $node);
    $mediuser = new CMediusers();
    return $this->getDoctor($xcn, $mediuser);
  }

  /**
   * OBX Segment pulse generator and lead observation results
   *
   * @param DOMNode    $OBX       DOM node
   * @param CPatient   $patient   Person
   * @param COperation $operation Opération
   *
   * @return bool
   */
  function getPulseGeneratorAndLeadObservationResults(DOMNode $OBX, CPatient $patient, COperation $operation) {
    $result_set = new CObservationResultSet();

    $dateTimeOBX = $this->getOBXObservationDateTime($OBX);
    if ($dateTimeOBX) {
      $result_set->patient_id    = $patient->_id;
      $result_set->context_class = "COperation";
      $result_set->context_id    = $operation->_id;
      $result_set->datetime      = CMbDT::dateTime($dateTimeOBX);
      if ($msg = $result_set->store()) {
        $this->codes[] = "E302";
      }
    }

    // Traiter le cas où ce sont des paramètres sans résultat utilisable
    if ($this->getObservationResultStatus($OBX) === "X") {
      return true;
    }

    $result = new CObservationResult();
    $result->observation_result_set_id = $result_set->_id;
    $this->mappingObservationResult($OBX, $result);

    /* @todo à voir si on envoi un message d'erreur ou si on continu ... */
    if ($msg = $result->store()) {
      $this->codes[] = "E304";
    }

    return true;
  }

  /**
   * Return the mime type
   *
   * @param String $type type
   *
   * @return null|string
   */
  function getFileType($type) {
    $file_type = null;

    switch ($type) {
      case "GIF":
        $file_type = "image/gif";
        break;
      case "JPEG":
      case "JPG":
        $file_type = "image/jpeg";
        break;
      case "PNG":
        $file_type = "image/png";
        break;
      case "RTF":
        $file_type = "application/rtf";
        break;
      case "HTML":
        $file_type = "text/html";
        break;
      case "TIFF":
        $file_type = "image/tiff";
        break;
      case "XML":
        $file_type = "application/xml";
        break;
      case "PDF":
        $file_type = "application/pdf";
        break;
      default:
        $file_type = "unknown/unknown";
    }

    return $file_type;
  }

  /**
   * OBX Segment with encapsulated Data
   *
   * @param DOMNode   $OBX    node
   * @param CMbObject $object object
   * @param String    $name   name
   *
   * @return bool
   */
  function getEncapsulatedData($OBX, $object, $name) {
    //Récupération de le fichier et du type du fichier (basename)
    $observation  = $this->getObservationValue($OBX);

    $ed      = explode("^", $observation);
    $subtype = CMbArray::get($ed, 2);
    $content = CMbArray::get($ed, 4);

    $file_type = $this->getFileType($subtype);
    if (!$file_type) {
      $this->codes[] = "E344";
      return false;
    }

    $file = new CFile();
    $file->setObject($object);
    $file->file_name = $name.".".strtolower($subtype);
    $file->file_type = $file_type;
    $file->loadMatchingObject();

    $file->file_date = $this->getOBXObservationDateTime($OBX);
    $file->doc_size = strlen($content);

    $file->fillFields();
    $file->updateFormFields();

    $file->putContent($content);

    if ($file->store()) {
      $this->codes[] = "E343";
      return false;
    }

    return true;
  }

  /**
   * OBX Segment with reference pointer to external report
   *
   * @param DOMNode   $OBX    DOM node
   * @param CMbObject $object object
   * @param String    $name   name
   *
   * @return bool
   */
  function getReferencePointerToExternalReport(DOMNode $OBX, CMbObject $object, $name) {
    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender         = $exchange_hl7v2->_ref_sender;

    //Récupération de l'emplacement et du type du fichier (full path)
    $observation  = $this->getObservationValue($OBX);

    $rp      = explode("^", $observation);
    $pointer = CMbArray::get($rp, 0);
    $type    = CMbArray::get($rp, 2);

    //Création d'un lien Hypertext sur l'objet
    if ($type == "HTML") {
      $hyperlink = new CHyperTextLink();
      $hyperlink->setObject($object);
      $hyperlink->name = $name;
      $hyperlink->link = $pointer;

      if ($msg = $hyperlink->store()) {
        $this->codes[] = "E343";
        return false;
      }

      return true;
    }

    // Chargement de la source associée à l'expéditeur
    /** @var CInteropSender $sender_link */
    $object_links = $sender->loadRefsObjectLinks();
    if (!$object_links) {
      $this->codes[] = "E340";

      return false;
    }

    $sender_link = reset($object_links)->_ref_object;
    // Aucun expéditeur permettant de récupérer les fichiers
    if (!$sender_link->_id) {
      $this->codes[] = "E340";

      return false;
    }

    $authorized_sources = array(
      "CSenderFileSystem",
      "CSenderFTP"
    );

    // L'expéditeur n'est pas prise en charge pour la réception de fichiers
    if (!CMbArray::in($sender_link->_class, $authorized_sources)) {
      $this->codes[] = "E341";

      return false;
    }

    $sender_link->loadRefsExchangesSources();
    // Aucune source permettant de récupérer les fichiers
    if (!$sender_link->_id) {
      $this->codes[] = "E342";

      return false;
    }

    $source = $sender_link->getFirstExchangesSources();

    $path = str_replace("\\", "/", $pointer);
    $path = basename($path);

    $filename = $path;

    if ($source instanceof CSourceFileSystem) {
      $path = $source->getFullPath()."/$path";
    }

    // Exception déclenchée sur la lecture du fichier
    try {
      $content = $source->getData("$path");
    }
    catch (Exception $e) {
      $this->codes[] = "E345";
      return false;
    }

    if (!$type) {
      $type = CMbPath::getExtension($path);
    }

    $file_type = $this->getFileType($type);

    // Gestion du CFile
    $file = new CFile();
    $file->setObject($object);
    $file->file_name = $filename;
    $file->file_type = $file_type;
    $file->loadMatchingObject();

    $file->file_date = "now";
    $file->doc_size  = strlen($content);

    $file->fillFields();
    $file->updateFormFields();

    $file->putContent($content);

    if ($msg = $file->store()) {
      $this->codes[] = "E343";
    }

    $this->codes[] = "I340";

    return true;
  }
}
