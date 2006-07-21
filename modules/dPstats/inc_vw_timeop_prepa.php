<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPstats", "tempsPrepa"));

$total["nbPrep"] = 0;
$total["nbPlages"] = 0;
$total["somme"] = 0;
$total["moyenne"] = 0;

$listTemps = new CTempsPrepa;

$where = array();
if(count($listPrats)) {
  $where["chir_id"] = "IN (".implode(",", array_keys($listPrats)).")";
} else {
  $where[] = "0 = 1";
}

$ljoin = array();
$ljoin["users"] = "users.user_id = temps_prepa.chir_id";

$order = "users.user_last_name ASC, users.user_first_name ASC";

$listTemps = $listTemps->loadList($where, $order, null, null, $ljoin);

foreach($listTemps as $keyTemps => $temps) {
  $listTemps[$keyTemps]->loadRefsFwd();
  $total["nbPrep"] += $temps->nb_prepa;
  $total["nbPlages"] += $temps->nb_plages;
  $total["somme"] += $temps->nb_prepa * strtotime($temps->duree_moy);
}

$total["moyenne"] = $total["somme"] / $total["nbPrep"];
?>
