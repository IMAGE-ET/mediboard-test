<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass('dPplanningOp', 'planning'));
require_once($AppUI->getModuleClass('dPhospi', 'affectation'));

$cmd = dPgetParam( $_GET, 'cmd', '0' );
$id = dPgetParam( $_GET, 'id', '0' );

$sql = "SELECT operations.plageop_id, operations.rank
        FROM operations
        WHERE operations.operation_id = '$id'";
$result = db_loadlist($sql);
$plageop = $result[0]["plageop_id"];
$rank = $result[0]["rank"];

switch($cmd)
{
  case "insert" : {
    $sql = "SELECT operations.rank AS rank, operations.time_operation AS time,
            operations.temp_operation as duree, plagesop.debut AS debut
            FROM operations, plagesop
            WHERE plagesop.id = '$plageop'
            AND operations.plageop_id = plagesop.id
            ORDER BY operations.rank DESC";
    $result = db_loadlist($sql);
    $sql = "UPDATE operations
            SET rank = '".($result[0]["rank"] + 1)."'
            WHERE operations.operation_id = '$id'";
    $exec = db_exec($sql);
    cleanOrderOp($plageop, "rank");
    break;
  }
  case "down" : {
    //On fait monter celui qui est en dessous
    $sql = "SELECT operation_id" .
    		"\nFROM operations" .
    		"\nWHERE operations.plageop_id = '$plageop'" .
    		"\nAND rank = '".($rank + 1)."'";
    $id_temp = db_loadlist($sql);
    $sql = "UPDATE operations
            SET rank = '$rank'
            WHERE operations.operation_id = '".$id_temp[0]["operation_id"]."'";
    $exec = db_exec($sql);
    //On fait descendre celui qu'on a choisit
    $sql = "UPDATE operations
            SET rank = '".($rank + 1)."'
            WHERE operations.operation_id = '$id'";
    $exec = db_exec($sql);
    cleanOrderOp($plageop, "rank");
    break;
  }
  case "up" : {
    //On fait descendre celui qui est au dessus
    $sql = "SELECT operation_id" .
    		"\nFROM operations" .
    		"\nWHERE operations.plageop_id = '$plageop'" .
    		"\nAND rank = '".($rank - 1)."'";
    $id_temp = db_loadlist($sql);
    $sql = "UPDATE operations
            SET rank = '$rank'
            WHERE  operations.operation_id = '".$id_temp[0]["operation_id"]."'";
    $exec = db_exec($sql);
    //On fait monter celui qu'on a choisit
    $sql = "UPDATE operations
            SET rank = '".($rank - 1)."'
            WHERE operations.operation_id = '$id'";
    $exec = db_exec($sql);
    cleanOrderOp($plageop, "rank");
    break;
  }
  case "rm" : {
  	$sql = "UPDATE operations
			SET time_operation = null, rank = 0
			WHERE operations.operation_id = '$id'";
    $result = db_exec($sql);
    cleanOrderOp($plageop, "time");
    changeAffect($id, "rm");
    break;
  }
  case "sethour" : {
    $hour = dPgetParam( $_GET, 'hour', '00' );
    $min = dPgetParam( $_GET, 'min', '00' );
    $sql = "UPDATE operations
			SET pause = '".$hour.":".$min.":00'
			WHERE operations.operation_id = '$id'";
    $result = db_exec($sql);
    cleanOrderOp($plageop, "time");
    break;
  }
  case "setanesth" : {
    $type = dPgetParam( $_GET, 'type', NULL);
    $anesth = dPgetSysVal("AnesthType");
    foreach($anesth as $key => $value) {
      if(trim($value) == $type) {
        $lu = $key;
      }
    }
    if(!isset($lu))
      $lu = NULL;
    $sql = "UPDATE operations
            SET type_anesth = '$lu'
            WHERE operations.operation_id = '$id'";
    $result = db_exec($sql);
    break;
  }
}

//Réarrangement de l'ordre des interventions
function cleanOrderOp($plageop, $type = "rank") {
  switch($type) {
    case "time" :
      $sql = "SELECT operations.operation_id," .
          "\noperations.rank," .
          "\noperations.temp_operation," .
          "\nplagesop.debut" .
          "\nFROM operations" .
          "\nLEFT JOIN plagesop" .
          "\nON plagesop.id = operations.plageop_id" .
          "\nWHERE operations.plageop_id = '$plageop'" .
          "\nAND operations.rank != 0" .
          "\nORDER BY operations.time_operation ASC";
      $result = db_loadlist($sql);
      $i = 1;
      foreach($result as $key => $value) {
        $curr_id = $value["operation_id"];
        $sql = "UPDATE operations" .
            "\nSET operations.rank = '$i'" .
            "\nWHERE operations.operation_id = '$curr_id'";
        db_exec($sql);
        $i++;
      }
    case "rank" :
      $sql = "SELECT operations.operation_id," .
          "\noperations.rank," .
          "\noperations.temp_operation," .
          "\noperations.pause," .
          "\nplagesop.debut" .
          "\nFROM operations" .
          "\nLEFT JOIN plagesop" .
          "\nON plagesop.id = operations.plageop_id" .
          "\nWHERE operations.plageop_id = '$plageop'" .
          "\nAND operations.rank != 0" .
          "\nORDER BY operations.rank ASC";
      $result = db_loadlist($sql);
      $debut = $result[0]["debut"];
      foreach($result as $key => $value) {
        $curr_id = $value["operation_id"];
        $sql = "UPDATE operations" .
            "\nSET operations.time_operation = '$debut'" .
            "\nWHERE operations.operation_id = '$curr_id'";
        db_exec($sql);
        changeAffect($curr_id);
        $debut = mbAddTime($value["temp_operation"], $debut); // durée de l'opération
        $debut = mbAddTime("00:15:00", $debut);               // pause d'1/4h
        $debut = mbAddTime($value["pause"], $debut);          // Pause
      }
      break;
  }
}

// Modification de l'heure de sortie de la dernière affectation
function changeAffect($id, $cmd = null) {
  $operation = new COperation;
  $operation->load($id);
  $operation->loadRefs();
  $affectation =& $operation->_ref_last_affectation;
  if ($affectation->affectation_id && ($operation->type_adm == "ambu")) {
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

$AppUI->redirect("m=$m#$id");
?>