<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
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
   * @return null
   */
  function __construct() {
    $this->type = "Echo";
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
?>