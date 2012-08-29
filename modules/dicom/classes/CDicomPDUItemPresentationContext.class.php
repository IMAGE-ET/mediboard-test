<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

class CDicomPDUItemPresentationContext extends CDicomPDUItem {
  
  var $type = 0x20;
  var $length = null;
  var $id = null;
  var $abstract_syntax = null;
  var $transfer_syntaxes = array();
  
  function decodeItem(CDicomStreamReader $stream_reader) {
    // On passe le 2ème octet, réservé par Dicom et égal à 00
    $stream_reader->skip(1);
    $this->length = $stream_reader->readUnsignedInt16();
    $this->id = $stream_reader->readUnsignedInt8();
    $stream_reader->skip(3);
    
    $this->abstract_syntax = CDicomPDUItemFactory::decodeItem($stream_reader);
    $this->transfer_syntaxes = CDicomPDUItemFactory::decodeItems($stream_reader, "40");
  }
  
  function encodeItem(CDicomStreamWriter $stream_writer) {}

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