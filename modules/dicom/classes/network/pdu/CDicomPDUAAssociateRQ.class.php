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
   * @var array of CDicomPDUItemPresentationContextRQ
   */
  var $presentation_contexts = array();
  
  /**
   * The User informations
   * 
   * @var CDicomPDUItemUserInfo
   */
  var $user_info = null; 
  
  /**
   * The constructor.
   * 
   * @param array $datas Default null. 
   * You can set all the field of the class by passing an array, the keys must be the name of the fields.
   */
  function __construct(array $datas = array()) {
    $this->setType("01");
    $this->setTypeStr("A-Associate-RQ");
    foreach ($datas as $key => $value) {
      $words = explode('_', $key);
      $method = 'set';
      foreach ($words as $_word) {
        $method .= ucfirst($_word);
      }
      if (method_exists($this, $method)) {
        $this->$method($value);
      }
    }
  }
  
  /**
   * Set the protocol version
   * 
   * @param integer $protocol_version The protocol_version
   *  
   * @return null
   */
  function setProtocolVersion($protocol_version) {
    $this->protocol_version = $protocol_version;
  }
  
  /**
   * Set the called AE title
   * 
   * @param integer $called_AE_title The called AE title
   * 
   * @return null
   */
  function setCalledAETitle($called_AE_title) {
    $this->called_AE_title = $called_AE_title;
  }
  
  /**
   * Set the calling application entity
   * 
   * @param integer $calling_AE_title The calling AE title
   * 
   * @return null
   */
  function setCallingAETitle($calling_AE_title) {
    $this->calling_AE_title = $calling_AE_title;
  }
  
  /**
   * Set the application context
   * 
   * @param array $datas The data for create the application context
   * 
   * @return null
   */
  function setApplicationContext($datas) {
    $this->application_context = new CDicomPDUItemApplicationContext($datas);
  }
  
  /**
   * Set the presentation context
   * 
   * @param array $pres_contexts The datas for create the transfer syntaxes
   * 
   * @return null
   */
  function setPresentationContexts($pres_contexts) {
    foreach ($pres_contexts as $datas) {
      $this->presentation_contexts[] = new CDicomPDUItemPresentationContext($datas);
    }
  }
  
  /**
   * Set the user informations
   * 
   * @param array $datas The data for create the user informations
   * 
   * @return null
   */
  function setUserInfo($datas) {
    $this->user_info = new CDicomPDUItemTransferSyntax($datas);
  }
  
  /**
   * Decode the PDU
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   *  
   * @return null
   */
  function decodePDU(CDicomStreamReader $stream_reader) {
    $this->protocol_version = $stream_reader->readHexByte(2);
    
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
    $this->calculateLength();
    
    $stream_writer->writeHexByte($this->type, 2);
    $stream_writer->skip(1);
    $stream_writer->writeUInt32($this->length);
    $stream_writer->skip(1);
    $stream_writer->writeUInt16($this->protocol_version);
    $stream_writer->skip(2);
    $stream_writer->writeUInt16($this->called_AE_title);
    $stream_writer->writeUInt8($this->calling_AE_title);
    $stream_writer->skip(32);
    $this->application_context->encodeItem($stream_writer);
    foreach ($this->presentation_contexts as $_item) {
      $item->encodeItem($stream_writer);
    }
    $this->user_info->encodeItem($stream_writer);
  }

  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->length = 68 + $this->application_context->getTotalLength();
    
    foreach ($this->presentation_contexts as $_item) {
      $this->length += $_item->getTotalLength();
    }
    
    $this->length += $this->user_info->getTotalLength();
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
    return $this->length + 6;
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
              <li>{$this->application_context->__toString()}</li>";
    foreach ($this->presentation_contexts as $pres_context) {
      $str .= "<li>{$pres_context->__toString()}</li>";
    }
    $str .= "<li>{$this->user_info->__toString()}</li></ul>";
    return $str;
  }
}
?>