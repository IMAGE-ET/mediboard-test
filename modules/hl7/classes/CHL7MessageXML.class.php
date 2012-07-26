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
 * Interface CHL7MessageXML
 * Message XML HL7
 */
interface CHL7MessageXML {
  function getContentNodes();
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data);
}
