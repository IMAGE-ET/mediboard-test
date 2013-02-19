<?php

/**
 * Patient Demographics Query Cancel Query HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventQCN
 * Patient Demographics Query Cancel Query
 */
interface CHL7EventQCN {
  /**
   * Construct
   *
   * @return CHL7EventQCN
   */
  function __construct();

  /**
   * Build QCN message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}