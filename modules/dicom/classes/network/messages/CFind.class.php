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
 * The Find message family
 */
class CFind {
  static $evenements = array(
    "C-Find-RQ"         => "CDicomMessageCFindRQ",
    "C-Find-RSP"        => "CDicomMessageCFindRSP",
    "C-Cancel-Find-RQ"  => "CDicomMessageCCancelFindRQ",
    "Datas"             => "CDicomMessageCFindData",
  );

  /**
   * The constructor
   *
   * @return \CFind
   */
  function __construct() {
    $this->type = "Find";
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