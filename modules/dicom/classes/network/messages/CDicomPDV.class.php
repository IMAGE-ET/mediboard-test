<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * A Presentation Data Value
 * 
 * @see DICOM Standard PS 11_08, section 9.3.5.1 and Annexe E
 */
class CDicomPDV extends CDIcomPDU {
  
  /**
   * The length of the PDV
   * 
   * @var integer
   */
  protected $length = null;
  
  /**
   * The Presentation Context ID
   * 
   * @var integer
   */
  protected $pres_context_id = null;
  
  /**
   * The message control header
   * 
   * @var integer
   */
  protected $message_control_header = null;
  
  /**
   * The different values for the message control header and their signification
   * 
   * @var array
   */
  static $message_control_header_values = array(
    0 => "Data, not last fragment",
    1 => "Command, not last fragment",
    2 => "Data, last fragment",
    3 => "Command, last fragment"
  );
  
  /**
   * The transfer syntax used in this PDV, represented as the UID of the corresponding tranfer syntax
   * 
   * @var string 
   */
  protected $transfer_syntax = null;
  
  /**
   * The message
   * 
   * @var CDicomMessage
   */
  protected $message = null;
  
  /**
   * The constructor.
   * 
   * @param array $datas Default null. 
   * You can set all the field of the class by passing an array, the keys must be the name of the fields.
   */
  function __construct(array $datas = array()) {
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
   * Return the length
   * 
   * @return integer
   */
  function getLength() {
    return $this->length;
  }
  
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
   * Return the presentation context id
   * 
   * @return integer
   */
  function getPresContextId() {
    return $this->pres_context_id;
  }
  
  /**
   * Set the presentation context id
   * 
   * @param integer $id The presentation context id
   * 
   * @return null
   */
  function setPresContextId($id) {
    $this->pres_context_id = $id;
  }
  
  /**
   * Return the message control header
   * 
   * @return integer
   */
  function getMessageControlHeader() {
    return $this->message_control_header;
  }
  
  /**
   * Set the message control header
   * 
   * @param integer $header The message control header
   * 
   * @return null
   */
  function setMessageControlHeader($header) {
    $this->message_control_header = $header;
  }
  
  /**
   * Return the transfer syntax UID
   * 
   * @return string
   */
  function getTransferSyntax() {
    return $this->transfer_syntax;
  }
  
  /**
   * Set the transfer syntax
   * 
   * @param string $transfer_syntax The transfer syntax's UID
   * 
   * @return null
   */
  function setTransferSyntax($transfer_syntax) {
    $this->transfer_syntax = $transfer_syntax;
  }
  
  /**
   * Return the message
   * 
   * @return CDicomService
   */
  function getMessage() {
    return $this->message;
  }
  
  /**
   * Set the message
   * 
   * @param array $datas Must contain 2 things, the type of the message, with the key "type",
   * and the messages datas, an array, with the key "datas"
   * 
   * @return null
   */
  function setMessage($datas) {
    $message_class = CDicomMessageFactory::getMessageClass($datas["type"]);  
    $this->message = new $message_class($datas["datas"]);
  }
  
  /**
   * Calculate the length of the pdv, without the field "length"
   * 
   * @return null
   */
  protected function calculateLength() {
    $this->length = 2 + $this->message->getTotalLength();
  }
  
  /**
   * Return the total length of the pdv
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
   * Encode the PDV
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @return null
   */
  function encode(CDicomStreamWriter $stream_writer) {
    $this->calculateLength();
    
    $stream_writer->writeUInt32($this->length);
    $stream_writer->writeUInt8($this->pres_context_id);
    $stream_writer->writeUInt8($this->message_control_header);
    
    $this->message->encode($stream_writer, $this->transfer_syntax);
  }
  
  /**
   * Decode the PDV
   * 
   * @param CDicomStreamReader $stream_reader	The stream reader
   * 
   * @return null
   */
  function decode(CDicomStreamReader $stream_reader) {
    // On fait un stream temp pour le message
    $this->length = $stream_reader->readUInt32();
    $this->pres_context_id = $stream_reader->readUInt8();
    $this->message_control_header = $stream_reader->readUInt8();
    
    $message_length = $this->length - 2;
    $message_content = $stream_reader->read($message_length);
    
    $handle = fopen("php://temp", "w+");
    fwrite($handle, $message_content);
    
    $message_stream = new CDicomStreamReader($handle);
    $message_stream->setStreamLength($message_length);
    
    $this->message = CDicomMessageFactory::encodeMessage($message_stream, $this->transfer_syntax);
  }
  
  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    $str = "PDV :
            <ul>
              <li>Length : $this->length</li>
              <li>Presentation context ID : $this->pres_context_id</li>
              <li>Message control header : ". self::$message_control_header_values[$this->message_control_header] . "</li>
              <li>" . $this->message->__toString() . "</li>
            </ul>";
    return $str;
  }
}