<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @todo REMPLACER PAR UN DO_MULTI_SEJOUR_AED CAR ON PERD LES LOGS
 */

global $AppUI, $m;
$ds = CSQLDataSource::get("std");
 
$ajax  = CValue::post("ajax", 0);
$m     = CValue::post("m", 0);
$mode  = CValue::post("mode", 0);
$value = CValue::post("value", 1);
$id    = CValue::post("id", 0);

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