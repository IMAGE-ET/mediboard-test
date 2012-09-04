<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Maximum Length PDU Item
 */
class CDicomPDUItemMaximumLength extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = "51";
  
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
  var $maximum_length = null;
  
  /**
   * The constructor
   * 
   * @param integer $maximum_length The maximum_length, default null
   */
  function __construct($maximum_length = null) {
    if ($maximum_length) {
      $this->maximum_length = $maximum_length;
    }
  }
  
  /**
   * Return the maximum length
   * 
   * @return integer
   */
  function getValue() {
    return $this->maximum_length;
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
    $this->maximum_length = $stream_reader->readUnsignedInt32();
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
    $stream_writer->writeUnsignedInt32($this->maximum_length);
  }

  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->length = 4;
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
    return "Maximum length : <ul><li>Item type : $this->type</li><li>Item length : $this->length</li><li>maximum length : $this->maximum_length</li></ul>";
  }
}
?>