<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

 /**
  * The Sub field Related SOP Class Identification
  */
class CDicomSubFieldRelatedSopClassIdentification {
  
  /**
   * The related general SOP class UID length
   * 
   * @var integer
   */
  var $class_uid_length = null;
   
  /**
   * The related general SOP class UID
   * 
   * @var string
   */
  var $class_uid = null;
  
  /**
   * The constructor of the sub field
   * 
   * @param string $class_uid The class uid
   */
  function __construct($class_uid = null) {
    if (!$class_uid) {
      return;
    }
    $this->class_uid = $class_uid;
    $this->class_uid_length = strlen($this->class_uid);
  }
  
  /**
   * Return the class UID
   * 
   * @return string
   */
  function getClassUID() {
    return $this->class_uid;
  }
  
  /**
   * Calculate the length
   * 
   * @return null
   */
  function calculatelength() {
    $this->class_uid_length =  2 + strlen($this->class_uid);
  }
   
   /**
   * Return the length, in number of bytes
   * 
   * @return integer
   */
  function getLength() {
    if (!$this->length) {
      $this->calculateLength();
    }
    return $this->length;
  }
  
  /**
   * Decode the field
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodeField(CDicomStreamReader $stream_reader) {
    $this->class_uid_length = $stream_reader->readUInt8();
    $this->class_uid = $stream_reader->readUID($this->class_uid_length);
  }
  
  /**
   * Encode the field
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @return null
   */
  function encodeField(CDicomStreamWriter $stream_writer) {
    $stream_writer->writeUInt8($this->class_uid_length);
    $stream_writer->writeUID($this->class_uid, $this->class_uid_length);
  }
  
  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    return "<ul>
              <li>SOP class UID length : $this->class_uid_length</li>
              <li>SOP class UID : $this->class_uid_length</li>
            </ul>";
  }
}
?>