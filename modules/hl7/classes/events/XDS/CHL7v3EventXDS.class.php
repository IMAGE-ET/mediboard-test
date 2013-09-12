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
class CHL7v3EventXDS extends CHL7v3Event implements CHL7EventXDS {
  /**
   * Construct
   *
   * @return \CHL7v3EventXDS
   */
  function __construct() {
    parent::__construct();

    $this->event_type = "XDS";
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
