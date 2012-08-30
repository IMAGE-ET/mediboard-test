<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Presentation Syntax PDU Item
 */
class CDicomPDUItemPresentationContext extends CDicomPDUItem {
  
  /**
   * The type of the Item
   * 
   * @var hexadecimal number
   */
  var $type = 0x20;
    
  /**
   * The length of the Item
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * The id of the presentation context
   * 
   * @var integer
   */
  var $id = null;
  
  /**
   * The abstract syntax
   * 
   * @var CDicomPDUItemAbstractSyntax
   */
  var $abstract_syntax = null;
  
  /**
   * The transfer syntaxes
   * 
   * @var array of CDicomPDUItemTransferSyntax
   */
  var $transfer_syntaxes = array();
  
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
    $this->id = $stream_reader->readUnsignedInt8();
    $stream_reader->skip(3);
    
    $this->abstract_syntax = CDicomPDUItemFactory::decodeItem($stream_reader);
    $this->transfer_syntaxes = CDicomPDUItemFactory::decodeConsecutiveItemsByType($stream_reader, "40");
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
              <li>Item length : $this->length</li>
              <li>id : $this->id</li>
              <li>Abstract syntax : 
                {$this->abstract_syntax->__toString()}
              </li>";
    foreach ($this->transfer_syntaxes as $transfer_syntax) {
      $str .= "<li>Transfer syntax : {$transfer_syntax->__toString()}</li>";
    }
    $str .= "</ul>";
    return $str;
  }
}
?>