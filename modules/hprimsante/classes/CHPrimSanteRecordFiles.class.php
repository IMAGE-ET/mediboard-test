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
 * Class CHPrimSanteRecordFiles
 * Record Result, message XML
 */
class CHPrimSanteRecordFiles extends CHPrimSanteMessageXML {
  /**
   * @see parent::getContentNodes
   */
  function getContentNodes() {
    $data = array();

    $this->queryNodes("//ORU.PATIENT_RESULT", null, $data, true); // get ALL the P segments

    return $data;
  }

  /**
   * @see parent::handle
   */
  function handle($ack, CMbObject $object, $data) {
    /** @var CExchangeHprimSante $exchange_hpr */
    $exchange_hpr = $this->_ref_exchange_hpr;
    $erreur = array();

    foreach ($data["//ORU.PATIENT_RESULT"] as $_i => $_data_patient) {
      //Permet d'identifier le numéro de ligne
      $this->loop               = $_i;
      $patient_node             = $this->queryNode("P", $_data_patient);
      $identifier               = $this->getPersonIdentifiers($patient_node);
      $this->identifier_patient = $identifier;

      if (!$identifier["identifier"]) {
        //identifier non transmis
        $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "01", array("P", $_i+1, $identifier), "8.3");
        continue;
      }
      //récupération du patient par idex/match
      $patient = $this->getPatient($identifier["identifier"], $patient_node);

      if (!$patient->_id) {
        //patient non trouvé
        $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "02", array("P", $_i+1, $identifier), "8.3");
        continue;
      }

      //récupération de l'identifiant du sejour
      $nda_identifier = $this->getSejourIdentifier($patient_node);
      //récupération du séjour idex/match
      $sejour = $this->getSejour($patient, $nda_identifier["sejour_identifier"], $patient_node);

      if ($sejour instanceof CHPrimSanteError) {
        $erreur[] = $sejour;
        continue;
      }

      //Récupération des demandes
      $orders_node = $this->queryNodes("ORU.ORDER_OBSERVATION", $_data_patient);
      foreach ($orders_node as $_j => $_order) {
        //Permet d'identifier le numéro de ligne
        $loop = $_i+$_j+2;
        //Récupération des résultats
        $observations_node = $this->queryNodes("ORU.OBSERVATION", $_order);
        foreach ($observations_node as $_k => $_observation) {
          //Permet d'identifier le numéro de ligne
          $loop += $_k+1;
          $type_observation = $this->getObservationType($_observation);
          //On ne traite que les OBX qui contiennent les fichiers
          switch ($type_observation) {
            case "FIC":
              $result       = $this->getObservationResult($_observation);
              $result_parts = explode("\\S\\", $result);
              $name_editor  = CMbArray::get($result_parts, 0);
              $file_name    = CMbArray::get($result_parts, 1);
              $file_type    = $this->getFileType(CMbArray::get($result_parts, 2));

              if (!$file_name) {
                $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "16", array("OBX", $loop, $identifier), "10.6");
                continue;
              }
              $this->loop = $loop;
              $this->storeFile($name_editor, $file_name, $file_type, $sejour, $erreur);
              break;
            default:
          }
        }
      }
    }

    return $exchange_hpr->setAck($ack, $erreur, $patient);
  }

  /**
   * Get the mediboard file type
   *
   * @param String $file_type Type file
   *
   * @return null|string
   */
  function getFileType($file_type) {
    switch ($file_type) {
      case "PDF":
        $result = "application/pdf";
        break;
      default:
        $result = null;
    }

    return $result;
  }

  /**
   * Get the observation type
   *
   * @param DOMNode $observation Observation
   *
   * @return string
   */
  function getObservationType(DOMNode $observation) {
    $xpath = new CHPrimSanteMessageXPath($observation ? $observation->ownerDocument : $this);
    return $xpath->queryTextNode("OBX/OBX.2/CE.1", $observation);
  }

  /**
   * Get the observation result
   *
   * @param DOMNode $observation Observation
   *
   * @return string
   */
  function getObservationResult(DOMNode $observation) {
    $xpath = new CHPrimSanteMessageXPath($observation ? $observation->ownerDocument : $this);
    return $xpath->queryTextNode("OBX/OBX.5", $observation);
  }

  /**
   * Store the file
   *
   * @param String  $prefix    Prefix for the name of file
   * @param String  $file_name Name of file
   * @param String  $file_type Type file
   * @param CSejour $sejour    Sejour
   * @param Array   &$erreur   Error
   *
   * @return bool
   */
  function storeFile($prefix, $file_name, $file_type, $sejour, &$erreur) {
    /** @var CInteropSender $sender */
    $sender       = $this->_ref_sender;
    $exchange_hpr = $this->_ref_exchange_hpr;
    $file         = false;
    $object_links = $sender->loadRefsObjectLinks();

    foreach ($object_links as $_object_link) {
      /** @var CInteropSender $sender_link */
      $sender_link = $_object_link->loadRefObject();
      $sender_link->loadRefsExchangesSources();

      foreach ($sender_link->_ref_exchanges_sources as $_source) {
        $path = $_source->getFullPath($file_name);
        /** @var CExchangeSource $_source */
        $data = $_source->getData($path);
        if (!$data) {
          continue;
        }
        $file = new CFile();
        $file->file_name = "$prefix $file_name";
        $file->file_type = $file_type;
        $file->fillFields();
        $file->setObject($sejour);
        $file->putContent($data);

        if ($msg = $file->store()) {
          $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "17", array("OBX", $this->loop, $this->identifier_patient), "10.6", CMbString::removeAllHTMLEntities($msg));
          continue;
        };
      }
    }

    if (!$file) {
      $erreur[] = new CHPrimSanteError($exchange_hpr, "P", "18", array("OBX", $this->loop, $this->identifier_patient), "10.6");
      return false;
    }

    return true;
  }
}