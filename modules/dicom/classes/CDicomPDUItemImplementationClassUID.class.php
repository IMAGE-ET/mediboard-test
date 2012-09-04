<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Implementation Class UID PDU Item
 */
class CDicomPDUItemImplementationClassUID extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = "52";
  
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * The implementation class uid
   * 
   * @var integer
   */
  var $uid = null;
  
  /**
   * The constructor.
   * 
   * @param string $uid The uid, default null. 
   */
  function __construct($uid = null) {
    if ($uid) {
      $this->uid = $uid;
    }  
  }
  
  /**
   * Return the UID
   * 
   * @return integer
   */
  function getValue() {
    return $this->uid;
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
    $this->uid = $stream_reader->readUID($this->length);
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
    $stream_writer->writeUID($this->uid, $this->length);
  }

  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->length = strlen($this->uid);
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
    return "Implementation class UID : <ul><li>Item type : $this->type</li><li>Item length : $this->length</li><li>UID : $this->uid</li></ul>";
  }
}
?>