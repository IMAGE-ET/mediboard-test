<?php

/**
 * Event HL7v3
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3Event
 * Event HL7v3
 */
class CHL7v3Event extends CHL7Event {
  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
  }

  /**
   * Handle event
   *
   * @param string $msg_hl7 HL7 message
   *
   * @return DOMDocument|void
   */
  function handle($msg_hl7) {
  }

  /**
   * Generate exchange HL7v3
   *
   * @return CExchangeHL7v3
   */
  function generateExchange() {
  }

  /**
   * Update exchange HL7v3 with
   *
   * @return CExchangeHL7v3
   */
  function updateExchange() {
  }
}