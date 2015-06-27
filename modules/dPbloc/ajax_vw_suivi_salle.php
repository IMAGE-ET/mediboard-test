<?php 

/**
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date_suivi = CAppUI::pref("suivisalleAutonome") ? CMbDT::date() : CValue::getOrSession("date", CMbDT::date());
$bloc_id    = CValue::getOrSession("bloc_id");
$page       = CValue::get("page");

$group = CGroups::loadCurrent();

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
if (!$bloc->_id) {
  CAppUI::stepAjax("dPbloc-msg-select_bloc", UI_MSG_WARNING);
  return;
}
$listBlocs = array();

if (CAppUI::conf("dPsalleOp COperation modif_salle")) {
  $listBlocs = $group->loadBlocs(PERM_READ, null, "nom");
  // Chargement de la liste des salles de chaque bloc
  foreach ($listBlocs as $_bloc) {
    $_bloc->loadRefsSalles();
  }
}

// Chargement des Anesthésistes
$anesth      = new CMediusers();
$listAnesths = $anesth->loadAnesthesistes(PERM_READ);

// Chargement des Chirurgiens
$chir      = new CMediusers();
$listChirs = $chir->loadPraticiens(PERM_READ);

$salle = new CSalle();
$where = array("bloc_id" => "='$bloc->_id'");
$bloc->_ref_salles = $salle->loadListWithPerms(PERM_READ, $where, "nom");

$total_salles = count($bloc->_ref_salles);
$page_size = CAppUI::conf("dPbloc mode_presentation salles_count", $group);
$page_count = null;
$current_page = null;

if ($page) {
  $page_count = ceil($total_salles / $page_size);
  $current_page = (($page-1) % $page_count);
  $slice = $current_page * $page_size;
  $bloc->_ref_salles = array_slice($bloc->_ref_salles, $slice, $page_size, true);
}

$systeme_materiel_expert = CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert";

foreach ($bloc->_ref_salles as &$salle) {
  /** @var CSalle $salle */
  $salle->loadRefsForDay($date_suivi);

  if ($systeme_materiel_expert) {
    foreach ($salle->_ref_plages as $_plage) {
      foreach ($_plage->_ref_operations as $_operation) {
        $besoins = $_operation->loadRefsBesoins();
        CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
        foreach ($besoins as $_besoin) {
          $_besoin->loadRefTypeRessource();
        }
      }
    }

    foreach ($salle->_ref_urgences as $_operation) {
      $besoins = $_operation->loadRefsBesoins();
      CMbObject::massLoadFwdRef($besoins, "type_ressource_id");
      foreach ($besoins as $_besoin) {
        $_besoin->loadRefTypeRessource();
      }
    }
  }
}

// Interventions hors plages non traitées
$op = new COperation();
$ljoin = array();
$ljoin["sejour"] = "operations.sejour_id = sejour.sejour_id";
$where = array();
$where["operations.date"]       = "= '$date_suivi'";
$where["operations.salle_id"]   = "IS NULL";
$where["operations.plageop_id"] = "IS NULL";
$where["operations.chir_id"]    = CSQLDataSource::prepareIn(array_keys($listChirs));
$where["sejour.group_id"]       = "= '".$group->_id."'";

/** @var COperation[] $non_traitees */
$non_traitees = $op->loadList($where, null, null, null, $ljoin);

foreach ($non_traitees as $_operation) {
  $_operation->loadRefChir();
  $_operation->loadRefPatient();
  $_operation->loadExtCodesCCAM();
  $_operation->loadRefPlageOp();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"     , true);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listBlocs"      , $listBlocs);
$smarty->assign("bloc"           , $bloc);
$smarty->assign("date_suivi"     , $date_suivi);
$smarty->assign("operation_id"   , 0);
$smarty->assign("non_traitees"   , $non_traitees);
$smarty->assign("page"           , $page);
$smarty->assign("page_count"     , $page_count);
$smarty->assign("current_page"   , $current_page);

$smarty->display("inc_suivi_salles.tpl");
