<?php /* $Id: do_edit_chambre.php,v 1.3 2006/04/24 07:57:46 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision: 1.3 $
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