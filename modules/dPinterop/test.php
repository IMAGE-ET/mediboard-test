<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$function = mbGetValueFromGet("action" , "release");
$timeout  = mbGetValueFromGet("timeout", 5);

$sem1 = new CMbSemaphore("montest");
$sem2 = new CMbSemaphore("montest");
if($function == "acquire") {
  if($sem1->acquire($timeout)) {
    mbTrace("Aquire du sémaphore 1 réussi");
  } else {
    mbTrace("Aquire sémaphore 1 échoué");
  }
  if($sem2->acquire($timeout)) {
    mbTrace("Aquire sémaphore 2 réussi");
  } else {
    mbTrace("Aquire sémaphore 2 échoué");
  }
} else {
  if($sem1->release()) {
    mbTrace("Release sémaphore 1 réussi");
  } else {
    mbTrace("release sémaphore 1 échoué");
  }
  if($sem2->release()) {
    mbTrace("Release sémaphore 2 réussi");
  } else {
    mbTrace("release sémaphore 2 échoué");
  }
}


?>