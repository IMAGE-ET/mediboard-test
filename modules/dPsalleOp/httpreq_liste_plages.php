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
$salle_id      = CValue::getOrSession("salle");
$bloc_id       = CValue::getOrSession("bloc_id");
$date          = CValue::getOrSession("date", CMbDT::date());
$operation_id  = CValue::getOrSession("operation_id");
$hide_finished = CValue::getOrSession("hide_finished", 0);

// Récuperation du service par défaut dans les préférences utilisateur
$group_id = CGroups::loadCurrent()->_id;
$default_salles_id = CAppUI::pref("default_salles_id");
// Récuperation de la salle à afficher par défaut
$default_salle_id = "";
$default_salles_id = json_decode($default_salles_id);
if (isset($default_salles_id->{"g$group_id"})) {
  $default_salle_id = reset(explode("|", $default_salles_id->{"g$group_id"}));
}

if (!$salle_id) {
  $salle_id = $default_salle_id;
}
// Chargement des praticiens
$mediuser    = new CMediusers();
$listAnesths = $mediuser->loadAnesthesistes(PERM_READ);

// Selection des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

// Selection des plages opératoires de la journée
$salle = new CSalle();
if ($salle->load($salle_id)) {
  $salle->loadRefsForDay($date, true);
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
  $date_last_checklist = CDailyCheckList::getDateLastChecklist($salle, "ouverture_salle");
}

// Checklist_fermeture bloc
$date_close_checklist = null;
$currUser = CMediusers::get();
$require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active", $group_id) && $date >= CMbDT::date() && !$currUser->isPraticien();

if ($require_check_list) {
  list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($salle, $date, "fermeture_salle");

  if ($check_list_not_validated == 0) {
    $require_check_list = false;
  }
  $date_close_checklist = CDailyCheckList::getDateLastChecklist($salle, "fermeture_salle");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("default_salle_id", $default_salle_id);
$smarty->assign("group_id"      , $group_id);
$smarty->assign("vueReduite"    , false);
$smarty->assign("salle"         , $salle);
$smarty->assign("hide_finished" , $hide_finished);
$smarty->assign("praticien_id"  , null);
$smarty->assign("listBlocs"     , $listBlocs);
$smarty->assign("listAnesths"   , $listAnesths);
$smarty->assign("date"          , $date);
$smarty->assign("operation_id"  , $operation_id);
$smarty->assign("date_last_checklist", $date_last_checklist);
$smarty->assign("require_check_list_close", $require_check_list);
$smarty->assign("date_close_checklist", $date_close_checklist);

$smarty->display("inc_liste_plages.tpl");
