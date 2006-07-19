<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision:  $
* @author Sébastien Fillonneau
*/

$preparation = array();
$result = array();

foreach($listPrats as $_prat) {
  //Récupération des opérations par chirurgien
  
  $sql="SELECT operations.plageop_id,TIME_TO_SEC(entree_bloc) AS sec_entree, " .
  	"\nTIME_TO_SEC(sortie_bloc) AS sec_sortie" .
    "\nFROM operations" .
    "\nINNER JOIN plagesop" .
    "\nON operations.plageop_id = plagesop.id" .
    "\nWHERE operations.chir_id = '$_prat->user_id'" .
    "\nAND annulee = 0" .
    "\nAND entree_bloc IS NOT NULL" .
    "\nAND sortie_bloc IS NOT NULL";
    
  switch($intervalle) {
    case 0:
      $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 month")."' AND '".mbDate()."'";
      break;
    case 1:
      $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-6 month")."' AND '".mbDate()."'";
      break;
    case 2:
      $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 year")."' AND '".mbDate()."'";
      break;
  }
  $sql .= "\nORDER BY plageop_id, sec_entree ASC";

  $operations = db_loadList($sql);

  $old_plagesop = 0;
  $old_operation_id = 0;
    
  foreach($operations as $keyOp => $curr_op) {
      
    if($old_operation_id and $old_plagesop == $curr_op["plageop_id"]) {
      $testValid = (30 * 60) > ($curr_op["sec_entree"] - $operations[$old_operation_id]["sec_sortie"]);
      $testValid = $testValid && ($operations[$old_operation_id]["sec_sortie"] < $curr_op["sec_entree"]);
      if($testValid) {
        $preparation[$_prat->user_id][] = $curr_op["sec_entree"] - $operations[$old_operation_id]["sec_sortie"];
      }
    }
    $old_operation_id = $keyOp;
    $old_plagesop = $curr_op["plageop_id"];
  }
    
  foreach($preparation as $keyPrep => $curr_prep) {
  	$result[$keyPrep]["praticien"] = $listPrats[$keyPrep]->_view;
    $result[$keyPrep]["somme"] = array_sum($curr_prep);
    $result[$keyPrep]["preparation"] = count($curr_prep);
    $result[$keyPrep]["moyenne"] = strftime("%H:%M:%S", array_sum($curr_prep)/count($curr_prep));
  } 
}

?>