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

$date    = CValue::getOrSession("date", CMbDT::date());
$bloc_id = CValue::getOrSession("bloc_id");

$modif_operation = CCanDo::edit() || $date >= CMbDT::date();
$blocs_list = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id) && count($blocs_list)) {
  $bloc = reset($blocs_list);
}

// Vérification de la check list journalière
$daily_check_lists = array();
$daily_check_list_types = array();
$require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active_salle_reveil") && $date >= CMbDT::date();

if ($require_check_list) {
  list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($bloc, $date);

  if ($check_list_not_validated == 0) {
    $require_check_list = false;
  }
}

$personnels  = array();
$listChirs   = array();
$listAnesths = array();

if ($require_check_list) {
  // Chargement de la liste du personnel pour le reveil
  if (CModule::getActive("dPpersonnel")) {
    $type_personnel = array("reveil");
    if (count($daily_check_list_types)) {
      $type_personnel = array();
      foreach ($daily_check_list_types as $check_list_type) {
        $validateurs = explode("|", $check_list_type->type_validateur);
        foreach ($validateurs as $validateur) {
          $type_personnel[] = $validateur;
        }
      }
    }

    $personnel  = new CPersonnel();
    $personnels = $personnel->loadListPers(array_unique(array_values($type_personnel)));
  }

  $curr_user = CMediusers::get();

  // Chargement des praticiens
  $listChirs = $curr_user->loadPraticiens(PERM_DENY);

  // Chargement des anesths
  $listAnesths = $curr_user->loadAnesthesistes(PERM_DENY);
}

// Création du template
$smarty = new CSmartyDP();

// Daily check lists
$smarty->assign("require_check_list"    , $require_check_list);
$smarty->assign("daily_check_lists"     , $daily_check_lists);
$smarty->assign("daily_check_list_types", $daily_check_list_types);

$smarty->assign("personnels"           , $personnels);
$smarty->assign("date"                 , $date);
$smarty->assign("hour"                 , CMbDT::time());
$smarty->assign("modif_operation"      , $modif_operation);
$smarty->assign("blocs_list"           , $blocs_list);
$smarty->assign("bloc"                 , $bloc);
$smarty->assign("listChirs"            , $listChirs);
$smarty->assign("listAnesths"          , $listAnesths);
$smarty->assign("isImedsInstalled"     , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_reveil.tpl");
