<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Thomas Despoix
 */

CCanDo::checkRead();

$sleep = 5;

$i        = CValue::get("i");
$duration = CValue::get("duration", 10);

$colors = array(
  "#f00",
  "#0f0",
  "#09f",
  "#ff0",
  "#f0f",
  "#0ff",
);

// Remove session lock
session_write_close();

$mutex = new CMbMutex("test", $colors[$i]);
$time = $mutex->acquire($duration);

sleep($sleep);

$mutex->release();

$data = array(
  "driver" => get_class($mutex->getDriver()),
  "i"      => $i,
  "time"   => $time,
);

echo json_encode($data);

CApp::rip();
