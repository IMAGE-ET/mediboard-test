<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * An P-Data-TF PDU
 */
class CDicomPDUPDataTF extends CDicomPDU {
 
  /**
   * The presentation data value
   * 
   * @var CDicomPDV
   */
  protected $pdv = array();
  
  /**
   * The presentation contexts
   * 
   * @var array 
   */
  protected $presentation_contexts = null;
 
  /**
   * The constructor.
   * 
   * @param array $datas Default null. 
   * You can set all the field of the class by passing an array, the keys must be the name of the fields.
   */
  function __construct(array $datas = array()) {
    $this->setType(0x04);
    $this->setTypeStr("P-Data-TF");
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
   * Set the PDV
   * 
   * @param array $pdv_datas The pdv datas
   * 
   * @return null
   */
  function setPDV($pdv_datas) {
    $this->pdv = new CDicomPDV($pdv_datas);
  }
  
  /**
   * Return the PDV
   * 
   * @return CDicomPDV
   */
  function getPDV() {
    return $this->pdv;
  }
  
  /**
   * Return the presentation contexts
   * 
   * @return array 
   */
  function getPresentationContexts() {
    return $this->presentation_contexts;
  }
  
  /**
   * Set the transfer syntax
   * 
   * @param array $presentation_contexts The presentation contexts
   * 
   * @return null
   */
  function setPresentationContexts($presentation_contexts) {
    $this->presentation_contexts = $presentation_contexts;
  }
  
  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->length = $this->pdv->getTotalLength();
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
   * Decode the PDU
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   *  
   * @return null
   */
  function decodePDU(CDicomStreamReader $stream_reader) {
    $this->pdv = new CDicomPDV(array("presentation_contexts" => $this->presentation_contexts));
    $this->pdv->decode($stream_reader);
  }
  
  /**
   * Encode the PDU
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   *  
   * @return null
   */
  function encodePDU(CDicomStreamWriter $stream_writer) {
    $handle = fopen("php://temp", "w+");
    $pdv_stream = new CDicomStreamWriter($handle);
    $this->pdv->setPresentationContexts($this->presentation_contexts);
    $this->pdv->encode($pdv_stream);
    
    $this->calculateLength();
    
    $stream_writer->writeUInt8($this->type);
    $stream_writer->skip(1);
    $stream_writer->writeUInt32($this->length);
    $stream_writer->write($pdv_stream->buf);
  }
  
  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function toString() {
    $str = "<h1>P-Data-TF</h1><br>
            <ul>
              <li>Type : " . sprintf("%02X", $this->type) . "</li>
              <li>Length : $this->length</li>
              <li>" . $this->pdv->__toString() . "</li>
            </ul>";
    echo $str;
  }
}
?>