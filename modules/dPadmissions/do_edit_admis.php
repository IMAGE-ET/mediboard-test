<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$ajax  = mbGetValueFromPost("ajax", 0);
$m     = mbGetValueFromPost("m", 0);
$mode  = mbGetValueFromPost("mode", 0);
$value = mbGetValueFromPost("value", 'o');
$id    = mbGetValueFromPost("id", 0);

switch($mode) {
  case 'admis' : {
    if($id) {
      $sql = "UPDATE operations
              SET admis = '$value'
              WHERE operation_id = '$id'";
      $result = db_exec($sql);
      db_error();
    }
    break;
  }
  case 'saisie' : {
    if($id) {
      $sql = "UPDATE operations
              SET saisie = '$value', modifiee = '0'
              WHERE operation_id = '$id'";
      $result = db_exec($sql);
      db_error();
    }
    break;
  }
  case 'allsaisie' : {
    $sql = "UPDATE operations" .
    		"\nSET saisie = '$value', modifiee = '0'" .
    		"\nWHERE date_adm = '$id'";
    $result = db_exec($sql);
    db_error();
    $id = 0;
    break;
  }
}

if($ajax) {
  echo '<div class="message">Action effectuée</div>';
  exit(0);
}

$AppUI->redirect("m=$m#adm$id");

?>