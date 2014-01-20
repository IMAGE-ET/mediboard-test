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

$salle_id     = CValue::getOrSession("salle");
$bloc_id      = CValue::getOrSession("bloc_id");
$date         = CValue::getOrSession("date", CMbDT::date());
$operation_id = CValue::getOrSession("operation_id");
$hide_finished = CValue::getOrSession("hide_finished", 0);

// Chargement des praticiens
$mediuser    = new CMediusers();
$listAnesths = $mediuser->loadAnesthesistes(PERM_READ);

// Selection des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

// Selection des plages opératoires de la journée
$salle = new CSalle();
if ($salle->load($salle_id)) {
  $salle->loadRefsForDay($date);
}

if ($hide_finished == 1 && $salle->_ref_plages) {
  foreach ($salle->_ref_plages as $_plage) {
    foreach ($_plage->_ref_operations as $_key => $_op) {
      if ($_op->sortie_salle) {
        unset($_plage->_ref_operations[$_key]);
      }
    }
    foreach ($_plage->_unordered_operations as $_key => $_op) {
      if ($_op->sortie_salle) {
        unset($_plage->_unordered_operations[$_key]);
      }
    }
  }

  foreach ($salle->_ref_deplacees as $_key => $_op) {
    if ($_op->sortie_salle) {
      unset($salle->_ref_deplacees[$_key]);
    }
  }

  foreach ($salle->_ref_urgences as $_key => $_op) {
    if ($_op->sortie_salle) {
      unset($salle->_ref_urgences[$_key]);
    }
  }
}

// Calcul du nombre d'actes codé dans les interventions
if ($salle->_ref_plages) {
  foreach ($salle->_ref_plages as $_plage) {
    foreach ($_plage->_ref_operations as $_operation) {
      $_operation->countActes();
    }
    foreach ($_plage->_unordered_operations as $_operation) {
      $_operation->countActes();
    }
  }
}
if ($salle->_ref_deplacees) {
  foreach ($salle->_ref_deplacees as $_operation) {
    $_operation->countActes();
  }
}
if ($salle->_ref_urgences) {
  foreach ($salle->_ref_urgences as $_operation) {
    $_operation->countActes();
  }
}

$date_last_checklist = null;
if ($salle->cheklist_man) {
  $checklist = new CDailyCheckList();
  $checklist->object_class = $salle->_class;
  $checklist->object_id = $salle->_id;
  $checklist->loadMatchingObject("date DESC");
  if ($checklist->_id) {
    $log = new CUserLog();
    $log->object_id     = $checklist->_id;
    $log->object_class  = $checklist->_class;
    $log->loadMatchingObject("date DESC");
    $date_last_checklist = $log->date;
  }
  else {
    $date_last_checklist = $checklist->date;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("salle"         , $salle       );
$smarty->assign("hide_finished" , $hide_finished);
$smarty->assign("praticien_id"  , null         );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listAnesths"   , $listAnesths );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);
$smarty->assign("date_last_checklist"  , $date_last_checklist);

$smarty->display("inc_liste_plages.tpl");
