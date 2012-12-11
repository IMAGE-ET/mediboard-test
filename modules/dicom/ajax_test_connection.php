<?php

CCanDo::checkRead();

$client = @stream_socket_client("tcp://192.168.1.32:7010", $errno, $errstr, 10);

$pdu = CDicomPDUFactory::encodePDU(0x01, array(
  "protocol_version" => 1,
  "called_AE_title" => "Mediboard",
  "calling_AE_title" => "Mediboard",
  "application_context" => array(
    "name" => "1.2.840.10008.3.1.1.1"
  ),
  "presentation_contexts" => array(
    array(
      "id" => 1,
      "abstract_syntax" => array(
        "name" => "1.2.840.10008.1.1"
      ),
      "transfer_syntaxes" => array(
        array(
          "name" => "1.2.840.10008.1.2"
        )
      )
    ),
  ),
  "user_info" => array(
    "sub_items" => array(
      "CDicomPDUItemMaximumLength" => array(
        "maximum_length" => 32768
      ),
      "CDicomPDUItemImplementationClassUID" => array(
        "uid" => "1.2.826.0.1.3680043.2.60.0.1"
      ),
      "CDicomPDUItemImplementationVersionName" => array(
        "version_name" => "mediboard"
      )
    )
  )
));

/*$data = $pdu->getPacket();
fwrite($client, $data, strlen($data));*/

$data1 = substr($pdu->getPacket(), 0, 50);
$data2 = substr($pdu->getPacket(), 50);

fwrite($client, $data1, strlen($data1));
sleep(2);
fwrite($client, $data2, strlen($data2));

