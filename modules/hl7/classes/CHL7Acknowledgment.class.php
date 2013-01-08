<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Interface CHL7v2Acknowledgment 
 * Acknowledgment HL7
 */
interface CHL7Acknowledgment {
  function __construct(CHL7Event $event = null);

  function getStatutAcknowledgment();
}
