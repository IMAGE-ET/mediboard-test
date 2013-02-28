<?php

/**
 * Represents a ACK message structure HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventACK
 * Represents a ACK message structure
 */
interface CHL7EventACK {
  /**
   * Construct
   *
   * @param CHL7Event $trigger_event HL7 event
   *
   * @return CHL7EventACK
   */
  function __construct(CHL7Event $trigger_event);

  /**
   * Build ACK event
   *
   * @param CMbObject $object Object
   *
   * @return mixed
   */
  function build($object);
}