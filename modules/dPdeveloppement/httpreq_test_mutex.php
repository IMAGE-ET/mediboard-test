<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Thomas Despoix
 */

CCanDo::checkRead();

$action = CValue::get("action");

$duration = 10;

// Remove session lock
session_write_close();

CAppUI::stepAjax("test_mutex-try", UI_MSG_OK, $action);

$mutex = new CMbMutex("test");

switch ($action) {
  case "stall" :
    CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire($duration));
    sleep(5);
    $mutex->release();
    break;

  case "die" : 
    CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire($duration));
    sleep(5);
    CApp::rip();
    break;

  case "run" :
    CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire($duration));
    $mutex->release();
    break;

  case "dummy" :
    break;
  
  default:
    CAppUI::stepAjax("test_mutex-fail", UI_MSG_WARNING, $action);
    return;
}
