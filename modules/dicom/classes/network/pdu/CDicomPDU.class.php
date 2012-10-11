<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem | llemoine
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * Represent a DICOM PDU (Protocol Data Unit)
 */
class CDicomPDU {
  
  /**
   * The type of the PDU, in number
   * 
   * @var string
   */
  var $type = null;
  
  /**
   * The type of the PDU, in string
   * 
   * @var string
   */
  var $type_str = null;
  
  /**
   * The length of the PDU
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * The encoded pdu
   * 
   * @var string
   */
  protected $packet = null;
  
  /**
   * Set the length
   * 
   * @param integer $length The length
   *  
   * @return null
   */
  function setLength($length) {
    $this->length = $length;
  }
  
  /**
   * Set the type
   * 
   * @param string $type The type of the PDU
   *  
   * @return null
   */
  function setType($type) {
    $this->type = $type;
  }
  
  /**
   * Set the type
   * 
   * @param string $type The type of the PDU
   *  
   * @return null
   */
  function setTypeStr($type) {
    $this->type_str = $type;
  }
  
  /**
   * Return the encoded pdu
   * 
   * @return string
   */
  function getPacket() {
    return $this->packet;
  }
  
  /**
   * Set the encoded pdu
   * 
   * @param string $packet The encoded packet
   * 
   * @return null
   */
  function setPacket($packet) {
    $this->packet = $packet;
  }
  
  /**
   * Decode the PDU
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodePDU(CDicomStreamReader $stream_reader) {
    
  }
  
  /**
   * Encode the PDU
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @return null
   */
  function encodePDU(CDicomStreamWriter $stream_writer) {
    
  }
}
?>