<?php

/**
 * Message XML HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7MessageXML
 * Message XML HL7
 */
interface CHL7MessageXML {
  function getContentsXML();
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data);
}

?>