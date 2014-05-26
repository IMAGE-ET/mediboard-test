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
 * Event XDSb
 */
interface CHL7EventXDSb {
  /**
   * Construct
   *
   * @return CHL7EventXDSb
   */
  function __construct();

  /**
   * Build XDSb message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}
