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
$value = mbGetValueFromPost("value", 1);
$id    = mbGetValueFromPost("id", 0);

$dateTime = mbDateTime();

switch ($mode) {
  case "admis" : {
    if ($id) {
      if($value == "o") {
        $sql = "UPDATE sejour SET" .
          "\n`entree_reelle` = '$dateTime'" .
          "\nWHERE sejour_id = '$id';";
      } else {
        $sql = "UPDATE sejour SET" .
          "\n`entree_reelle` = NULL" .
          "\nWHERE sejour_id = '$id';";
      }
      $result = db_exec($sql);
      db_error();
    }
    break;
  }
  case "saisie" : {
    if($id) {
      $sql = "UPDATE sejour" .
        "\nSET sejour.saisi_SHS = '$value', sejour.modif_SHS = '0'" .
        "\nWHERE sejour.sejour_id = '$id';";
      $result = db_exec($sql); db_error();
    }
    break;
  }
  case "allsaisie" : {
      $sql = "UPDATE sejour" .
        "\nSET sejour.saisi_SHS = '$value', sejour.modif_SHS = '0'" .
        "\nWHERE sejour.entree_prevue LIKE '$id __:__:__';";
    $result = db_exec($sql); db_error();
    $id = 0;
    break;
  }
}

if($ajax) {
  $dbError = db_error();
  echo "<div class='message'>Action effectuée</div>";
  exit(0);
}

$AppUI->redirect("m=$m#adm$id");

?>