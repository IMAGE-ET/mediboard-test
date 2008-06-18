<?php /* $Id: vw_bloc.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 783 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$deb_personnel  = mbGetValueFromGetOrSession("deb_personnel" , mbDate("-1 WEEK"));
$fin_personnel  = mbGetValueFromGetOrSession("fin_personnel" , mbDate(""));
$prat_personnel = mbGetValueFromGetOrSession("prat_personnel", null);

$user = new CMediusers();
$user->load($AppUI->user_id);
$listPrats = $user->loadPraticiens(PERM_READ);

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
     $curr_plage->_op_for_diree_totale = 0;
     foreach($curr_plage->_ref_operations as $curr_op) {
       // Durées
       if($curr_op->debut_op && $curr_op->fin_op && ($curr_op->debut_op < $curr_op->fin_op)) {
         $curr_plage->_first_op       = min($curr_plage->_first_op, $curr_op->debut_op);
         $curr_plage->_last_op        = max($curr_plage->_last_op, $curr_op->fin_op);
         $duree_op = mbTransformTime(null, mbTimeRelative($curr_op->debut_op, $curr_op->fin_op), "%H hours %M minutes");
         $curr_plage->_duree_total_op = mbTime("+ $duree_op", $curr_plage->_duree_total_op);
         $curr_plage->_op_for_duree_totale++;
       }
       // Personnel
       $curr_plage->loadAffectationsPersonnel();
     }
     $curr_plage->_duree_first_to_last = mbTimeRelative($curr_plage->_first_op, $curr_plage->_last_op);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPrats"     , $listPrats);
$smarty->assign("deb_personnel" , $deb_personnel);
$smarty->assign("fin_personnel" , $fin_personnel);
$smarty->assign("prat_personnel", $prat_personnel);
$smarty->assign("listPlages"    , $listPlages);

$smarty->display("vw_personnel_salle.tpl");

?>