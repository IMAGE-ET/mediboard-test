<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * The C-Find-Data message
 * 
 * @see DICOM Standard PS 3.07, section 9.1.2 ans 9.3.2
 */
class CDicomMessageCFindData {
  
  /**
   * The list of the differents attributes
   * 
   * The keys are the group number, the values an array (key element number)
   * 
   * @var array
   */
  protected $attributes = null;
  
  /**
   * An array which contains the datasets
   * 
   * @var arroy-of-CDicomDataSet
   */
  protected $datasets = array();
  
  /**
   * The constructor
   * 
   * @param array $attributes The attributes
   */
  function __construct(array $attributes = null) {
    if ($attributes) {
      $this->setAttributes($attributes);
    }
  }
  
  /**
   * Return the list of attributes
   * 
   * @return array
   */
  function getAttributes() {
    return $this->attributes;
  }
  
  /**
   * Set the list of attributes
   * 
   * @param array $attributes The list
   * 
   * @return null
   */
  function setAttributes(array $attributes) {
    $this->attributes = $attributes;
  }
  
  /**
   * Return the datasets
   * 
   * @return array
   */
  function getDatasets() {
    return $this->datasets;
  }
  
  /**
   * Encode the datas, depending on the transfer syntax
   * 
   * @param CDicomStreamWriter $stream_writer   The stream writer
   * 
   * @param string             $transfer_syntax The UID of the transfer syntax
   * 
   * @return null
   */
  function encode(CDicomStreamWriter $stream_writer, $transfer_syntax) {
    foreach ($this->attributes as $group_number => $group) {
      $this->datasets[$group_number] = array();
      
      $handle = fopen("php://temp", "w+");
      $group_writer = new CDicomStreamWriter($handle);
      
      foreach ($group as $element_number => $value) {
        $dataset = new CDicomDataSet(array("group_number" => $group_number, "element_number" => $element_number, "value" => $value));
        $dataset->encode($group_writer, $transfer_syntax);
        
        $this->datasets[$group_number][] = $dataset;
      }
      
      $length = strlen($group_writer->buf);
      $group_length_dataset = new CDicomDataSet(array("group_number" => $group_number, "element_number" => 0x0000, "value" => $length));
      
      $group_length_dataset->encode($stream_writer, $transfer_syntax);
      $this->datasets[$group_number][] = $group_length_dataset;
      
      $stream_writer->write($group_writer->buf);
    
      $group_writer->close();
    }
  }
  
  /**
   * Decode the datas, depending on the transfer syntax
   * 
   * @param CDicomStreamReader $stream_reader   The stream writer
   * 
   * @param integer            $stream_length   The length of the stream
   * 
   * @param string             $transfer_syntax The UID of the transfer syntax
   * 
   * @return null
   */
  function decode(CDicomStreamReader $stream_reader, $stream_length, $transfer_syntax) {
    $this->attributes = array();
    $this->datasets = array();
    while ($stream_reader->getPos() < $stream_length) {
      $dataset = new CDicomDataSet();
      $dataset->decode($stream_reader, $transfer_syntax);
      
      $group = $dataset->getGroupNumber();
      $element = $dataset->getElementNumber();
      if (!array_key_exists($group, $this->datasets)) {
        $this->datasets[$group] = array();
      }
      if (!array_key_exists($group, $this->attributes)) {
        $this->attributes[$group] = array();
      }
      
      $this->datasets[$group][] = $dataset;
      $this->attributes[$group][$element] = $dataset->getValue();
    }
  }
}
?>
  