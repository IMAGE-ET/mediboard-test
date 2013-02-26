<?php

/**
 * Represents a ORR message structure HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventORR
 * Represents a ORR message structure
 */
interface CHL7EventORR {
  /**
   * Construct
   *
   * @param CHL7Event $trigger_event Trigger event
   *
   * @return CHL7EventORR
   */
  function __construct(CHL7Event $trigger_event);

  /**
   * Build ORR message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}