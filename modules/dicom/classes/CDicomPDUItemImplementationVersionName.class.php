<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Implementation Version Name PDU Item
 */
class CDicomPDUItemImplementationVersionName extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = 0x55;
  
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * The maximum length for PDU
   * 
   * @var integer
   */
  var $version_name = null;
  
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
    $this->version_name = $stream_reader->readString($this->length);
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
    return "Implementation version name : <ul><li>Item type : $this->type</li><li>Item length : $this->length</li><li>Version name : $this->version_name</li></ul>";
  }
}
?>