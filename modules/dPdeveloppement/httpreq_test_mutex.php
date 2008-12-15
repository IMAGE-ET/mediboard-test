<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision: $
 * @author Thomas Despoix
 */

global $can;
$can->needsRead();

$action = mbGetValueFromGet("action");
CAppUI::stepAjax("test_mutex-try", UI_MSG_OK, $action);

$mutex = new CMbSemaphore("test");

CAppUI::stepAjax("process $mutex->process", UI_MSG_OK);

switch ($action) {
  case "stall" :
  $mutex->acquire();
  sleep(5);
  $mutex->release();
  break;

  case "die" : 
  $mutex->acquire();
  sleep(5);
  CApp::rip();
  break;

  case "run" :
  $mutex->acquire();
  $mutex->release();
  break;

  default:
  CAppUI::stepAjax("test_mutex-fail", UI_MSG_WARNING, $action);
  return;
}

CAppUI::stepAjax("test_mutex-end", UI_MSG_OK, $action);

?>