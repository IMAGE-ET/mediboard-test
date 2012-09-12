<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * Represents a Role Selection PDU Item
 */
class CDicomPDUItemRoleSelection extends CDicomPDUItem {
  
  /**
   * The uid length
   * 
   * @var integer
   */
  var $uid_length = null;
  
  /**
   * The SOP class uid
   * 
   * @var string
   */
   var $sop_class_uid = null;
   
   /**
    * The SCU role
    * Set to 1 if the sender can support the SCU role, to 0 if not
    * 
    * @var integer
    */
  var $scu_role = null;
  
  /**
    * The SCP role
    * Set to 1 if the sender can support the SCP role, to 0 if not
    * 
    * @var integer
    */
  var $scp_role = null;
  
  /**
   * The constructor.
   * 
   * @param array $datas The datas, default null. 
   */
  function __construct($datas = array()) {
    $this->setType("54");
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
   * Set the uid length
   * 
   * @param integer $uid_length The uid length
   * 
   * @return null
   */
  function setUidLength($uid_length) {
    $this->uid_length = $uid_length;
  }
  
  /**
   * Set the SOP class UID
   * 
   * @param string $uid SOP class UID
   * 
   * @return null
   */
  function setSOPClassUid($uid) {
    $this->sop_class_uid = $uid;
  }
  
  /**
   * Set the SCU role
   * 
   * @param integer $scu_role The SCu role
   * 
   * @return null
   */
  function setScuRole($scu_role) {
    $this->scu_role = $scu_role;
  }
  
  /**
   * Set the SCP role
   * 
   * @param integer $scp_role The SCP role
   * 
   * @return null
   */
  function setScpRole($scp_role) {
    $this->scp_role = $scp_role;
  }
  
  /**
   * Return the values of the fields
   * 
   * @return integer
   */
  function getValues() {
    return array(
      "uid_length" 		=> $this->uid_length,
      "sop_class_uid" => $this->sop_class_uid,
      "scu_role" 			=> $this->scu_role,
      "scp_role" 			=> $this->scp_role
    );
  }
  
  /**
   * Decode the Implementation Version Name
   * 
   * @param CDicomStreamReader $stream_reader The stream reader
   * 
   * @return null
   */
  function decodeItem(CDicomStreamReader $stream_reader) {
    $this->uid_length = $stream_reader->readUnsignedInt16();
    $this->sop_class_uid = $stream_reader->readUID($this->uid_length);
    $this->scu_role = $stream_reader->readUnsignedInt8();
    $this->scp_role = $stream_reader->readUnsignedInt8();
  }
  
  /**
   * Encode the Implementation Version Name
   * 
   * @param CDicomStreamWriter $stream_writer The stream writer
   *  
   * @return null
   */
  function encodeItem(CDicomStreamWriter $stream_writer) {
    $this->calculateLength();
    
    $stream_writer->writeHexByte($this->type, 2);
    $stream_writer->skip(1);
    $stream_writer->writeUnsignedInt16($this->length);
    $stream_writer->writeUnsignedInt16($this->uid_length);
    $stream_writer->writeUID($this->sop_class_uid, $this->uid_length);
    $stream_writer->writeUnsignedInt8($this->scu_role);
    $stream_writer->writeUnsignedInt8($this->scp_role);
  }

  /**
   * Calculate the length of the item (without the type and the length fields)
   * 
   * @return null
   */
  function calculateLength() {
    $this->uid_length = strlen($this->sop_class_uid);
    $this->length = 4 + $this->uid_length;
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
    return $this->length + 4;
  }

  /**
   * Return a string representation of the class
   * 
   * @return string
   */
  function __toString() {
    return "Role selection :
            <ul>
              <li>Item type : $this->type</li>
              <li>Item length : $this->length</li>
              <li>UID length : $this->uid_length</li>
              <li>SOP class UID : $this->sop_class_uid</li>
              <li>SCU role : $this->scu_role</li>
              <li>SCP role : $this->scp_role</li>
            </ul>";
  }
}
?>