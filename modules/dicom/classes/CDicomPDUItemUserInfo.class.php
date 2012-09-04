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
  var $type = "50";
    
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
   * The constructor.
   * 
   * @param array $items Default null. 
   * You can set all the field of the class by passing an array, the keys must be the name of the fields.
   * For the sub items, the value must be an array,
   * with keys the type of the item, and for value and item array.
   */
  function __construct($items = array()) {
    foreach ($items as $_type => $_data) {
      $class = CDicomPDUItemFactory::getItemClass("$_type");
      $this->sub_items[] = new $class($_data);
    }
  }
  
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
    $stream_writer->writeHexByte($this->type, 2);
    $stream_writer->skip(1);
    $stream_writer->writeUnsignedInt16($this->length);
    
    foreach ($this->sub_items as $sub_item) {
      $sub_item->encodeItem($stream_writer);
    }
  }
  
  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->length = 0;
    
    foreach ($this->sub_items as $sub_item) {
      $this->length += $sub_item->getTotalLength();
    }
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