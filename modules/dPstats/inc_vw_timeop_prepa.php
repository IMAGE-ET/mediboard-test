<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision:  $
* @author Sébastien Fillonneau
*/

$preparation = array();
$result = array();

$total["preparation"] = 0;
$total["nbPlage"] = 0;
$total["elements"] = array();

// Vide la table contenant les données
db_exec("TRUNCATE `temps_prepa`"); db_error();

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
  $nb_oper_par_plage = 0;
  $nb_plage[$_prat->user_id] = 0;
  $preparation[$_prat->user_id] = array();
    
  foreach($operations as $keyOp => $curr_op) {
      
    if($old_operation_id and $old_plagesop == $curr_op["plageop_id"]) {
      $testValid = (30 * 60) > ($curr_op["sec_entree"] - $operations[$old_operation_id]["sec_sortie"]);
      $testValid = $testValid && ($operations[$old_operation_id]["sec_sortie"] < $curr_op["sec_entree"]);
      if($testValid) {
        $preparation[$_prat->user_id][] = $curr_op["sec_entree"] - $operations[$old_operation_id]["sec_sortie"];
        $nb_oper_par_plage++;
        if($nb_oper_par_plage==1){
          $nb_plage[$_prat->user_id] = $nb_plage[$_prat->user_id] + 1;  
        }
      }
    }else{
      $nb_oper_par_plage = 0;
    }
    $old_operation_id = $keyOp;
    $old_plagesop = $curr_op["plageop_id"];
  }
  
  if(count($preparation[$_prat->user_id])) {
    $result[$_prat->user_id]["nbPlage"] = $nb_plage[$_prat->user_id];
    $result[$_prat->user_id]["praticien"] = $listPrats[$_prat->user_id]->_view;
    $result[$_prat->user_id]["somme"] = array_sum($preparation[$_prat->user_id]);
    $result[$_prat->user_id]["preparation"] = count($preparation[$_prat->user_id]);
    $result[$_prat->user_id]["moyenne"] = mbMoyenne($preparation[$_prat->user_id]);    
    $result[$_prat->user_id]["ecartType"] = mbEcartType($preparation[$_prat->user_id]);
    
    // Mémorisation des données dans MySQL
    $sql = "INSERT INTO `temps_prepa` (`temp_prepa_id`, `chir_id`, `nb_prepa`, `nb_plages`, `duree_moy`, `duree_ecart`)
            VALUES (NULL, 
            		'$_prat->user_id',
            		'".$result[$_prat->user_id]["preparation"]."',
            		'".$result[$_prat->user_id]["nbPlage"]."',
            		'".strftime("%H:%M:%S",$result[$_prat->user_id]["moyenne"])."',
            		'".strftime("%H:%M:%S",$result[$_prat->user_id]["ecartType"])."');";
	db_exec( $sql ); db_error();
    
    // Mémorisation pour le calcul de la moyenne générale
    $total["elements"] = array_merge($total["elements"], $preparation[$_prat->user_id]);
  }

}

foreach($result as $keyresult => $curr_result){
  $total["preparation"] += $curr_result["preparation"];
  $total["nbPlage"] += $curr_result["nbPlage"];
}

$total["moyenne"] = mbMoyenne($total["elements"]);
$total["ecartType"] = mbEcartType($total["elements"]);

?>