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
        "name" => "1.2.840.10008.5.1.4.31"
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

echo "A-Associate-RQ :\n" . bin2hex($pdu->getPacket()) . "\n";

$data1 = substr($pdu->getPacket(), 0, 50);
$data2 = substr($pdu->getPacket(), 50);

fwrite($client, $data1, strlen($data1));
sleep(2);
fwrite($client, $data2, strlen($data2));

stream_set_timeout($client, 10);

$tmp = @fread($client, 2048);

echo "A-Associate-AC :\n" . bin2hex($tmp) . "\n";
$response = fopen("php://temp", "w");

$stream = new CDicomStreamWriter($response);

/** C-Find-RQ **/
$stream->writeUInt8(0x04);
$stream->skip(1);
$stream->writeUInt32(88);
$stream->writeUInt32(84);
$stream->writeUInt8(0x01);
$stream->writeUInt8(0x03);
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt32(4, "LE");
$stream->writeUInt32(70, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt16(0x0002, "LE");
$stream->writeUInt32(22, "LE");
$stream->writeString("1.2.840.10008.5.1.4.31", 22);
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt16(0x0100, "LE");
$stream->writeUInt32(2, "LE");
$stream->writeUInt16(0x0020, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt16(0x0110, "LE");
$stream->writeUInt32(2, "LE");
$stream->writeUInt16(1, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt16(0x0700, "LE");
$stream->writeUInt32(2, "LE");
$stream->writeUInt16(0, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt16(0x0800, "LE");
$stream->writeUInt32(2, "LE");
$stream->writeUInt16(0xfefe, "LE");

/** C-Find-Data **/
$stream->writeUInt8(0x04);
$stream->skip(1);
$stream->writeUInt32(112);
$stream->writeUInt32(108);
$stream->writeUInt8(0x01);
$stream->writeUInt8(0x02);

$stream->writeUInt16(0x0008, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt32(4, "LE");
$stream->writeUInt32(50, "LE");
$stream->writeUInt16(0x0008, "LE");
$stream->writeUInt16(0x0005, "LE");
$stream->writeUInt32(10, "LE");
$stream->writeString("ISO_IR 100", 10);
$stream->writeUInt16(0x0008, "LE");
$stream->writeUInt16(0x0020, "LE");
$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0008, "LE");
$stream->writeUInt16(0x0050, "LE");
$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0008, "LE");
$stream->writeUInt16(0x0090, "LE");
$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0008, "LE");
$stream->writeUInt16(0x1030, "LE");

$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0010, "LE");
$stream->writeUInt16(0x0000, "LE");
$stream->writeUInt32(4, "LE");
$stream->writeUInt32(32, "LE");
$stream->writeUInt16(0x0010, "LE");
$stream->writeUInt16(0x0020, "LE");
$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0010, "LE");
$stream->writeUInt16(0x0010, "LE");
$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0010, "LE");
$stream->writeUInt16(0x0030, "LE");
$stream->writeUInt32(0, "LE");
$stream->writeUInt16(0x0010, "LE");
$stream->writeUInt16(0x0040, "LE");
$stream->writeUInt32(0, "LE");

echo "C-Find-RQ :\n" . bin2hex($stream->buf) . "\n";

fwrite($client, $stream->buf, strlen($stream->buf));

$tmp = @fread($client, 2048);

echo "C-Find-RSP :\n" . bin2hex($tmp) . "\n";
