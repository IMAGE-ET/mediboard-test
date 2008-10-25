<?php /* $Id$ */

/**
 * @TODO REMPLACER PAR UN DO_MULTI_SEJOUR_AED CAR ON PERD LES LOGS
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $m;
$ds = CSQLDataSource::get("std");
 
$ajax  = mbGetValueFromPost("ajax", 0);
$m     = mbGetValueFromPost("m", 0);
$mode  = mbGetValueFromPost("mode", 0);
$value = mbGetValueFromPost("value", 1);
$id    = mbGetValueFromPost("id", 0);

$dateTime = mbDateTime();

switch ($mode) {
  case "allsaisie" : {
      $sql = "UPDATE sejour" .
        "\nSET sejour.saisi_SHS = '$value', sejour.modif_SHS = '0'" .
        "\nWHERE sejour.entree_prevue LIKE '$id __:__:__';";
    $result = $ds->exec($sql); $ds->error();
    $id = 0;
    break;
  }
}

if ($ajax) {
  $dbError = $ds->error();
  echo "<div class='message'>Action effectuée</div>";
  CApp::rip();
}

$AppUI->redirect("m=$m#adm$id");

?>