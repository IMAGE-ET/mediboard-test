<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem | llemoine
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * The PDU Factory, who matches the type of PDU and the corresponding PHP class
 * 
 * @todo Changer $pdu_types en un switch, et lever une exception en cas de type inconnu 
 */
class CDicomPDUFactory {
  
  /**
   * Get the type of the PDU, and create the corresponding CDicomPDU
   * 
   * @param string $pdu_content           The datas sent by the client
   * 
   * @param array  $presentation_contexts The presentation contexts
   * 
   * @return CDicomPDU The PDU
   */
  static function decodePDU($pdu_content, $presentation_contexts = null) {
    $stream = fopen("php://temp", 'w+');
    fwrite($stream, $pdu_content);
    
    $stream_reader = new CDicomStreamReader($stream);
    $stream_reader->rewind();
    
    $pdu_class = self::readType($stream_reader);
    if ($pdu_class == "Unknown type!") {
      return null;
    }
    $length = self::readLength($stream_reader);

    $pdu = new $pdu_class(array("length" => $length));
    
    if ($pdu_class == "CDicomPDUPDataTF") {
      $pdu->setPresentationContexts($presentation_contexts);
    }
    
    $pdu->decodePDU($stream_reader);
    
    $stream_reader->close();
    
    $pdu->setPacket($stream_reader->buf);
    
    return $pdu;
  }
  
  /**
   * Create a PDU of the given type
   * 
   * @param string $type                  The type of the PDU you want to create
   * 
   * @param array  $datas                 The differents datas of the PDU
   * 
   * @param array  $presentation_contexts The presentation context
   * 
   * @return CDicomPDU The PDU
   */
  static function encodePDU($type, $datas = array(), $presentation_contexts = null) {
    $stream = fopen("php://temp", 'w+');
    
    $stream_writer = new CDicomStreamWriter($stream);

    $pdu_class = self::getPDuClass($type);
    
    $pdu = new $pdu_class($datas);
    
    if ($pdu_class == "CDicomPDUPDataTF") {
      $pdu->setPresentationContexts($presentation_contexts);
    }
    
    $pdu->encodePDU($stream_writer);
    
    $pdu->setPacket($stream_writer->buf);
    $stream_writer->close();
    
    return $pdu;
  }
  
  /**
   * Read the type of the PDU from the stream
   * 
   * @param CDicomStreamReader $stream The stream reader
   * 
   * @return string
   */
  static function readType(CDicomStreamReader $stream) {
    $tmp = $stream->readUInt8();
    $stream->skip(1);
    return self::getPDUClass($tmp);
  }
  
  /**
   * Read the length of the PDU from the stream
   * 
   * @param CDicomStreamReader $stream The stream reader
   * 
   * @return integer
   */
  static function readLength(CDicomStreamReader $stream) {
    return $stream->readUInt32();
  }
  
  /**
   * Make the link between the code types and the PDU classes
   * 
   * @param string $type The type of PDU
   * 
   * @return string
   */
  static function getPDUClass($type) {
    $class = "";
    switch ($type) {
      case 0x01 :
        $class = "CDicomPDUAAssociateRQ";
        break;
      case 0x02 :
        $class = "CDicomPDUAAssociateAC";
        break;
      case 0x03 :
        $class = "CDicomPDUAAssociateRJ";
        break;
      case 0x04 :
        $class = "CDicomPDUPDataTF";
        break;
      case 0x05 :
        $class = "CDicomPDUAReleaseRQ";
        break;
      case 0x06 :
        $class = "CDicomPDUAReleaseRP";
        break;
      case 0x07 :
        $class = "CDicomPDUAAbort";
        break;
        
      default:
        $class = "Unknown type!";
        break;
    }
    return $class;
  }
}
?>
