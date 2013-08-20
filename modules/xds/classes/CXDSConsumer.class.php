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
class CXDSConsumer {

  /** @var array */
  static $evenements = array (
    // retrieve Document Set
    "ITI-43" => "CXDSEventITI43",
    //Registry Stored Query
    "ITI-18" => "CXDSEventITI18",
  );

  /**
   * Construct
   */
  function __construct() {
    $this->type = "consumer";
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
