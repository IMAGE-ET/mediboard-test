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
   * The maximum length for PDU
   * 
   * @var integer
   */
  var $maximum_length = null;
  
  /**
   * The constructor.
   * 
   * @param array $datas The datas, default null. 
   */
  function __construct($datas = array()) {
    $this->setType("51");
    foreach ($datas as $key => $value) {
      $words = explode('_', $key);
      $method = 'set';
      foreach ($words as $_word) {
        $method .= ucfirst($_word);
      }
      if (method_exists($this, $method)) {
        $this->$method($value);
      }
    }
  }
  
  /**
   * Set the maximum length
   * 
   * @param interer $max_length The maximum length of PDU
   * 
   * @return null
   */
  function setMaximumLength($max_length) {
    $this->maximum_length = $max_length;
  }
  
  /**
   * Return the values of the fields
   * 
   * @return array
   */
  function getValues() {
    return array("maximum_length" => $this->maximum_length);
  }
  
  /**
   * Decode the Maximum Length
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodeItem(CDicomStreamReader $stream_reader) {
    $this->maximum_length = $stream_reader->readUnsignedInt32();
  }
  
  /**
   * Encode the Maximum Length
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
    return "Maximum length :
            <ul>
              <li>Item type : $this->type</li>
              <li>Item length : $this->length</li>
              <li>maximum length : $this->maximum_length</li>
            </ul>";
  }
}
?>