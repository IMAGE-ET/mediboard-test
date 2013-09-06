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
 * Classe Producer
 */
class CXDSRegistry {

  /** @var array */
  static $evenements = array (
    "ITI-18" => "CXDSEventITI18",
    "ITI-57" => "CXDSEventITI57",
  );

  /**
   * Construct
   */
  function __construct() {
    $this->type = "registry";
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