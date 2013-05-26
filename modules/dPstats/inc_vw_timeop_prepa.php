<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$total["nbPrep"] = 0;
$total["nbPlages"] = 0;
$total["somme"] = 0;
$total["moyenne"] = 0;


$where = array();
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrats));

$ljoin = array();
$ljoin["users"] = "users.user_id = temps_prepa.chir_id";

$order = "users.user_last_name ASC, users.user_first_name ASC";

$listTemps = new CTempsPrepa;
$listTemps = $listTemps->loadList($where, $order, null, null, $ljoin);

foreach ($listTemps as $temps) {
  $temps->loadRefsFwd();
  $total["nbPrep"  ] += $temps->nb_prepa;
  $total["nbPlages"] += $temps->nb_plages;
  $total["somme"   ] += $temps->nb_prepa * strtotime($temps->duree_moy);
}
if ($total["nbPrep"] !=0 ) {
  $total["moyenne"] = $total["somme"] / $total["nbPrep"];
}
