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
class CXDS extends CIHE {

  /** @var array */
  static $interaction_ITI41 = array (
    // Patient Registry Get Demographics Query
    "ProvideAndRegisterDocumentSetRequest"
  );

  /** @var array */
  static $interaction_ITI18 = array (
    // Patient Registry Get Demographics Query
    "RegistryStoredQuery"
  );

  /** @var array */
  static $interaction_ITI57 = array (
    // Patient Registry Get Demographics Query
    "UpdateDocumentSet"
  );


  /** @var array */
  static $evenements = array (
    // Patient Registry Get Demographics Query
    "ProvideAndRegisterDocumentSetRequest" => "CHL7v3EventXDSProvideAndRegisterDocumentSetRequest",
    "RegistryStoredQuery"                  => "CHL7v3EventXDSRegistryStoredQuery",
    "UpdateDocumentSet"                    => "CHL7v3EventXDSUpdateDocumentSet",
  );

  /**
   * Construct
   *
   * @return CXDS
   */
  function __construct() {
    $this->type = "XDS";

    $this->_categories = array(
      "ITI-41" => self::$interaction_ITI41,
      "ITI-18" => self::$interaction_ITI18,
      "ITI-57" => self::$interaction_ITI57,
    );
  }

  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
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
   * Get aAcknowledgment object
   *
   * @param string $ack_data Data
   *
   * @return CHL7v3AcknowledgmentPRPA|null
   */
  static function getAcknowledgment($ack_data) {
    $dom = new CXDSXmlDocument();
    $dom->loadXMLSafe($ack_data);

    $hl7event = new CHL7v3AcknowledgmentXDS();
    $hl7event->dom = $dom;

    return $hl7event;
  }
}