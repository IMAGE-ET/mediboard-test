<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Transfer Syntax PDU Item
 */
class CDicomPDUItemTransferSyntax extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = 0x40;
  
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * The transfer syntax name, coded as a UID
   * 
   * @var string
   */
  var $name = null;
  
  /**
   * Decode the Transfer Syntax
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodeItem(CDicomStreamReader $stream_reader) {
    $stream_reader->skip(1);
    $this->length = $stream_reader->readUnsignedInt16();
    $this->name = $stream_reader->readUID($this->length);
  }
  
  /**
   * Encode the Transfer Syntax
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   *  
   * @return null
   */
  function encodeItem(CDicomStreamWriter $stream_writer) {
    
  }

  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    return "<ul><li>Item type : $this->type</li><li>Item length : $this->length</li><li>Transfer syntax name : $this->name</li></ul>";
  }
}
?>