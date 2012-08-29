<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

class CDicomPDUAAssociateRQ extends CDicomPDU {
  
  var $type = 0x01;
  var $length = null;
  var $protocol_version = null;
  var $called_AE_title = null;
  var $calling_AE_title = null;
  var $application_context = null;
  var $presentation_contexts = array();
  
  function decodePDU(CDicomStreamReader $stream_reader) {
    // On passe le 2ème octet, réservé par Dicom et égal à 00
    $stream_reader->skip(1);
    $this->length = $stream_reader->readUnsignedInt32();
    $this->protocol_version = $stream_reader->readHexByte(2);
    
    // On vérifie que la version du protocole est bien 0001
    if ($this->protocol_version != 0001) {
      // Erreur
      echo "Protocol version differente de 1";
    }
    
    $stream_reader->skip(2);
    
    $this->called_AE_title = $stream_reader->readString(16);
    
    // On test si called_AE_title = AE title du serveur
    
    $this->calling_AE_title = $stream_reader->readString(16);
    
    
    // On passe 32 octets, réservés par Dicom
    $stream_reader->skip(32);
    
    $this->application_context = CDicomPDUItemFactory::decodeItem($stream_reader);
    $this->presentation_contexts = CDicomPDUItemFactory::decodeItems($stream_reader, "20");
    $this->user_info = CDicomPDUItemFactory::decodeItem($stream_reader);
  }
  
  function encodePDU(CDicomStreamWriter $stream_writer) {}
  
  function __toString() {
    $str = "<h1>A-Associate-RQ</h1><br>
            <ul>
              <li>Type : $this->type</li>
              <li>Length : $this->length</li>
              <li>Called AE title : $this->called_AE_title</li>
              <li>Calling AE title : $this->calling_AE_title</li>
              <li>Application Context : 
                {$this->application_context->__toString()}
              </li>";
    foreach ($this->presentation_contexts as $pres_context) {
      $str .= "<li>Presentation context : {$pres_context->__toString()}";
    }
    $str .= "</ul>";
    return $str;
  }
}
?>