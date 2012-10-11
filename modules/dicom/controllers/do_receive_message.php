<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

$client_addr = CValue::post("client_addr");
$client_port = CValue::post("client_port");
$message = CValue::post("message");

$dicom_handler = CDicomSessionsHandler::getInstance();
$ack = $dicom_handler->handleMessage($client_addr, $client_port, $message);

echo $ack;

CApp::rip();
?>