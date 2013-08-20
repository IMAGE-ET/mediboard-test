<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CXDSProducer {

  /** @var array */
  static $evenements = array (
    // Provider&register Document Set - b
    "ITI-41" => "CXDSEventITI41",
  );

  /**
   * Construct
   */
  function __construct() {
    $this->type = "producer";
  }

  /**
   * Retrieve events list of data format
   *
   * @return array Events list
   */
  function getEvenements() {
    return self::$evenements;
  }
}