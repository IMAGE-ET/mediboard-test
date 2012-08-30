<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a User Info PDU Item
 */
class CDicomPDUItemUserInfo extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = 0x50;
    
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * An array of differents items
   * 
   * @var array of CDicomPDUItem
   */
  var $sub_items = array();
  
  /**
   * Decode the Transfer Syntax
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodeItem(CDicomStreamReader $stream_reader) {
    // On passe le 2ème octet, réservé par Dicom et égal à 00
    $stream_reader->skip(1);
    $this->length = $stream_reader->readUnsignedInt16();

    $this->sub_items = CDicomPDUItemFactory::decodeConsecutiveItemsByLength($stream_reader, $this->length);
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
    $str = "<ul>
              <li>Item type : $this->type</li>
              <li>Item length : $this->length</li>";
    foreach ($this->sub_items as $item) {
      $str .= "<li>{$item->__toString()}</li>";
    }
    $str .= "</ul>";
    return $str;
  }
}
?>