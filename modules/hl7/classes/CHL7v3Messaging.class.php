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
 * Class CHL7v3Messaging
 * Patient Administration
 */
class CHL7v3Messaging extends CInteropNorm {
  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->name = "CHL7v3Messaging";

    parent::__construct();
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @throws CMbException
   *
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    switch ($exchange->type) {
      case "PRPA":
        return CPRPA::getEvent($exchange);

      default:
        throw new CMbException("CHL7v3Messaging_event-unknown");
    }
  }
}