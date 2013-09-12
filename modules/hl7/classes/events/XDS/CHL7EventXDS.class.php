<?php

/**
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Event XDS
 */
interface CHL7EventXDS {
  /**
   * Construct
   *
   * @return CHL7EventXDS
   */
  function __construct();

  /**
   * Build XDS message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}
