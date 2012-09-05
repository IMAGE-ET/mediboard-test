<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * An A-Associate-RJ PDU
 */
class CDicomPDUAReleaseRP extends CDIcomPDU {
  
  /**
   * The type of the PDU
   * 
   * @var hexadecimal number
   */
  var $type = "06";
  
  /**
   * The length of the PDU
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * Decode the PDU
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   *  
   * @return null
   */
  function decodePDU(CDicomStreamReader $stream_reader) {
    // On passe le 2ème octet, réservé par Dicom et égal à 00
    $stream_reader->skip(1);
    
    $this->length = $stream_reader->readUnsignedInt32();
    $stream_reader->skip(4);
  }
  
  /**
   * Encode the PDU
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   *  
   * @return null
   */
  function encodePDU(CDicomStreamWriter $stream_writer) {
    $this->calculateLength();
    
    $stream_writer->writeHexByte($this->type, 2);
    $stream_writer->skip(1);
    $stream_writer->writeUnsignedInt32($this->length);
    $stream_writer->skip(4);
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
    return $this->length + 6;
  }
  
  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    $str = "<h1>A-Release-RP</h1><br>
            <ul>
              <li>Type : $this->type</li>
              <li>Length : $this->length</li>
            </ul>";
    return $str;
  }
}
?>