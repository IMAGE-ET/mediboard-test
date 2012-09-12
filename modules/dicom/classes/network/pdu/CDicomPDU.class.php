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
   * The type of the PDU
   * 
   * @var string
   */
  var $type = null;
  
  /**
   * The length of the PDU
   * 
   * @var integer
   */
  var $length = null;
  
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