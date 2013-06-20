<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage DICOM
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The Echo message family
 */
class CEcho {
  static $evenements = array(
    "C-Echo-RQ"  => "CDicomMessageCEchoRQ",
    "C-Echo-RSP" => "CDicomMessageCEchoRSP",
  );

  /**
   * The constructor
   *
   * @return self
   */
  function __construct() {
    $this->type = "Echo";
  }
  
  /**
   * Retrieve events list of data format
   * 
   * @return string[] Events list
   */
  function getEvenements() {
    return self::$evenements;
  }
}
