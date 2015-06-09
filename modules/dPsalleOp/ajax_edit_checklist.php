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

// Récupération des paramètres
$date     = CValue::getOrSession("date", CMbDT::date());
$salle_id = CValue::getOrSession("salle_id");
$bloc_id  = CValue::getOrSession("bloc_id");
$type     = CValue::getOrSession("type", "ouverture_salle");

// Récupération de l'utilisateur courant
$user = CUser::get();
$currUser = new CMediusers();
$currUser->load($user->_id);
$currUser->isAnesth();
$currUser->isPraticien();

$salle = new CSalle();
$salle->load($salle_id);

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);

// Vérification de la check list journalière
$daily_check_lists = array();
$daily_check_list_types = array();
$require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active", CGroups::loadCurrent()) && $date >= CMbDT::date() && !$currUser->_is_praticien;

if ($require_check_list) {
  if ($bloc->_id) {
    list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($bloc, $date, $type);
  }
  else {
    list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($salle, $date, $type);
  }

  if ($check_list_not_validated == 0) {
    $require_check_list = false;
  }
}

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

$type_personnel = array("op", "op_panseuse", "iade", "sagefemme", "manipulateur");
if (count($daily_check_list_types) && $require_check_list) {
  $type_personnel = array();
  foreach ($daily_check_list_types as $check_list_type) {
    $validateurs = explode("|", $check_list_type->type_validateur);
    foreach ($validateurs as $validateur) {
      $type_personnel[] = $validateur;
    }
  }
}
$listValidateurs = CPersonnel::loadListPers(array_unique(array_values($type_personnel)), true, true);
$operateurs_disp_vasc = implode("-", array_merge(CMbArray::pluck($listChirs, "_id"), CMbArray::pluck($listValidateurs, "user_id")));

$nb_op_no_close = 0;
if ($type == "fermeture_salle") {
  $salle->loadRefsForDay($date);

// Calcul du nombre d'actes codé dans les interventions
  if ($salle->_ref_plages) {
    foreach ($salle->_ref_plages as $_plage) {
      foreach ($_plage->_ref_operations as $_operation) {
        if (!$_operation->sortie_salle && !$_operation->annulee) {
          $nb_op_no_close++;
        }
      }
      foreach ($_plage->_unordered_operations as $_operation) {
        if (!$_operation->sortie_salle && !$_operation->annulee) {
          $nb_op_no_close++;
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

// Daily check lists
$smarty->assign("salle"                 , $salle);
$smarty->assign("bloc"                  , $bloc);
$smarty->assign("type"                  , $type);
$smarty->assign("date"                  , $date);
$smarty->assign("nb_op_no_close"        , $nb_op_no_close);
$smarty->assign("require_check_list"    , $require_check_list);
$smarty->assign("daily_check_lists"     , $daily_check_lists);
$smarty->assign("daily_check_list_types", $daily_check_list_types);
$smarty->assign("listValidateurs"       , $listValidateurs);
$smarty->assign("listChirs"             , $listChirs);
$smarty->assign("listAnesths"           , $listAnesths);

$smarty->display("vw_edit_checklist.tpl");