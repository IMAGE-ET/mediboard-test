<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date  = CValue::getOrSession("date", CMbDT::date());
$operation_id = CValue::getOrSession("operation_id");
$hide_finished = CValue::getOrSession("hide_finished", 0);
$praticien_id = CValue::getOrSession("praticien_id");

// Chargement de l'utilisateur courant
$user = CMediusers::get();

if (!$praticien_id && $user->isPraticien() && !$user->isAnesth()) {
  $praticien_id = $user->user_id;
}

// Selection des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

// Chargement des chirurgiens ayant une intervention ce jour
$listPermPrats = $user->loadPraticiens(PERM_READ);
$listPrats  = array();
$operation = new COperation();
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$where = array();
$where[] = "operations.date = '$date' OR plagesop.date = '$date'";
$where["annulee"] = "= '0'";
$groupby = "operations.chir_id";
$opsJour = $operation->loadList($where, null, null, $groupby, $ljoin);
foreach ($opsJour as $curr_op) {
  if (array_key_exists($curr_op->chir_id, $listPermPrats)) {
    $listPrats[$curr_op->chir_id] = $listPermPrats[$curr_op->chir_id];
  }
}
$listPrats = CMbArray::pluck($listPrats, "_view");
asort($listPrats);

// Selection des plages opératoires de la journée
$praticien = new CMediusers;
if ($praticien->load($praticien_id)) {
  $praticien->loadRefsForDay($date); 
  foreach ($praticien->_ref_plages as $plage) {
    $plage->loadRefsNotes();
  }
}

if ($hide_finished == 1 && $praticien->_ref_plages) {
  foreach ($praticien->_ref_plages as $plage) {
    foreach ($plage->_ref_operations as $key => $op) {
      if ($op->sortie_salle) {
        unset($plage->_ref_operations[$key]);
      }
    }
    foreach ($plage->_unordered_operations as $key => $op) {
      if ($op->sortie_salle) {
        unset($plage->_unordered_operations[$key]);
      }
    }
  }

  foreach ($praticien->_ref_deplacees as $key => $op) {
    if ($op->sortie_salle) {
      unset($praticien->_ref_deplacees[$key]);
    }
  }

  foreach ($praticien->_ref_urgences as $key => $op) {
    if ($op->sortie_salle) {
      unset($praticien->_ref_urgences[$key]);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("hide_finished" , $hide_finished);
$smarty->assign("praticien"     , $praticien   );
$smarty->assign("salle"         , new CSalle   );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listPrats"     , $listPrats   );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_op_prat.tpl");
