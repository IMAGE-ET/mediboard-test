<?php

/**
 * Patient Registry
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Interface CHL7EventPRPA
 * Patient Registry
 */
interface CHL7EventPRPA {
  /**
   * Construct
   *
   * @return CHL7EventPRPA
   */
  function __construct();

  /**
   * Build PRPA message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}