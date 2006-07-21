<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPstats", "tempsOp"));

$codeCCAM   = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));
$prat_id    = mbGetValueFromGetOrSession("prat_id", 0);

$total["nbInterventions"] = 0;
$total["estim_moy"] = 0;
$total["estim_somme"] = 0;
$total["occup_moy"] = 0;
$total["occup_somme"] = 0;
$total["duree_moy"] = 0;
$total["duree_somme"] = 0;


$listTemps = new CTempsOp;

$where = array();
if($prat_id) {
  $where["chir_id"] = "= '$prat_id'";
} elseif(count($listPrats)) {
  $where["chir_id"] = "IN (".implode(",", array_keys($listPrats)).")";
} else {
  $where[] = "0 = 1";
}

if($codeCCAM) {
  $where["ccam"] = "LIKE '%$codeCCAM%'";
}

$ljoin = array();
$ljoin["users"] = "users.user_id = temps_op.chir_id";

$order = "users.user_last_name ASC, users.user_first_name ASC, ccam";

$listTemps = $listTemps->loadList($where, $order, null, null, $ljoin);

foreach($listTemps as $keyTemps => $temps) {
  $listTemps[$keyTemps]->loadRefsFwd();
  $total["nbInterventions"] += $temps->nb_intervention;
  $total["occup_somme"] += $temps->nb_intervention * strtotime($temps->occup_moy);
  $total["duree_somme"] += $temps->nb_intervention * strtotime($temps->duree_moy);
  $total["estim_somme"] += $temps->nb_intervention * strtotime($temps->estimation);
}
$total["occup_moy"] = $total["occup_somme"] / $total["nbInterventions"];
$total["duree_moy"] = $total["duree_somme"] / $total["nbInterventions"];
$total["estim_moy"] = $total["estim_somme"] / $total["nbInterventions"];

?>
