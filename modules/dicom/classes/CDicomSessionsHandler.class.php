<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

class CDicomSessionsHandler {
  
  /**
   * The Dicom sessions
   * 
   * @var array-of-CDicomSession
   */
  protected $sessions = array();
  
  
  /**
   * The static instance of the SessionsHandler
   * 
   * @var CDicomSessionsHandler
   */
  protected static $instance = null;
  
  /**
   * Return the static instance
   * 
   * @return CDicomSessionsHandler
   */
  static function getInstance() {
    if (!self::$instance) {
      self::$instance = new CDicomSessionsHandler();
    }
    return self::$instance;
  }
  
  /**
   * Return the session corresponding to the ip adress of the sender
   * 
   * @param string  $addr The ip adress
   * 
   * @param integer $port The port
   * 
   * @return CDicomSession
   */
  protected function getSession($addr, $port) {
    if (!array_key_exists($addr, $this->sessions)) {
      $this->createSession($addr, $port);
    }
    return $this->sessions[$addr];
  }
  
  /**
   * Create a new session
   * 
   * @param string  $addr The ip adress
   * 
   * @param integer $port The port
   * 
   * @return null
   */
  protected function createSession($addr, $port) {
    $dicom_sender = new CDicomSender();
    $dicom_senders = $dicom_sender->loadMatchingList();
    
    $dicom_sender = null;
    foreach ($dicom_senders as $_sender) {
      $_sender->loadRefExchangeSource();
      
      if ($_sender->_ref_exchange_source->host == $addr && $_sender->_ref_exchange_source->port == $port) {
        $dicom_sender = $_sender;
        break;
      }
    }
    
    if ($dicom_sender) {
      $_session = new CDicomSession($dicom_sender);
      $this->sessions[$addr] = $_session;
    }
    else {
      /**
       * @todo send exception
       */
    }
  }
  
  /**
   * Return the event name, depends on the PDU type
   * 
   * @param string $type The PDU type
   * 
   * @return string
   */
  function getEventName($type) {
    $event = "";
    switch($type) {
      case "01" :
        $event = "AAssociateRQ_Received";
        break;
      case "02" :
        $event = "AAssociateAC_Received";
        break;
      case "03" :
        $event = "AAssociateRJ_Received";
        break;
      case "04" :
        $event = "PDataTF_Received";
        break;
      case "05" :
        $event = "AReleaseRQ_Received";
        break;
      case "06" :
        $event = "AReleaseRP_Received";
        break;
      case "07" :
        $event = "AAbort_Received";
        break;
      default :
        $event = "InvalidPDU";
        break; 
    }
    return $event;
  }
  
  /**
   * Handle a message
   * 
   * @param string  $addr  The IP adress of the sender
   * 
   * @param integer $port    The port used by the sender
   * 
   * @param string  $message The message
   */
  function handleMessage($addr, $port, $message) {
    $session = $this->getSession($addr, $port);
    $event = "";
    $datas = "";
    if ($message == "TCP_Open" || $message == "TCP_Closed") {
      $event = $message;
      $datas = null;
    }
    else {
      $datas = $message;
      $event = $this->getEventName(unpack("H*", substr($message, 0, 2)));
    }
    
    return $session->handleEvent($event, $datas);
  }
}
?>