<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage 
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represent a Dicom data set
 */
class CDicomDataSet {
  
  /**
   * The group number
   * 
   * @var integer
   */
  protected $group_number = null;
  
  /**
   * The element number
   * 
   * @var integer
   */
  protected $element_number = null;
  
  /**
   * The name of the element
   * 
   * @var string
   */
  protected $name = null;
  
  /**
   * The value representation of the element
   * 
   * @var string
   */
  protected $vr = null;
  
  /**
   * The value multiplicity
   * 
   * @var string
   */
  protected $vm = null;
  
  /** 
   * The length
   * 
   * @var integer
   */
  protected $length = null;
  
  /**
   * The value
   * 
   * @var mixed
   */
  protected $value = null;
  
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
    
    if ($group != null && $element != null) {
      $this->setDataSet();
    }
  }
  
  /**
   * Set the group number
   * 
   * @param integer $group The group number
   * 
   * @return null
   */
  public function setGroupNumber($group) {
    $this->group_number = $group;
  }
  
  /**
   * Get the group number
   * 
   * @return integer
   */
  public function getGroupNumber() {
    return $this->group_number;
  }
  
  /**
   * Set the element number
   * 
   * @param integer $element The element number
   * 
   * @return null
   */
  public function setElementNumber($element) {
    $this->element_number = $element;
  }
  
  /**
   * Get the element number
   * 
   * @return integer
   */
  public function getElementNumber() {
    return $this->element_number;
  }
  
  /**
   * Set the name
   * 
   * @param string $name The name
   * 
   * @return null
   */
  public function setName($name) {
    $this->name = $name;
  }
  
  /**
   * Get the name
   * 
   * @return string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * Set the value representation
   * 
   * @param string $vr The value representation
   * 
   * @return null
   */
  public function setVr($vr) {
    $this->vr = $vr;
  }
  
  /**
   * Get the value representation
   * 
   * @return string
   */
  public function getVr() {
    return $this->vr;
  }
  
  /**
   * Set the value multiplicity
   * 
   * @param string $vm The value multiplicity
   * 
   * @return null
   */
  public function setVm($vm) {
    $this->vm = $vm;
  }
  
  /**
   * Get the value multiplicity
   * 
   * @return string
   */
  public function getVm() {
    return $this->vm;
  }
  
  /**
   * Set the value
   * 
   * @param mixed $value The value
   * 
   * @return null
   */
  public function setValue($value) {
    $this->value = $value;
  }
  
  /**
   * Get the value
   * 
   * @return mixed
   */
  public function getValue() {
    return $this->value;
  }
  
  /**
   * Get the data set definition from the DICOM dictionary,
   * and set the vr, the vm and the name.
   * 
   * @return null
   */
  public function setDataSet() {
    $dataset = CDicomDictionary::getDataSet($this->group_number, $this->element_number);
    $this->vr = $dataset[0];
    $this->vm = $dataset[1];
    $this->name = $dataset[2];
  }
  
  /**
   * Calculate the length of the value
   * 
   * @return null
   */
  protected function calculateLength() {
    $vr_def = CDicomDictionary::getValueRepresentation($this->vr);
    if ($vr_def['Fixed'] == 1) {
      $this->length = $vr_def['Length'];
    }
    else {
      $this->length = strlen($this->value);
    }
  }
  
  /**
   * Encode the dataset, depending on the transfer syntax
   * 
   * @param CDicomStreamWriter $stream_writer		The stream writer
   * 
   * @param string						 $transfer_syntax	The UID of the transfer syntax
   * 
   * @return null
   */
  public function encode(CDicomStreamWriter $stream_writer, $transfer_syntax = "1.2.840.10008.1.2") {
    $vr_encoding = "";
    $endianness = "";
    switch ($transfer_syntax) {
      case "1.2.840.10008.1.2" :
        $vr_encoding = "Implicit";
        $endiannes = "LE";
        break;
      case "1.2.840.10008.1.2.1" :
        $vr_encoding = "Explicit";
        $endiannes = "LE";
        break;
      case "1.2.840.10008.1.2.2" :
        $vr_encoding = "Explicit";
        $endiannes = "BE";
        break;
      default :
        
        break;
    }
    $this->calculateLength();
    
    $method = "encode$vr_encoding";
    $this->$method($stream_writer, $endianness);
  }
  
  /**
   * Encode the data set with the implicit VR
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @param string						 $endianness		The endianness, must be equal to "BE" (Big Endian) or "LE" (Little Endian)
   * 
   * @return null
   */
  protected function encodeImplicit(CDicomStreamWriter $stream_writer, $endianness) {
    $stream_writer->writeHexByte($this->group_number, 2, $endiannes);
    $stream_writer->writeHexByte($this->element_number, 2, $endiannes);
    $stream_writer->writeUInt32($this->length, $endianness);
    $this->encodeValue($stream_writer, $endianness);
  }
  
  /**
   * Encode the data set with the explicit VR
   * 
   * @param CDicomStreamWriter $stream_writer	The stream writer
   * 
   * @param string						 $endianness		The endianness, must be equal to "BE" (Big Endian) or "LE" (Little Endian)
   * 
   * @return null
   */
  protected function encodeExplicit(CDicomStreamWriter $stream_writer, $endianness) {
    $stream_writer->writeHexByte($this->group_number, 2, $endiannes);
    $stream_writer->writeHexByte($this->element_number, 2, $endiannes);
    $stream_writer->writeString($this->vr, 2);
    if (in_array($this->vr, array("OB", "OW", "OF", "SQ", "UT", "UN"))) {
      $stream_writer->skip(2);
      $stream_writer->writeUInt32($this->length, $endianness);
    }
    else {
      $stream_writer->writeUInt16($this->length, $endianness);
    }
    $this->encodeValue($stream_writer, $endianness);
  }
  
  /**
   * Encode the value
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   * 
   * @param string						 $endianness		The endianness, must be equal to "BE" (Big Endian) or "LE" (Little Endian)
   * 
   * @return null
   * 
   * @todo traiter les cas FL, FD, OB, OW, OF, SQ
   */
  protected function encodeValue(CDicomStreamWriter $stream_writer, $endianness) {
    switch ($this->vr) {
      case 'AE' :
      case 'AS' :
      case 'CS' :
      case 'DA' :
      case 'DS' :
      case 'DT' :
      case 'FL' :
      case 'FD' :
      case 'IS' :
      case 'LO' :
      case 'LT' :
      case 'OB' :
      case 'OF' :
      case 'OX' :
      case 'OW' :
      case 'PN' :
      case 'SH' :
      case 'SQ' :
      case 'ST' :
      case 'TM' :
      case 'UN' :
      case 'UT' :
        $stream_writer->writeString($this->value, $this->length);
        break;
      case 'AT' :
        $stream_writer->writeHexByte($this->value[0], 2, $endianness);
        $stream_writer->writeHexByte($this->value[1], 2, $endianness);
        break;
      case 'SL' :
        $stream_writer->writeInt32($this->value, $endianness);
        break;
      case 'SS' :
        $stream_writer->writeInt16($this->value, $endianness);
        break;
      case 'UI' :
        $stream_writer->writeUID($this->value, $this->length);
        break;
      case 'UL' :
        $stream_writer->writeUInt32($this->value, $endianness);
        break;
      case 'US' :
        $stream_writer->writeUInt16($this->value, $endianness);
        break;
      default :
        
        break;
    }
  }
  
  /**
   * Decode the dataset, depending on the transfer syntax
   * 
   * @param CDicomStreamReader $stream_reader		The stream reader
   * 
   * @param string						 $transfer_syntax	The UID of the transfer syntax
   * 
   * @return null
   */
  public function encode(CDicomStreamReader $stream_reader, $transfer_syntax = "1.2.840.10008.1.2") {
    $vr_encoding = "";
    $endianness = "";
    switch ($transfer_syntax) {
      case "1.2.840.10008.1.2" :
        $vr_encoding = "Implicit";
        $endiannes = "LE";
        break;
      case "1.2.840.10008.1.2.1" :
        $vr_encoding = "Explicit";
        $endiannes = "LE";
        break;
      case "1.2.840.10008.1.2.2" :
        $vr_encoding = "Explicit";
        $endiannes = "BE";
        break;
      default :
        
        break;
    }
    
    $method = "decode$vr_encoding";
    $this->$method($stream_reader, $endianness);
  }
  
  /**
   * Decode the data set with the implicit VR
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @param string						 $endianness		The endianness, must be equal to "BE" (Big Endian) or "LE" (Little Endian)
   * 
   * @return null
   */
  protected function decodeImplicit(CDicomStreamReader $stream_reader, $endianness) {
    $this->group_number = $stream_reader->readHexByte(2, $endiannes);
    $this->element_number = $stream_reader->readHexByte(2, $endiannes);
    $this->length = $stream_reader->readUInt32($endianness);
    $this->setDataSet();
    $this->decodeValue($stream_reader, $endianness);
  }
  
  /**
   * Decode the data set with the explicit VR
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @param string						 $endianness		The endianness, must be equal to "BE" (Big Endian) or "LE" (Little Endian)
   * 
   * @return null
   */
  protected function decodeExplicit(CDicomStreamReader $stream_reader, $endianness) {
    $this->group_number = $stream_reader->readHexByte(2, $endiannes);
    $this->element_number = $stream_reader->readHexByte(2, $endiannes);
    $this->vr = $stream_reader->readString(2);
    if (in_array($this->vr, array("OB", "OW", "OF", "SQ", "UT", "UN"))) {
      $stream_reader->skip(2);
      $stream_reader->readUInt32($this->length, $endianness);
    }
    else {
      $stream_reader->readUInt16($this->length, $endianness);
    }
    $this->decodeValue($stream_reader, $endianness);
  }
  
  /**
   * Decode the value
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @param string						 $endianness		The endianness, must be equal to "BE" (Big Endian) or "LE" (Little Endian)
   * 
   * @return null
   * 
   * @todo traiter les cas FL, FD, OB, OW, OF, SQ
   */
  protected function decodeValue(CDicomStreamReader $stream_reader, $endianness) {
    switch ($this->vr) {
      case 'AE' :
      case 'AS' :
      case 'CS' :
      case 'DA' :
      case 'DS' :
      case 'DT' :
      case 'FL' :
      case 'FD' :
      case 'IS' :
      case 'LO' :
      case 'LT' :
      case 'OB' :
      case 'OF' :
      case 'OX' :
      case 'OW' :
      case 'PN' :
      case 'SH' :
      case 'SQ' :
      case 'ST' :
      case 'TM' :
      case 'UN' :
      case 'UT' :
        $this->value = $stream_reader->readString($this->length);
        break;
      case 'AT' :
        $this->value = array();
        $this->value[] = $stream_reader->readHexByte(2, $endianness);
        $this->value[] = $stream_reader->readHexByte(2, $endianness);
        break;
      case 'SL' :
        $this->value = $stream_reader->readInt32($endianness);
        break;
      case 'SS' :
        $this->value = $stream_reader->readInt16($endianness);
        break;
      case 'UI' :
        $this->value = $stream_reader->readUID($this->length);
        break;
      case 'UL' :
        $this->value = $stream_reader->readUInt32($endianness);
        break;
      case 'US' :
        $this->value = $stream_reader->readUInt16($endianness);
        break;
      default :
        
        break;
    }
  }

  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    return "";
  }
}
?>