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
class CDicomPDUPDataTF extends CDIcomPDU {
 
  /**
   * The presentation data value
   * 
   * @var CDicomPDV
   */
  protected $pdv = array();
  
  /**
   * The transfer syntax used in this PDV, represented as the UID of the corresponding tranfer syntax
   * 
   * @var string 
   */
  protected $transfer_syntax = null;
 
  /**
   * The constructor.
   * 
   * @param array $datas Default null. 
   * You can set all the field of the class by passing an array, the keys must be the name of the fields.
   */
  function __construct(array $datas = array()) {
    $this->setType("04");
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
   * @param array $pd_datas The pdv datas
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
    $this->pdv = new CDicomPDV(array("transfer_syntax" => $this->transfer_syntax));
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
    $this->calculateLength();
    
    $stream_writer->writeHexByte($this->type, 2);
    $stream_writer->skip(1);
    $stream_writer->writeUInt32($this->length);
    $this->pdv->encode($stream_writer);
  }
  
  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    $str = "<h1>P-Data-TF</h1><br>
            <ul>
              <li>Type : $this->type</li>
              <li>Length : $this->length</li>
              <li>" . $this->pdv->__toString() . "</li>
            </ul>";
    return $str;
  }
}
?>