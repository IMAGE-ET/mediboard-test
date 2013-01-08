<?php

/**
 * Represents a RSP message structure HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventRSP
 * Represents a RSP message structure
 */
interface CHL7EventRSP {
  function __construct(CHL7Event $trigger_event);
  
  function build($object);
}

?>