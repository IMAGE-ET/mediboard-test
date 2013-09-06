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
class CXDSRepository {

  /** @var array */
  static $evenements = array (
    "ITI-43" => "CXDSEventITI43",
    "ITI-41" => "CXDSEventITI41",
  );

  /**
   * Construct
   */
  function __construct() {
    $this->type = "repository";
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
