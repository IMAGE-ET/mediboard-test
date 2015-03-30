<?php

/**
 * Sharing Value Sets - IHE
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CSVS
 * Sharing Value Sets
 */
class CSVS extends CIHE {
  /**
   * @var array
   */
  static $transaction_iti48 = array(
    "RetrieveValueSet"
  );

  /**
   * @var array
   */
  static $transaction_iti60 = array(
    "RetrieveMultipleValueSets"
  );

  /**
   * @var array
   */
  static $evenements = array(
    // ITI-48
    "RetrieveValueSet"          => "CHL7v3EventSVSRetrieveValueSet",

    // ITI-60
    "RetrieveMultipleValueSets" => "CHL7v3EventSVSRetrieveMultipleValueSets"
  );

  /**
   * Construct
   *
   * @return CSVS
   */
  function __construct() {
    $this->domain = "ITI";
    $this->type   = "SVS";

    $this->_categories = array(
      "ITI-48" => self::$transaction_iti48,
      "ITI-60" => self::$transaction_iti60
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
   * Retrieve transaction name,
   *
   * @param string $code Event code
   *
   * @return string Transaction name
   */
  static function getTransaction($code) {
    if (in_array($code, self::$transaction_iti48)) {
      return "ITI48";
    }

    if (in_array($code, self::$transaction_iti60)) {
      return "ITI60";
    }
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object An instance of data format
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
    $dom = new CHL7v3MessageXML();
    $dom->loadXMLSafe($ack_data);
    $dom->formatOutput = true;

    $acknowledgment_svs      = new CHL7v3AcknowledgmentSVS();
    $acknowledgment_svs->dom = $dom;

    return $acknowledgment_svs;
  }

  static function sendRetrieveValueSet($OID, $version = null, $language = null) {
    $receiver_hl7v3           = new CReceiverHL7v3();
    $receiver_hl7v3->actif    = 1;
    $receiver_hl7v3->group_id = CGroups::loadCurrent()->_id;

    /** @var CReceiverHL7v3[] $receivers */
    $receivers = $receiver_hl7v3->loadMatchingList();

    $event_name  = "CHL7v3EventSVSRetrieveValueSet";

    /** @var CHL7v3Event $event */
    $event              = new $event_name;
    $event->_event_name = "ValueSetRepository_RetrieveValueSet";

    $data = array(
      "OID"      => trim($OID),
      "version"  => trim($version),
      "language" => trim($language)
    );

    $object = new CMbObject();
    $object->_data = $data;

    $headers = CHL7v3Adressing::createWSAddressing("urn:ihe:iti:2008:RetrieveValueSet", "http://valuesetrepository/");

    $value_set = null;
    foreach ($receivers as $_receiver) {
      if (!$_receiver->isMessageSupported($event_name)) {
        continue;
      }

      $value_set = $_receiver->sendEvent($event, $object, $headers, true)->getQueryAck();
    }

    return $value_set;
  }
}