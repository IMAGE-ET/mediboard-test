<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

$addr = CValue::post("client_addr");
$port = CValue::post("client_port");
$message = base64_decode(CValue::post("message"));

$session = getSession($addr, $port);

if ($session) {
  $session->updateFormFields();
  $session->loadRefActor();
  $event = "";
  $datas = "";
  if ($message == "TCP_Open" || $message == "TCP_Closed") {
    $event = $message;
    $datas = null;
    CApp::rip();
  }
  else {
    $datas = $message;
    $type = unpack("H*", substr($message, 0, 1));
    $event = getEventName($type[1]);
  }
  $ack = $session->handleEvent($event, $datas);
  $session->store();
  
  if ($ack) {
    echo base64_encode($ack);
  }
  else {
    echo " ";
  }
}
CApp::rip();

/**
 * Return the session corresponding to the ip adress of the sender
 * 
 * @param string  $addr The ip adress
 * 
 * @param integer $port The port
 * 
 * @return CDicomSession
 */
function getSession($addr, $port) {
  $dicom_sender = new CDicomSender();
  $dicom_senders = $dicom_sender->loadMatchingList();
  $dicom_sender = null;
  foreach ($dicom_senders as $_sender) {
    $_sender->loadRefsExchangesSources();
    if ($_sender->_ref_exchanges_sources[0]->host == $addr /*&& $_sender->_ref_exchanges_sources[0]->port == $port*/) {
      $dicom_sender = $_sender;
      break;
    }
  }
  
  if (!$dicom_sender->_id) {
    return false;
  }
  
  $session = new CDicomSession();
  $where = array();
  $where["sender_id"] = " = $dicom_sender->_id";
  $where["status"] = " IS NULL";
  $where["state"] = " != 'Sta13'";
  $where["end_date"] = " IS NULL";
  $session->loadObject($where);
  
  if (!$session->_id) {
    $session = new CDicomSession($dicom_sender);
  }
  return $session;
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
?>