<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $m;
$ds = CSQLDataSource::get("std");

$cmd = mbGetValueFromGet("cmd", "0");
$id  = mbGetValueFromGet("id",  "0");

$operation = new COperation();
$operation->load($id);
$plageop = $operation->plageop_id;
$rank    = $operation->rank;

switch($cmd)
{
  /*case "insert" : {
    /// Liste des op de la plageop, avec le rang le plus grand en premier
    $sql = "SELECT operations.rank AS rank, operations.time_operation AS time,
            operations.temp_operation as duree, plagesop.debut AS debut
            FROM operations, plagesop
            WHERE plagesop.plageop_id = '$plageop'
            AND operations.plageop_id = plagesop.plageop_id
            ORDER BY operations.rank DESC";
    $result = $ds->loadlist($sql);
    
    /// On insere l'operation avec le rang le plus grand + 1 (tout en bas)
    $sql = "UPDATE operations
            SET rank = '".($result[0]["rank"] + 1)."'
            WHERE operations.operation_id = '$id'";
    $exec = $ds->exec($sql);
    cleanOrderOp($plageop, "rank");
    break;
  }*/
  /*case "down" : {
    //On fait monter celui qui est en dessous
    $sql = "SELECT operation_id" .
    		"\nFROM operations" .
    		"\nWHERE operations.plageop_id = '$plageop'" .
    		"\nAND rank = '".($rank + 1)."'";
    $id_temp = $ds->loadlist($sql);
    $sql = "UPDATE operations
            SET rank = '$rank'
            WHERE operations.operation_id = '".$id_temp[0]["operation_id"]."'";
    $exec = $ds->exec($sql);
    //On fait descendre celui qu'on a choisit
    $sql = "UPDATE operations
            SET rank = '".($rank + 1)."'
            WHERE operations.operation_id = '$id'";
    $exec = $ds->exec($sql);
    cleanOrderOp($plageop, "rank");
    break;
  }*/
  /*case "up" : {
    //On fait descendre celui qui est au dessus
    $sql = "SELECT operation_id" .
    		"\nFROM operations" .
    		"\nWHERE operations.plageop_id = '$plageop'" .
    		"\nAND rank = '".($rank - 1)."'";
    $id_temp = $ds->loadlist($sql);
    $sql = "UPDATE operations
            SET rank = '$rank'
            WHERE  operations.operation_id = '".$id_temp[0]["operation_id"]."'";
    $exec = $ds->exec($sql);
    //On fait monter celui qu'on a choisit
    $sql = "UPDATE operations
            SET rank = '".($rank - 1)."'
            WHERE operations.operation_id = '$id'";
    $exec = $ds->exec($sql);
    cleanOrderOp($plageop, "rank");
    break;
  }*/
  /*case "rm" : {
  // l'operation n'occupe plus du tout la plage op : temps nul
  // la vraie suppression supprime l'op et reordonne...
  	$sql = "UPDATE operations
			SET time_operation = '00:00:00', pause = '00:00:00',rank = 0
			WHERE operations.operation_id = '$id'";
    $result = $ds->exec($sql);
    cleanOrderOp($plageop, "time");
    changeAffect($id, "rm");
    break;
  }*/
 /* case "sethour" : {
  // on change la pause
    $hour = dPgetParam( $_GET, "hour", "00" );
    $min = dPgetParam( $_GET, "min", "00" );
    $sql = "UPDATE operations
			SET pause = '".$hour.":".$min.":00'
			WHERE operations.operation_id = '$id'";
    $result = $ds->exec($sql);
    cleanOrderOp($plageop, "time");
    break;
  }*/
  /*case "setanesth" : {
    $type = dPgetParam( $_GET, "type", null);
    $sql = "UPDATE operations
            SET type_anesth = '$type'
            WHERE operations.operation_id = '$id'";
    $result = $ds->exec($sql);
    break;
  }*/
}

//Réarrangement de l'ordre des interventions
function cleanOrderOp($plageop, $type = "rank") {
  $ds = CSQLDataSource::get("std");
  switch($type) {
    case "time" :
      $sql = "SELECT operations.operation_id," .
          "\noperations.rank," .
          "\noperations.temp_operation," .
          "\nplagesop.debut" .
          "\nFROM operations" .
          "\nLEFT JOIN plagesop" .
          "\nON plagesop.plageop_id = operations.plageop_id" .
          "\nWHERE operations.plageop_id = '$plageop'" .
          "\nAND operations.rank != 0" .
          "\nORDER BY operations.time_operation ASC";
      $result = $ds->loadlist($sql);
      $i = 1;
      foreach($result as $key => $value) {
        $curr_id = $value["operation_id"];
        $sql = "UPDATE operations" .
            "\nSET operations.rank = '$i'" .
            "\nWHERE operations.operation_id = '$curr_id'";
        $ds->exec($sql);
        $i++;
      }
    case "rank" :
      /*$sql = "SELECT operations.operation_id," .
          "\noperations.rank," .
          "\noperations.temp_operation," .
          "\noperations.pause," .
          "\nplagesop.debut," .
          "\nplagesop.temps_inter_op" .
          "\nFROM operations" .
          "\nLEFT JOIN plagesop" .
          "\nON plagesop.plageop_id = operations.plageop_id" .
          "\nWHERE operations.plageop_id = '$plageop'" .
          "\nAND operations.rank != '0'" .
          "\nORDER BY operations.rank ASC";
      $result = $ds->loadlist($sql);
      if(count($result)) {
        $debut = $result[0]["debut"];
        foreach($result as $key => $value) {
          $curr_id = $value["operation_id"];
          $sql = "UPDATE operations" .
              "\nSET operations.time_operation = '$debut'" .
              "\nWHERE operations.operation_id = '$curr_id'";
          $ds->exec($sql);
          changeAffect($curr_id);
          $debut = mbAddTime($value["temp_operation"], $debut); // durée de l'opération
          $debut = mbAddTime($value["temps_inter_op"], $debut); // pause d'1/4h
          $debut = mbAddTime($value["pause"], $debut);          // Pause
        }
      }*/
      break;
  }
}

// Modification de l'heure de sortie de la dernière affectation
function changeAffect($id, $cmd = null) {
  $operation = new COperation;
  $operation->load($id);
  $operation->loadRefs();
  $operation->_ref_sejour->loadRefsAffectations();
  $affectation =& $operation->_ref_sejour->_ref_last_affectation;
  if ($affectation->affectation_id && ($operation->_ref_sejour->type == "ambu")) {
    if($cmd == "rm") {
      $affectation->sortie = mbDate("", $affectation->sortie)." 18:00:00";
    } else {
      $affectation->sortie = mbDate("", $affectation->sortie)." ";
      if($operation->time_operation < "18:00:00")
        $affectation->sortie .= mbTime("+ 6 hours", $operation->time_operation);
      else
        $affectation->sortie .= "23:59:00";
    }
    $affectation->store();
  }
}

$AppUI->redirect("m=$m#op$id");
?>