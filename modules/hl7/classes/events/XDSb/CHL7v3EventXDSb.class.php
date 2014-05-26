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
class CHL7v3EventXDSb extends CHL7v3Event implements CHL7EventXDSb {
  /**
   * Construct
   *
   * @return \CHL7v3EventXDSb
   */
  function __construct() {
    parent::__construct();

    $this->event_type = "XDSb";
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);
  }
}
