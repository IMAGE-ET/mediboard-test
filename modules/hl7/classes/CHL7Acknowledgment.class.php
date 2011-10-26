<?php

/**
 * Acknowledgment HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7v2Acknowledgment 
 * Acknowledgment HL7
 */
interface CHL7Acknowledgment {
  function __construct(CHL7Event $event); 
  
  function generateAcknowledgment($ack_code, $mb_error_code, $hl7_error_code = null, $severity = "E", $comments = null, $object = null);
  
  function getStatutAcknowledgment();
}

?>