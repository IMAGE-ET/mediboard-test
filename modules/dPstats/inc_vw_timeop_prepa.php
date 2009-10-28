<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$total["nbPrep"] = 0;
$total["nbPlages"] = 0;
$total["somme"] = 0;
$total["moyenne"] = 0;

$listTemps = new CTempsPrepa;

$where = array();
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrats));

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
if($total["nbPrep"]!=0){
  $total["moyenne"] = $total["somme"] / $total["nbPrep"];
}
?>
