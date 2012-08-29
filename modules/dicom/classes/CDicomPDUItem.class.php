<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Dicom PDU Item
 */
class CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal umber
   */
  var $type = null;
  
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * Decode the item
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   */
  function decodeItem(CDicomStreamReader $stream_reader) {}
  
  /**
   * Encode the item
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   */
  function encodeItem(CDicomStreamWriter $stream_writer) {}
}
?>