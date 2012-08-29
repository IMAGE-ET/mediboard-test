<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

 /** USING STREAM SOCKETs **/
$socket = stream_socket_server("tcp://127.0.0.1:6104");

if ($socket) {
  $conn = stream_socket_accept($socket);
  
  $pdu = CDicomPDUFactory::decodePDU($conn);
  
  echo $pdu->__toString();
  
  fclose($conn);
    
  fclose($socket);
} else {
  echo "<p>stream_socket_server fail!</p><br>";
}

?>