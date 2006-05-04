<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$ajax  = mbGetValueFromPost("ajax", 0);
$m     = mbGetValueFromPost("otherm", mbGetValueFromPost("m", ""));
$value = mbGetValueFromPost("value", "o");
$id    = mbGetValueFromPost("id", 0);

if($id) {
  $sql = "UPDATE operations
          SET chambre = '$value'
          WHERE operation_id = '$id'";
  $result = db_exec($sql);
  db_error();
}

if($ajax) {
  $AppUI->getMsg();
  exit(0);
}

$AppUI->redirect("m=$m#adm$id");

?>