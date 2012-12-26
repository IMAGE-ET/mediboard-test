<?php

/**
 * Patient Demographics Query HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventPDQ
 * Patient Demographics Query
 */
interface CHL7EventPDQ {
  function __construct();
  
  function build($object);
}

?>