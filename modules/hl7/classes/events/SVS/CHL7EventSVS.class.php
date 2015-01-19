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
 * Event SVS - Sharing Value Sets
 */
interface CHL7EventSVS {
  /**
   * Construct
   *
   * @return CHL7EventSVS
   */
  function __construct();

  /**
   * Build SVS message
   *
   * @param CMbObject $object object
   *
   * @return mixed
   */
  function build($object);
}
