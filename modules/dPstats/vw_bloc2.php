<?php /* $Id: vw_bloc.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 783 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$deblist = mbGetValueFromGetOrSession("deblist", mbDate("-1 WEEK"));
$finlist = mbDate("+1 DAY", $deblist);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listSalles = new CSalle;
$where["stats"] = "= '1'";
$order = "nom";
$listSalles = $listSalles->loadList($where, $order);

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

// Récupération des plages
$plage = new CPlageOp;
$listPlages = array();
$where = array();
$where["date"] = "BETWEEN '$deblist' AND '$finlist'";
$order = "date, salle_id, debut, chir_id";
$listPlages = $plage->loadList($where, $order);

// Récupération des interventions
foreach($listPlages as $keyPlage => $curr_plage) {
  $listPlages[$keyPlage]->loadRefs(0);
  $listPlages[$keyPlage]->loadRefsFwd();
  $listPlages[$keyPlage]->loadRefsBack(0, "entree_bloc");
  $i = 1;
  foreach($listPlages[$keyPlage]->_ref_operations as $keyOp => $curr_op) {
    $listPlages[$keyPlage]->_ref_operations[$keyOp]->_rank_reel = $i;
    $i++;
    $next = next($listPlages[$keyPlage]->_ref_operations);
    if($next !== false) {
      $listPlages[$keyPlage]->_ref_operations[$keyOp]->_pat_next = $next->entree_bloc;
    } else {
      $listPlages[$keyPlage]->_ref_operations[$keyOp]->_pat_next = null;
    }
    $listPlages[$keyPlage]->_ref_operations[$keyOp]->loadRefs();
    $listPlages[$keyPlage]->_ref_operations[$keyOp]->loadLogs();
    $listPlages[$keyPlage]->_ref_operations[$keyOp]->_ref_sejour->loadRefs();
  }
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("deblist"   , $deblist);
$smarty->assign("listPlages", $listPlages);

$smarty->display("vw_bloc2.tpl");

?>