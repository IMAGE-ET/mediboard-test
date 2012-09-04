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
  var $type = "55";
  
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
   * The constructor
   * 
   * @param integer $version_name The version name, default null
   */
  function  __construct($version_name = null) {
    if ($version_name) {
      $this->version_name = $version_name;
    }
  }
  
  /**
   * Return the version name
   * 
   * @return integer
   */
  function getValue() {
    return $this->version_name;
  }
  
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
    $this->calculateLength();
    
    $stream_writer->writeHexByte($this->type, 2);
    $stream_writer->skip(1);
    $stream_writer->writeUnsignedInt16($this->length);
    $stream_writer->writeString($this->version_name, $this->length);
  }

  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->length = strlen($this->version_name);
  }

  /**
   * Return the total length, in number of bytes
   * 
   * @return integer
   */
  function getTotalLength() {
    if (!$this->length) {
      $this->calculateLength();
    }
    return $this->length + 4;
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