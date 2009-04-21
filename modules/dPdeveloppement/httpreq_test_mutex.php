<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Thomas Despoix
 */

global $can;
$can->needsRead();

$action = mbGetValueFromGet("action");
CAppUI::stepAjax("test_mutex-try", UI_MSG_OK, $action);

$mutex = new CMbSemaphore("test");

CAppUI::stepAjax("test_mutex-process", UI_MSG_OK, $mutex->process);

switch ($action) {
  case "stall" :
  CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire());
  sleep(5);
  $mutex->release();
  break;

  case "die" : 
  CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire());
  sleep(5);
  CApp::rip();
  break;

  case "run" :
  CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire());
  $mutex->release();
  break;

  case "dummy" :
  break;
  
  default:
  CAppUI::stepAjax("test_mutex-fail", UI_MSG_WARNING, $action);
  return;
}

CAppUI::stepAjax("test_mutex-end", UI_MSG_OK, $action);

?>