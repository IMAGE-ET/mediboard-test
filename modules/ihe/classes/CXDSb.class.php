<?php

/**
 * Cross-Enterprise Document Sharing
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CXDS
 * Cross-Enterprise Document Sharing
 */
class CXDSb extends CIHE {
  /** @var array */
  static $interaction_ITI18 = array (
    // Patient Registry Get Demographics Query
    "RegistryStoredQuery"
  );

  /** @var array */
  static $interaction_ITI41 = array (
    // Patient Registry Get Demographics Query
    "ProvideAndRegisterDocumentSetRequest"
  );

  static $interaction_ITI43 = array(
    "RetrieveDocumentSet"
  );

  /** @var array */
  static $interaction_ITI57 = array (
    // Patient Registry Get Demographics Query
    "UpdateDocumentSet"
  );

  /** @var array */
  static $evenements = array (
    // Patient Registry Get Demographics Query
    "ProvideAndRegisterDocumentSetRequest" => "CHL7v3EventXDSbProvideAndRegisterDocumentSetRequest",

    "RegistryStoredQuery"                  => "CHL7v3EventXDSbRegistryStoredQuery",
    "UpdateDocumentSet"                    => "CHL7v3EventXDSbUpdateDocumentSet",
    "RetrieveDocumentSet"                  => "CHL7v3EventXDSbRetrieveDocumentSet",
  );

  /**
   * Construct
   *
   * @return CXDSb
   */
  function __construct() {
    $this->domain = "ITI";
    $this->type   = "XDSb";

    $this->_categories = array(
      "ITI-41" => self::$interaction_ITI41,
      "ITI-18" => self::$interaction_ITI18,
      "ITI-57" => self::$interaction_ITI57,
      "ITI-43" => self::$interaction_ITI43,
    );

    parent::__construct();
  }

  /**
   * @see parent::getEvenements
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * @see parent::getVersions
   */
  function getVersions() {
    return self::$versions;
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return CHL7Event An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $classname = "CHL7v3Event{$exchange->type}{$exchange->sous_type}";

    return new $classname;
  }

  /**
   * Retrieve transaction name,
   *
   * @param string $code Event code
   *
   * @return string Transaction name
   */
  static function getTransaction($code) {
    if (in_array($code, self::$interaction_ITI18)) {
      return "ITI18";
    }

    if (in_array($code, self::$interaction_ITI41)) {
      return "ITI41";
    }

    if (in_array($code, self::$interaction_ITI43)) {
      return "ITI43";
    }

    if (in_array($code, self::$interaction_ITI57)) {
      return "ITI57";
    }
  }

  /**
   * Get aAcknowledgment object
   *
   * @param string $ack_data Data
   *
   * @return CHL7v3AcknowledgmentPRPA|null
   */
  static function getAcknowledgment($ack_data) {
    $dom = new CXDSXmlDocument();
    $dom->loadXMLSafe($ack_data);

    $element = $dom->documentElement;
    $localName = $element->localName;

    $name_event = str_replace("Response", "", $localName);

    $class_name = "CHL7v3Acknowledgment$name_event";

    switch ($class_name) {
      case "CHL7v3AcknowledgmentRetrieveDocumentSet":
        $hl7event = new $class_name();
        break;
      default:
        $hl7event = new CHL7v3AcknowledgmentXDSb();
    }

    $hl7event->dom = $dom;

    return $hl7event;
  }
}