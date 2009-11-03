<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsEdit();

$deb_personnel  = CValue::getOrSession("deb_personnel" , mbDate("-1 WEEK"));
$fin_personnel  = CValue::getOrSession("fin_personnel" , mbDate(""));
$prat_personnel = CValue::getOrSession("prat_personnel", null);

$user = new CMediusers();
$user->load($AppUI->user_id);
$listPrats = $user->loadPraticiens(PERM_READ);
  
$total["duree_prevue"]             = "00:00:00";
$total["days_duree_prevue"]        = 0;
$total["duree_first_to_last"]      = "00:00:00";
$total["days_duree_first_to_last"] = 0;
$total["duree_reelle"]             = "00:00:00";
$total["days_duree_reelle"]        = 0;

// Récupération des plages
$plage = new CPlageOp;
$listPlages = array();
if($prat_personnel) {
  $where = array();
  $where["date"] = "BETWEEN '$deb_personnel' AND '$fin_personnel'";
  $where["chir_id"] = "= '$prat_personnel'";
  $order = "date, salle_id, debut";
  $listPlages = $plage->loadList($where, $order);

  // Récupération des interventions
  foreach($listPlages as &$curr_plage) {
    /*
     * Chargement des intervantions et des éléments suivants :
     * - durée prévue
     * - nombre d'interventions
     * - nombre d'interventions valides
     * - temps des interventions
     * - nombre de panseuses
     * - nombre d'aides op
     */
     $curr_plage->loadRefs(0);
     $curr_plage->_first_op            = "23:59:59";
     $curr_plage->_last_op             = "00:00:00";
     $curr_plage->_duree_total_op      = "00:00:00";
     $curr_plage->_duree_first_to_last = "00:00:00";
     $curr_plage->_op_for_duree_totale = 0;
     foreach($curr_plage->_ref_operations as $curr_op) {
       // Durées
       if($curr_op->debut_op && $curr_op->fin_op && ($curr_op->debut_op < $curr_op->fin_op)) {
         $curr_plage->_first_op       = min($curr_plage->_first_op, $curr_op->debut_op);
         $curr_plage->_last_op        = max($curr_plage->_last_op, $curr_op->fin_op);
         $duree_op = mbTimeRelative($curr_op->debut_op, $curr_op->fin_op);
         $curr_plage->_duree_total_op = mbAddTime($duree_op, $curr_plage->_duree_total_op);
         $curr_plage->_op_for_duree_totale++;
       }
       // Personnel
       $curr_plage->loadAffectationsPersonnel();
     }
     // Totaux
     // Durée prévue
     $newTotalPrevu = mbAddTime($curr_plage->_duree_prevue, $total["duree_prevue"]);
     if($newTotalPrevu < $total["duree_prevue"]) {
       $total["days_duree_prevue"]++;
     }
     $total["duree_prevue"] = $newTotalPrevu;
     // Durée première à la dernière
     if($curr_plage->_first_op && $curr_plage->_last_op && ($curr_plage->_first_op < $curr_plage->_last_op)) {
       $curr_plage->_duree_first_to_last = mbTimeRelative($curr_plage->_first_op, $curr_plage->_last_op);
       
       $newTotalFirstToLast = mbAddTime($curr_plage->_duree_first_to_last, $total["duree_first_to_last"]);
       if($newTotalFirstToLast < $total["duree_first_to_last"]) {
         $total["days_duree_first_to_last"]++;
       }
       $total["duree_first_to_last"] = $newTotalFirstToLast;
     }
     // Durée réèlle
     $newTotalReel = mbAddTime($curr_plage->_duree_total_op, $total["duree_reelle"]);
     if($newTotalReel < $total["duree_reelle"]) {
       $total["days_duree_reelle"]++;
     }
     $total["duree_reelle"] = $newTotalReel;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPrats"     , $listPrats);
$smarty->assign("deb_personnel" , $deb_personnel);
$smarty->assign("fin_personnel" , $fin_personnel);
$smarty->assign("prat_personnel", $prat_personnel);
$smarty->assign("listPlages"    , $listPlages);
$smarty->assign("total"         , $total);

$smarty->display("vw_personnel_salle.tpl");

?>