<?php

/**
 * Patient Administration - HL7v3
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CPRPAMessaging
 * Patient Administration
 */
class CPRPAMessaging extends CHL7v3Messaging {
  /**
   * @var array
   */
  static $versions = array (
    "2008", "2009"
  );

  /**
   * @var array
   */
  static $interaction_ST201317UV = array(
    "IN201307UV02", "IN201308UV02"
  );

  /**
   * @var array
   */
  static $evenements = array(
    //  	Patient Registry Get Demographics Query
    "IN201307UV02" => "CHL7v3EventPRPAIN201307UV02",

    // Patient Registry Get Demographics Query Response
    "IN201308UV02" => "CHL7v3EventPRPAIN201308UV02"
  );

  /**
   * Construct
   *
   * @return CPRPAMessaging
   */
  function __construct() {
    $this->type = "PRPA";

    $this->_categories = array(
      "ST201317UV" => self::$interaction_ST201317UV,
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
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $classname = "CHL7v3EventPRPA{$exchange->code}";

    return new $classname;
  }
}