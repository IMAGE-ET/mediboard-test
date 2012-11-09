<?php /** $Id$ **/

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
   * @var integer
   */
  var $type = null;
  
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * Set the type
   * 
   * @param string $type The type
   *  
   * @return void
   */
  function setType($type) {
    $this->type = $type;
  }
  
  /**
   * Set the length
   * 
   * @param integer $length The length
   *  
   * @return void
   */
  function setLength($length) {
    $this->length = $length;
  }
  
  /**
   * Decode the item
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return void
   */
  function decodeItem(CDicomStreamReader $stream_reader) {
    
  }
  
  /**
   * Encode the item
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @return void
   */
  function encodeItem(CDicomStreamWriter $stream_writer) {
    
  }
}
?>