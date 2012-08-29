<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

class CDicomPDUItemAbstractSyntax extends CDicomPDUItem {
  
  var $type = 0x30;
  var $length = null;
  var $name = null;
  
  function decodeItem(CDicomStreamReader $stream_reader) {
    // On passe le 2ème octet, réservé par Dicom et égal à 00
    $stream_reader->skip(1);
    $this->length = $stream_reader->readUnsignedInt16();
    $this->name = $stream_reader->readUID($this->length);
  }
  
  function encodeItem(CDicomStreamWriter $stream_writer) {}

  function __toString() {
    return "<ul><li>Item type : $this->type</li><li>Item length : $this->length</li><li>Abstract syntax name : $this->name</li></ul>";
  }
}
?>