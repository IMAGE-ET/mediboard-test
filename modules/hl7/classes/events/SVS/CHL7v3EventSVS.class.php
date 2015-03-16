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
class CHL7v3EventSVS extends CHL7v3Event implements CHL7EventSVS {
  /**
   * Construct
   *
   * @return \CHL7v3EventSVS
   */
  function __construct() {
    parent::__construct();

    $this->event_type = "SVS";
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
    $this->dom = new CHL7v3MessageXML("utf-8", $this->version);

    parent::build($object);
  }
}
