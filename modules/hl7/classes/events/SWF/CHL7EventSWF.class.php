<?php

/**
 * Scheduled Workflow HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventSWF 
 * Scheduled Workflow
 */
interface CHL7EventSWF {
  function __construct();
  
  function build($object);
}

?>