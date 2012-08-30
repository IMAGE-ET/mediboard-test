<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * An A-Associate-RQ PDU
 */
class CDicomPDUAAssociateRQ extends CDicomPDU {
  
  /**
   * The type of the PDU
   * 
   * @var hexadecimal number
   */
  var $type = 0x01;
  
  /**
   * The length of the PDU
   * 
   * @var integer
   */
  var $length = null;
  
  /**
   * Protocol version, must be equal to 1
   * 
   * @var integer
   */
  var $protocol_version = null;
  
  /**
   * The called application entity
   * 
   * @var string
   */
  var $called_AE_title = null;
  
  /**
   * The calling application entity
   * 
   * @var string
   */
  var $calling_AE_title = null;
  
  /**
   * The application context
   * 
   * @var CDicomPDUItemApplicationContext
   */
  var $application_context = null;
  
  /**
   * The presentation contexts
   * 
   * @var array of CDicomPDUItemPresentationContext
   */
  var $presentation_contexts = array();
  
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
    $this->presentation_contexts = CDicomPDUItemFactory::decodeConsecutiveItemsByType($stream_reader, "20");
    $this->user_info = CDicomPDUItemFactory::decodeItem($stream_reader);
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
  
  /**
   * Return a string representation of the class
   * 
   * @return string
   */
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
    $str .= "<li>User Info : {$this->user_info->__toString()}</ul>";
    return $str;
  }
}
?>